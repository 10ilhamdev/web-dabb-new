<!DOCTYPE html>
<html style="margin: 0; padding: 0;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Update Status Pendaftaran Kunjungan</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header dengan Logo ANRI -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #0579CB 0%, #034a8a 100%); padding: 30px; text-align: center;">
                            <img src="{{ $message->embed(public_path('image/logo_anri.png')) }}"
                                alt="Logo ANRI" width="80" style="margin-bottom: 15px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Depot Arsip
                                Berkelanjutan Bandung</h1>
                            <p style="color: #cce4f7; margin: 10px 0 0 0; font-size: 14px;">Lembaga Kearsipan Nasional
                                Republik Indonesia</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px;">
                            <h2 style="color: #333333; margin: 0 0 20px 0; font-size: 22px;">Status Pendaftaran Kunjungan</h2>

                            <p style="color: #555555; font-size: 16px; line-height: 1.8; margin: 0 0 25px 0;">
                                Yth. {{ $name }},
                            </p>

                            <p style="color: #555555; font-size: 16px; line-height: 1.8; margin: 0 0 25px 0;">
                                Permohonan pendaftaran kunjungan Anda ke Depot Arsip Berkelanjutan Bandung saat ini telah diproses dengan status:
                            </p>

                            <!-- Badge Status -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
                                <tr>
                                    <td align="center">
                                        @if($status === 'approved')
                                            <span style="display: inline-block; background-color: #d1fae5; color: #065f46; padding: 12px 35px; border-radius: 50px; font-size: 18px; font-weight: bold; border: 1px solid #a7f3d0;">
                                                DISETUJUI
                                            </span>
                                        @else
                                            <span style="display: inline-block; background-color: #fee2e2; color: #991b1b; padding: 12px 35px; border-radius: 50px; font-size: 18px; font-weight: bold; border: 1px solid #fca5a5;">
                                                DITOLAK
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Detail Kunjungan -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                                <h3 style="margin-top: 0; color: #1e293b; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Detail Kunjungan</h3>
                                <table width="100%" cellpadding="4" cellspacing="0" style="font-size: 14px; color: #475569;">
                                    <tr>
                                        <td width="40%" style="font-weight: 600;">Tanggal Kunjungan</td>
                                        <td width="60%">: {{ $visitDate }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Waktu / Sesi</td>
                                        <td>: {{ $visitTime }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;">Jumlah Peserta</td>
                                        <td>: {{ $visitorCount }} Orang</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Keterangan dari Admin (jika ada) -->
                            @if(!empty($keterangan))
                                <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #f59e0b;">
                                    <h3 style="margin-top: 0; color: #92400e; font-size: 15px; font-weight: bold; margin-bottom: 8px;">Catatan / Keterangan Tambahan:</h3>
                                    <p style="margin: 0; font-size: 14px; color: #78350f; line-height: 1.6; white-space: pre-wrap;">{{ $keterangan }}</p>
                                </div>
                            @endif

                            <p style="color: #555555; font-size: 14px; line-height: 1.6; margin: 25px 0 0 0;">
                                Hormat kami,<br>
                                <strong>Tim Depot Arsip Berkelanjutan Bandung</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 0;">
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9f9f9; padding: 25px 40px; text-align: center;">
                            <p style="color: #888888; font-size: 12px; margin: 0 0 10px 0;">
                                Depot Arsip Berkelanjutan Bandung<br>
                                Jl. Raya Derwati, Mekarjaya, Kec. Rancasari, Kota Bandung, Jawa Barat 40292<br>
                                Telp: (021) 7805851 | Email: info@anri.go.id
                            </p>
                            <p style="color: #aaaaaa; font-size: 11px; margin: 0;">
                                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
