@props([
    'href',
])

<table role="presentation" cellspacing="0" cellpadding="0" style="margin:22px 0 8px 0;">
    <tr>
        <td style="border-radius:999px;background:#061a63;">
            <a href="{{ $href }}" style="display:inline-block;padding:13px 22px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:800;letter-spacing:.01em;">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>
