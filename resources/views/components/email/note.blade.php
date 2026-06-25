@props([
    'tone' => 'default',
])

@php
    $styles = [
        'default' => 'background:#f7f9fd;border-color:#e4e8f0;color:#34405f;',
        'gold' => 'background:#fff8eb;border-color:#f0d7a7;color:#5c4218;',
    ];
@endphp

<div style="{{ $styles[$tone] ?? $styles['default'] }}border-width:1px;border-style:solid;border-radius:14px;padding:14px 16px;margin:18px 0;font-size:14px;line-height:1.6;">
    {{ $slot }}
</div>
