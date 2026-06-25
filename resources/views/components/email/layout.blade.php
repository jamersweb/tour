@props([
    'title' => 'Acute Tourism',
    'preheader' => '',
    'eyebrow' => null,
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f1eb;color:#17213f;font-family:Arial,Helvetica,sans-serif;line-height:1.6;">
    @if ($preheader)
        <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
            {{ $preheader }}
        </div>
    @endif

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;background:#f4f1eb;margin:0;padding:0;">
        <tr>
            <td align="center" style="padding:32px 14px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;max-width:680px;margin:0 auto;border-collapse:collapse;">
                    <tr>
                        <td style="padding:0 0 14px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="left" style="padding:0;">
                                        <img src="{{ url('/images/acute-tourism-logo.png') }}" width="168" height="59" alt="Acute Tourism" style="display:block;width:168px;max-width:168px;height:auto;border:0;">
                                    </td>
                                    <td align="right" style="padding:0;color:#6c7286;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">
                                        Dubai, UAE
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#ffffff;border:1px solid #e2dccf;border-radius:18px;overflow:hidden;box-shadow:0 18px 45px rgba(6,26,99,.10);">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <tr>
                                    <td style="height:8px;background:#061a63;font-size:0;line-height:0;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding:34px 34px 28px 34px;">
                                        @if ($eyebrow)
                                            <div style="display:inline-block;margin:0 0 14px 0;padding:5px 10px;border-radius:999px;background:#fff4df;color:#8d6320;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">
                                                {{ $eyebrow }}
                                            </div>
                                        @endif

                                        {{ $slot }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:18px 22px 0 22px;color:#6c7286;font-size:12px;line-height:1.6;">
                            Acute Tourism LLC<br>
                            Curated Dubai tours, holiday packages, visas, and travel support.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
