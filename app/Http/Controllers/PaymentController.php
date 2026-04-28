<?php
namespace App\Http\Controllers;

use App\Mail\PaymentSuccessfulMail;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

class PaymentController extends Controller
{
    public function checkout($bookingId)
    {
        $booking = Booking::with(['service', 'child', 'schedule', 'vaccine', 'transaction'])
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        if (in_array($booking->status, ['declined', 'canceled'], true)) {
            $message = $booking->status === 'declined'
                ? 'Booking ini ditolak oleh bidan.'
                : 'Booking ini sudah dibatalkan.';

            return redirect('/bookings')->with('error', $message);
        }

        return view('pages.booking.checkout', [
            'booking' => $booking,
            'snapToken' => null,
            'snapClientKey' => $this->midtransClientKey(),
            'transaction' => $booking->transaction,
        ]);
    }

    public function pay($bookingId)
    {
        $this->configureMidtrans();

        $booking = Booking::with('service')
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        if ($booking->status === 'paid') {
            return redirect('/bookings')->with('success', 'Booking ini sudah dibayar.');
        }

        if (in_array($booking->status, ['declined', 'canceled'], true)) {
            $message = $booking->status === 'declined'
                ? 'Tidak dapat membayar. Booking ditolak oleh bidan.'
                : 'Tidak dapat membayar. Booking sudah dibatalkan.';

            return redirect('/bookings')->with('error', $message);
        }

        $orderId = $this->generateOrderId((int) $booking->id);

        $transaction = Transaction::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'payment_gateway' => 'midtrans',
                'payment_method' => null,
                'reference_id' => $orderId,
                'amount' => $booking->service->price,
                'status' => 'pending',
                'paid_at' => null,
            ]
        );

        $snapPayload = [
            'transaction_details' => [
                'order_id' => $transaction->reference_id,
                'gross_amount' => (int) round((float) $transaction->amount),
            ],
            'customer_details' => [
                'first_name' => $booking->user?->name ?? 'Pelanggan',
                'email' => $booking->user?->email,
                'phone' => $booking->user?->phone ?? null,
            ],
            'item_details' => [
                [
                    'id' => (string) $booking->service->id,
                    'price' => (int) round((float) $booking->service->price),
                    'quantity' => 1,
                    'name' => mb_substr((string) $booking->service->name, 0, 50),
                ],
            ],
            'callbacks' => [
                'finish' => route('payment.finish'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($snapPayload);
        } catch (\Throwable $e) {
            Log::error('Midtrans snap token generation failed', [
                'booking_id' => $booking->id,
                'transaction_id' => $transaction->id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('checkout', $booking->id)
                ->with('error', 'Gagal membuat transaksi Midtrans. Coba lagi beberapa saat.');
        }

        $booking->load(['child', 'schedule', 'vaccine']);

        return view('pages.booking.checkout', [
            'booking' => $booking,
            'snapToken' => $snapToken,
            'snapClientKey' => $this->midtransClientKey(),
            'transaction' => $transaction,
        ]);
    }

    public function finish(Request $request)
    {
        $orderId = (string) $request->query('order_id', '');

        $transaction = Transaction::with('booking')
            ->where('reference_id', $orderId)
            ->first();

        if (!$transaction || !$transaction->booking || $transaction->booking->user_id !== Auth::id()) {
            return redirect('/bookings')->with('error', 'Transaksi tidak ditemukan.');
        }

        try {
            $this->syncTransactionStatusFromMidtrans($transaction);
        } catch (\Throwable $e) {
            Log::warning('Failed to sync transaction status on finish callback', [
                'transaction_id' => $transaction->id,
                'reference_id' => $transaction->reference_id,
                'message' => $e->getMessage(),
            ]);
        }

        return redirect()->route('payment.success', $transaction->id);
    }

    public function success($transactionId)
    {
        $transaction = Transaction::with('booking')->findOrFail($transactionId);

        if ($transaction->booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('pages.booking.success', [
            'transaction' => $transaction,
            'booking' => $transaction->booking,
            'isPaid' => $transaction->status === 'settlement',
        ]);
    }

    public function recheck($transactionId)
    {
        $transaction = Transaction::with('booking')
            ->findOrFail($transactionId);

        if (!$transaction->booking || $transaction->booking->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $this->configureMidtrans();
            $this->syncTransactionStatusFromMidtrans($transaction);
        } catch (\Throwable $e) {
            Log::warning('Manual payment recheck failed', [
                'transaction_id' => $transaction->id,
                'reference_id' => $transaction->reference_id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('payment.success', $transaction->id)
                ->with('error', 'Gagal cek status pembayaran. Coba lagi beberapa saat.');
        }

        $latest = $transaction->fresh();

        if ($latest?->status === 'settlement') {
            return redirect()->route('payment.success', $transaction->id)
                ->with('success', 'Pembayaran sudah terkonfirmasi.');
        }

        return redirect()->route('payment.success', $transaction->id)
            ->with('info', 'Pembayaran masih diproses.');
    }

    public function notification(Request $request): JsonResponse
    {
        $this->configureMidtrans();

        $payload = $request->all();

        if (!$this->isValidSignature($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        if ($orderId === '') {
            return response()->json(['message' => 'order_id is required'], 422);
        }

        $transaction = Transaction::with('booking.user')
            ->where('reference_id', $orderId)
            ->first();

        if (!$transaction) {
            Log::warning('Midtrans notification received for unknown order', [
                'order_id' => $orderId,
                'payload' => $payload,
            ]);

            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $this->syncTransactionStatusFromPayload($transaction, $payload);

        return response()->json(['message' => 'OK']);
    }

    private function configureMidtrans(): void
    {
        $serverKey = $this->midtransServerKey();

        if ($serverKey === '') {
            throw new \RuntimeException('MIDTRANS_SERVER_KEY belum terkonfigurasi.');
        }

        Config::$serverKey = $serverKey;
        Config::$isProduction = $this->midtransIsProduction();
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    private function generateOrderId(int $bookingId): string
    {
        return 'BOOKING-' . $bookingId . '-' . now()->format('YmdHis') . '-' . strtoupper(substr(md5((string) microtime(true)), 0, 6));
    }

    private function syncTransactionStatusFromMidtrans(Transaction $transaction): void
    {
        $statusResponse = MidtransTransaction::status($transaction->reference_id);
        $payload = json_decode(json_encode($statusResponse), true) ?: [];

        $this->syncTransactionStatusFromPayload($transaction, $payload);
    }

    private function syncTransactionStatusFromPayload(Transaction $transaction, array $payload): void
    {
        $transactionStatus = strtolower((string) ($payload['transaction_status'] ?? ''));
        $fraudStatus = strtolower((string) ($payload['fraud_status'] ?? ''));

        $normalizedStatus = match ($transactionStatus) {
            'capture' => $fraudStatus === 'challenge' ? 'pending' : 'settlement',
            'settlement' => 'settlement',
            'pending' => 'pending',
            'deny', 'cancel', 'expire', 'failure', 'refund', 'partial_refund', 'chargeback' => 'failed',
            default => $transaction->status,
        };

        $wasPaid = $transaction->status === 'settlement' || $transaction->booking?->status === 'paid';

        $transaction->update([
            'payment_gateway' => 'midtrans',
            'payment_method' => $payload['payment_type'] ?? $transaction->payment_method,
            'status' => $normalizedStatus,
            'paid_at' => $normalizedStatus === 'settlement'
                ? ($transaction->paid_at ?? now())
                : $transaction->paid_at,
        ]);

        $booking = $transaction->booking;
        if ($booking) {
            if ($normalizedStatus === 'settlement') {
                $booking->update(['status' => 'paid']);
            } elseif ($normalizedStatus === 'failed' && $booking->status !== 'paid') {
                $booking->update(['status' => 'pending']);
            }

            if (!$wasPaid && $normalizedStatus === 'settlement' && $booking->user?->email) {
                Mail::to($booking->user->email)->send(new PaymentSuccessfulMail($booking, $transaction->fresh()));
            }
        }
    }

    private function isValidSignature(array $payload): bool
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signature = (string) ($payload['signature_key'] ?? '');
        $serverKey = $this->midtransServerKey();

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signature === '' || $serverKey === '') {
            return false;
        }

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $signature);
    }

    private function midtransServerKey(): string
    {
        return (string) (config('services.midtrans.server_key') ?: env('MIDTRANS_SERVER_KEY', ''));
    }

    private function midtransClientKey(): string
    {
        return (string) (config('services.midtrans.client_key') ?: env('MIDTRANS_CLIENT_KEY', ''));
    }

    private function midtransIsProduction(): bool
    {
        $configValue = config('services.midtrans.is_production');
        if ($configValue !== null) {
            return (bool) $configValue;
        }

        return filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOL);
    }
}
