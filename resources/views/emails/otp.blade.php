<!DOCTYPE html>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP LaperPoll</title>
</head>
<body style="margin:0;padding:0;background-color:#F4F5F7;font-family:Arial,Helvetica,sans-serif;">

```
<!-- Preheader -->
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
    Kode OTP reset password akun LaperPoll Anda. Berlaku selama 10 menit.
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F4F5F7;padding:30px 15px;">
    <tr>
        <td align="center">

            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;background:#ffffff;border-radius:20px;overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td align="center" style="background-color:#172D23;padding:30px;">

                        <img src="https://yourdomain.com/assets/images/logo.png"
                             alt="LaperPoll"
                             width="150"
                             style="display:block;border:0;outline:none;text-decoration:none;margin-bottom:10px;">

                        <div style="font-size:12px;font-weight:bold;color:rgba(255,255,255,0.6);letter-spacing:2px;text-transform:uppercase;">
                            Reset Password
                        </div>

                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:40px 35px;">

                        <h1 style="margin:0 0 15px 0;font-size:24px;color:#172D23;">
                            Halo, {{ $user->name }}
                        </h1>

                        <p style="margin:0 0 25px 0;font-size:15px;line-height:1.8;color:#555656;">
                            Kami menerima permintaan untuk mereset password akun LaperPoll Anda.
                            Gunakan kode OTP berikut untuk melanjutkan proses reset password.
                        </p>

                        <div style="font-size:11px;font-weight:bold;color:#B7B8B9;letter-spacing:2px;text-transform:uppercase;margin-bottom:12px;">
                            Kode OTP Anda
                        </div>

                        <!-- OTP Box -->
                        <table role="presentation"
                               width="100%"
                               cellpadding="0"
                               cellspacing="0"
                               border="0"
                               style="background:#F4F5F7;border-radius:16px;padding:20px;">

                            <tr>
                                <td align="center">

                                    <table role="presentation" cellpadding="5" cellspacing="0" border="0">
                                        <tr>
                                            @foreach (str_split($otp) as $digit)
                                            <td align="center"
                                                style="width:45px;height:55px;background:#172D23;border-radius:8px;color:#ffffff;font-size:24px;font-weight:bold;">
                                                {{ $digit }}
                                            </td>
                                            @endforeach
                                        </tr>
                                    </table>

                                    <p style="margin-top:15px;font-size:13px;color:#888888;">
                                        Kode ini berlaku selama
                                        <strong style="color:#172D23;">10 menit</strong>
                                    </p>

                                </td>
                            </tr>

                        </table>

                        <!-- Warning -->
                        <table role="presentation"
                               width="100%"
                               cellpadding="0"
                               cellspacing="0"
                               border="0"
                               style="margin-top:25px;background:#FFF8E7;border-left:4px solid #F5A623;">

                            <tr>
                                <td style="padding:15px;">

                                    <p style="margin:0;font-size:13px;line-height:1.8;color:#7A5C00;">
                                        <strong>Penting:</strong><br>
                                        Jangan bagikan kode OTP ini kepada siapa pun.
                                        Tim LaperPoll tidak pernah meminta kode OTP Anda.
                                        Jika Anda tidak merasa melakukan permintaan reset password,
                                        abaikan email ini.
                                    </p>

                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center"
                        style="background:#E8EAE9;padding:25px;">

                        <p style="margin:0;font-size:12px;line-height:1.8;color:#666666;">
                            © {{ date('Y') }} LaperPoll<br>
                            Email ini dikirim secara otomatis untuk keperluan keamanan akun.<br>
                            Mohon tidak membalas email ini.
                        </p>

                        <p style="margin-top:10px;font-size:12px;">
                            <a href="{{ config('app.url') }}"
                               style="color:#172D23;font-weight:bold;text-decoration:none;">
                                Kunjungi LaperPoll
                            </a>
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>
```

</body>
</html>
