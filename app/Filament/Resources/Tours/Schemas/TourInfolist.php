<?php

namespace App\Filament\Resources\Tours\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TourInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            self::textEntry('title'),
            self::textEntry('slug'),
            self::textEntry('category')->placeholder('-'),
            self::textEntry('short_description')->placeholder('-')->columnSpanFull(),
            self::textEntry('description')->placeholder('-')->columnSpanFull(),
            self::textEntry('hero_video_url')->placeholder('-')->columnSpanFull(),
            self::textEntry('duration')->placeholder('-'),
            self::textEntry('location')->placeholder('-'),
            self::textEntry('pickup_note')->placeholder('-'),
            self::textEntry('experience_type')->placeholder('-'),
            self::textEntry('transfer_option')->placeholder('-'),
            self::textEntry('booking_type')->placeholder('-'),
            TextEntry::make('price_from')->label('Adult price from')->money('AED')->placeholder('-'),
            TextEntry::make('child_price_from')->label('Kid price from')->money('AED')->placeholder('-'),
            self::textEntry('currency')->placeholder('-'),
            self::arrayEntry('highlights')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('inclusions')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('exclusions')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('important_notices')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('expectation_steps')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('best_for')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('faqs')->placeholder('-')->columnSpanFull(),
            self::textEntry('cancellation_policy')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('preferred_time_options')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('tour_options')->label('Tour language choices')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('booking_options')->label('Priced booking options')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('unavailable_dates')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('unavailable_periods')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('gallery_images')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('gallery_videos')->placeholder('-')->columnSpanFull(),
            IconEntry::make('is_featured')->boolean(),
            IconEntry::make('is_private')->boolean(),
            IconEntry::make('is_active')->boolean(),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
        ]);
    }

    private static function textEntry(string $name): TextEntry
    {
        return TextEntry::make($name)
            ->state(fn ($record) => self::formatArrayState($record->{$name} ?? null));
    }

    private static function arrayEntry(string $name): TextEntry
    {
        return TextEntry::make($name)
            ->state(fn ($record) => self::formatArrayState($record->{$name} ?? null));
    }

    private static function formatArrayState($state): ?string
    {
        if ($state === null || $state === []) {
            return null;
        }

        if (! is_array($state)) {
            return (string) $state;
        }

        if (array_is_list($state) && collect($state)->every(fn ($item) => is_scalar($item) || $item === null)) {
            return collect($state)->filter(fn ($item) => filled($item))->implode(', ');
        }

        return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: null;
    }
}
