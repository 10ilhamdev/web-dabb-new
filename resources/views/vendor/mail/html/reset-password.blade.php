<!DOCTYPE html>
<html style="margin: 0; padding: 0;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Reset Password</title>
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
                            <h2 style="color: #333333; margin: 0 0 20px 0; font-size: 22px;">Reset Password
                            </h2>

                            <p style="color: #555555; font-size: 16px; line-height: 1.8; margin: 0 0 25px 0;">
                                Yth. Pengguna,
                            </p>

                            <p style="color: #555555; font-size: 16px; line-height: 1.8; margin: 0 0 25px 0;">
                                Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda di <strong>Depot Arsip Berkelanjutan Bandung</strong>.
                            </p>

                            <!-- Tombol Reset Password -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}"
                                            style="display: inline-block; background: linear-gradient(135deg, #0579CB 0%, #034a8a 100%); color: #ffffff; padding: 16px 40px; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(5, 121, 203, 0.3);">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #777777; font-size: 14px; line-height: 1.6; margin: 25px 0;">
                                Link reset password ini akan kadaluarsa dalam {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} menit.
                            </p>

                            <p style="color: #777777; font-size: 14px; line-height: 1.6; margin: 25px 0;">
                                Jika Anda tidak melakukan permintaan reset password, Anda dapat mengabaikan email ini dan tidak ada perubahan yang akan dilakukan pada akun Anda.
                            </p>

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