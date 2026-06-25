@props([
    'title' => 'Details',
])

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border-collapse:collapse;background:#fbfcff;border:1px solid #e4e8f0;border-radius:14px;overflow:hidden;">
    <tr>
        <td style="padding:14px 16px;background:#f7f9fd;color:#061a63;font-size:13px;font-weight:900;letter-spacing:.04em;text-transform:uppercase;">
            {{ $title }}
        </td>
    </tr>
    <tr>
        <td style="padding:4px 16px 10px 16px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                {{ $slot }}
            </table>
        </td>
    </tr>
</table>
