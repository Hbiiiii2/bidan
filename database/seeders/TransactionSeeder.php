<?php
namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $booking = Booking::where('status', 'paid')->first();

        if (! $booking) {
            return;
        }

        Transaction::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'payment_method'  => 'bank_transfer',
                'payment_gateway' => 'midtrans',
                'reference_id'    => 'TRX-' . now()->format('YmdHis'),
                'amount'          => $booking->service->price ?? 0,
                'status'          => 'settlement',
                'paid_at'         => now(),
            ]
        );
    }
}
