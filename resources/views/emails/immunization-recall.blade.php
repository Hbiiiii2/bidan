<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Imunisasi</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,.08);">
            <div style="background:linear-gradient(135deg,#0f766e,#14b8a6);padding:28px 32px;color:#fff;">
                <h1 style="margin:0;font-size:24px;line-height:1.3;">Pengingat imunisasi untuk {{ $child->name }}</h1>
                <p style="margin:10px 0 0;font-size:14px;opacity:.95;">Jadwal berikutnya sudah mendekat. Mohon siapkan kunjungan ke layanan bidan.</p>
            </div>
            <div style="padding:32px;">
                <p style="margin:0 0 18px;">Halo orang tua {{ $child->user->name }}, berikut pengingat imunisasi untuk anak Anda.</p>

                <table style="width:100%;border-collapse:collapse;margin:0 0 24px;">
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;width:170px;">Anak</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $child->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Vaksin</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $vaccineName }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Tahap Berikutnya</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $nextDose }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Tanggal Recall</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $dueDate->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Jarak Interval</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $intervalDays }} hari</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#6b7280;">Imunisasi Terakhir</td>
                        <td style="padding:10px 0;font-weight:700;">{{ $latestImmunization->date?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                </table>

                <div style="text-align:center;margin:28px 0 8px;">
                    <a href="{{ url('/child/' . $child->id . '/status') }}" style="display:inline-block;background:#0f766e;color:#fff;text-decoration:none;padding:12px 22px;border-radius:999px;font-weight:700;">Lihat Status Imunisasi</a>
                </div>

                <p style="margin:20px 0 0;font-size:13px;color:#6b7280;">Mohon datang sesuai jadwal agar imunisasi anak tetap lengkap.</p>
            </div>
        </div>
    </div>
</body>
</html>
