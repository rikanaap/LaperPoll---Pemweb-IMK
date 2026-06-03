<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP LaperPoll</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background-color: #F4F5F7;
            padding: 32px 16px;
        }

        .wrapper {
            max-width: 480px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background-color: #172D23;
            border-radius: 20px 20px 0 0;
            padding: 28px 32px;
            text-align: center;
        }

        .header img {
            height: 36px;
            margin-bottom: 8px;
        }

        .header p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Body */
        .body {
            background-color: #ffffff;
            padding: 36px 32px;
        }

        .greeting {
            font-size: 22px;
            font-weight: 800;
            color: #172D23;
            margin-bottom: 12px;
        }

        .desc {
            font-size: 14px;
            color: #555656;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        /* OTP Box */
        .otp-label {
            font-size: 11px;
            font-weight: 700;
            color: #B7B8B9;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .otp-box {
            background-color: #F4F5F7;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            margin-bottom: 28px;
        }

        .otp-digits {
            display: inline-flex;
            gap: 10px;
            justify-content: center;
        }

        .otp-digit {
            display: inline-block;
            width: 44px;
            height: 52px;
            background-color: #172D23;
            color: #ffffff;
            border-radius: 10px;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
            line-height: 52px;
        }

        .otp-expire {
            margin-top: 14px;
            font-size: 12px;
            color: #B7B8B9;
            font-weight: 600;
        }

        .otp-expire span {
            color: #172D23;
        }

        /* Warning */
        .warning {
            background-color: #FFF8E7;
            border-left: 4px solid #F5A623;
            border-radius: 0 12px 12px 0;
            padding: 14px 16px;
            margin-bottom: 28px;
        }

        .warning p {
            font-size: 12px;
            color: #7A5C00;
            line-height: 1.6;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            background-color: #E8EAE9;
            border-radius: 0 0 20px 20px;
            padding: 24px 32px;
            text-align: center;
        }

        .footer p {
            font-size: 12px;
            color: #B7B8B9;
            line-height: 1.8;
        }

        .footer a {
            color: #172D23;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="header">
            <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="LaperPoll">
            <p>Reset Password</p>
        </div>

        {{-- Body --}}
        <div class="body">
            <p class="greeting">Halo, {{ $user->name }}! 👋</p>
            <p class="desc">
                Kami menerima permintaan untuk mereset password akun LaperPoll kamu.
                Gunakan kode OTP di bawah ini untuk melanjutkan.
            </p>

            <p class="otp-label">Kode OTP kamu</p>
            <div class="otp-box">
                <div class="otp-digits">
                    @foreach (str_split($otp) as $digit)
                    <span class="otp-digit">{{ $digit }}</span>
                    @endforeach
                </div>
                <p class="otp-expire">
                    Kode ini berlaku selama <span>10 menit</span>
                </p>
            </div>

            <div class="warning">
                <p>
                    ⚠️ Jangan bagikan kode ini kepada siapapun.
                    Tim LaperPoll tidak pernah meminta kode OTP kamu.
                    Jika kamu tidak merasa meminta reset password, abaikan email ini.
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>
                Email ini dikirim otomatis oleh <a href="{{ config('app.url') }}">LaperPoll</a>.<br>
                Mohon tidak membalas email ini.
            </p>
        </div>

    </div>
</body>

</html>