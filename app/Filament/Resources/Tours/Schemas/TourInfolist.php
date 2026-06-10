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
            TextEntry::make('title'),
            TextEntry::make('slug'),
            TextEntry::make('category')->placeholder('-'),
            TextEntry::make('short_description')->placeholder('-')->columnSpanFull(),
            TextEntry::make('description')->placeholder('-')->columnSpanFull(),
            TextEntry::make('hero_video_url')->placeholder('-')->columnSpanFull(),
            TextEntry::make('duration')->placeholder('-'),
            TextEntry::make('location')->placeholder('-'),
            TextEntry::make('pickup_note')->placeholder('-'),
            TextEntry::make('experience_type')->placeholder('-'),
            TextEntry::make('transfer_option')->placeholder('-'),
            TextEntry::make('booking_type')->placeholder('-'),
            TextEntry::make('price_from')->label('Adult price from')->money('AED')->placeholder('-'),
            TextEntry::make('child_price_from')->label('Kid price from')->money('AED')->placeholder('-'),
            TextEntry::make('currency')->placeholder('-'),
            self::arrayEntry('highlights')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('inclusions')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('exclusions')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('important_notices')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('expectation_steps')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('best_for')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('faqs')->placeholder('-')->columnSpanFull(),
            TextEntry::make('cancellation_policy')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('preferred_time_options')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('tour_options')->label('Tour language choices')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('booking_options')->label('Priced booking options')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('gallery_videos')->placeholder('-')->columnSpanFull(),
            IconEntry::make('is_featured')->boolean(),
            IconEntry::make('is_private')->boolean(),
            IconEntry::make('is_active')->boolean(),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
        ]);
    }

    private static function arrayEntry(string $name): TextEntry
    {
        return TextEntry::make($name)
            ->formatStateUsing(fn ($state) => self::formatArrayState($state));
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
