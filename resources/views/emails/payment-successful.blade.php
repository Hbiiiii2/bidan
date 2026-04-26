<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,.08);">
            <div style="background:linear-gradient(135deg,#16a34a,#15803d);padding:28px 32px;color:#fff;">
                <h1 style="margin:0;font-size:24px;line-height:1.3;">Pembayaran berhasil</h1>
                <p style="margin:10px 0 0;font-size:14px;opacity:.95;">Booking layanan kamu sudah terkonfirmasi.</p>
            </div>
            <div style="padding:32px;">
                <p style="margin:0 0 18px;">Halo {{ $booking->user->name }}, pembayaran untuk booking kamu sudah berhasil diproses.</p>

                <table style="width:100%;border-collapse:collapse;margin:0 0 24px;">
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;width:170px;">Layanan</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $booking->service->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Anak</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $booking->child->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Tanggal</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $booking->schedule?->date?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Nominal</td>
                        <td style="padding:10px 0;font-weight:700;">Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Referensi</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $transaction->reference_id ?? $transaction->id }}</td>
                    </tr>
                </table>

                <div style="text-align:center;margin:28px 0 8px;">
                    <a href="{{ url('/bookings') }}" style="display:inline-block;background:#16a34a;color:#fff;text-decoration:none;padding:12px 22px;border-radius:999px;font-weight:700;">Lihat Riwayat Booking</a>
                </div>

                <p style="margin:20px 0 0;font-size:13px;color:#6b7280;">Simpan email ini sebagai bukti pembayaran dan informasi booking.</p>
            </div>
        </div>
    </div>
</body>
</html>
