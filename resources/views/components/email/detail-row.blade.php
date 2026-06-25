@props([
    'label',
    'value' => null,
])

@if ($value !== null && $value !== '')
    <tr>
        <td style="padding:9px 0;border-bottom:1px solid #edf0f5;color:#6c7286;font-size:13px;font-weight:700;vertical-align:top;width:38%;">
            {{ $label }}
        </td>
        <td style="padding:9px 0;border-bottom:1px solid #edf0f5;color:#17213f;font-size:14px;font-weight:700;vertical-align:top;">
            {{ $value }}
        </td>
    </tr>
@endif
