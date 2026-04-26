<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,.08);">
            <div style="background:linear-gradient(135deg,#db2777,#be185d);padding:28px 32px;color:#fff;">
                <h1 style="margin:0;font-size:24px;line-height:1.3;">Booking berhasil dibuat</h1>
                <p style="margin:10px 0 0;font-size:14px;opacity:.95;">Silakan lanjutkan pembayaran untuk mengunci jadwal layanan.</p>
            </div>
            <div style="padding:32px;">
                <p style="margin:0 0 18px;">Halo {{ $booking->user->name }}, booking layanan kamu sudah berhasil dibuat.</p>

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
                        <td style="padding:10px 0;color:#6b7280;">Jam</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $booking->schedule?->start_time ?? '-' }} - {{ $booking->schedule?->end_time ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Bidan</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $booking->schedule?->midwife?->name ?? '-' }}</td>
                    </tr>
                </table>

                <div style="text-align:center;margin:28px 0 8px;">
                    <a href="{{ url('/checkout/' . $booking->id) }}" style="display:inline-block;background:#db2777;color:#fff;text-decoration:none;padding:12px 22px;border-radius:999px;font-weight:700;">Lanjut ke Pembayaran</a>
                </div>

                <p style="margin:20px 0 0;font-size:13px;color:#6b7280;">Jika tombol tidak berfungsi, buka halaman booking kamu lalu lanjutkan ke checkout secara manual.</p>
            </div>
        </div>
    </div>
</body>
</html>
