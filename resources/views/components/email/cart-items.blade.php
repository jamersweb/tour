@props([
    'items' => [],
])

@if (is_array($items) && $items !== [])
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;margin:20px 0;border:1px solid #e4e8f0;border-radius:14px;overflow:hidden;">
        <thead>
            <tr>
                <th align="left" style="background:#f7f9fd;color:#061a63;border-bottom:1px solid #e4e8f0;padding:11px 10px;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">Item</th>
                <th align="left" style="background:#f7f9fd;color:#061a63;border-bottom:1px solid #e4e8f0;padding:11px 10px;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">Date</th>
                <th align="left" style="background:#f7f9fd;color:#061a63;border-bottom:1px solid #e4e8f0;padding:11px 10px;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">Guests</th>
                <th align="right" style="background:#f7f9fd;color:#061a63;border-bottom:1px solid #e4e8f0;padding:11px 10px;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td style="border-bottom:1px solid #edf0f5;padding:11px 10px;color:#17213f;font-size:13px;font-weight:700;">{{ $item['title'] ?? 'Cart item' }}</td>
                    <td style="border-bottom:1px solid #edf0f5;padding:11px 10px;color:#596175;font-size:13px;">{{ $item['travelDate'] ?? 'To be confirmed' }}</td>
                    <td style="border-bottom:1px solid #edf0f5;padding:11px 10px;color:#596175;font-size:13px;">{{ $item['guestCount'] ?? 1 }}</td>
                    <td align="right" style="border-bottom:1px solid #edf0f5;padding:11px 10px;color:#17213f;font-size:13px;font-weight:800;">{{ $item['lineTotalFormatted'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
