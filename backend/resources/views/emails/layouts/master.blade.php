<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name', 'ZipZapGo'))</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td align="center" style="background-color: {{ env('APP_THEME_COLOR', '#E41F07') }}; padding: 20px;">
                            <img src="{{ isset($message) ? $message->embed(public_path('assets/img/logo.png')) : asset('assets/img/logo.png') }}" alt="{{ config('app.name', 'ZipZapGo') }} Logo" style="max-height: 50px; display: block; margin-bottom: 10px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">{{ config('app.name', 'ZipZapGo') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="background-color: #f1f1f1; padding: 15px; font-size: 12px; color: #888;">
                            &copy; {{ date('Y') }} {{ config('app.name', 'ZipZapGo') }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
