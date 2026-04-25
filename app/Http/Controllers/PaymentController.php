<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function checkout($bookingId)
    {
        $booking = Booking::with(['service', 'child', 'schedule'])
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        if (in_array($booking->status, ['declined', 'canceled'], true)) {
            $message = $booking->status === 'declined'
                ? 'Booking ini ditolak oleh bidan.'
                : 'Booking ini sudah dibatalkan.';

            return redirect('/bookings')->with('error', $message);
        }

        return view('pages.booking.checkout', compact('booking'));
    }

    public function pay($bookingId)
    {
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

        $transaction = Transaction::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->service->price,
                'status' => 'pending',
            ]
        );

        // SIMULASI PAYMENT SUCCESS
        return redirect()->route('payment.success', $transaction->id);
    }

    public function success($transactionId)
    {
        $transaction = Transaction::with('booking')->findOrFail($transactionId);

        if ($transaction->booking->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->update([
            'status'  => 'settlement',
            'paid_at' => now(),
        ]);

        $booking = $transaction->booking;
        $booking->update(['status' => 'paid']);

        return view('pages.booking.success');
    }
}
