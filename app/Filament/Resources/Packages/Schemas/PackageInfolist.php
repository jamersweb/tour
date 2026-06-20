<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PackageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('title'),
            TextEntry::make('slug'),
            TextEntry::make('short_description')->columnSpanFull(),
            TextEntry::make('description')->columnSpanFull(),
            TextEntry::make('hero_video_url')->placeholder('-')->columnSpanFull(),
            TextEntry::make('duration')->placeholder('-'),
            TextEntry::make('location')->placeholder('-'),
            TextEntry::make('days')->numeric()->placeholder('-'),
            TextEntry::make('nights')->numeric()->placeholder('-'),
            TextEntry::make('price_from')->money('AED')->placeholder('-'),
            TextEntry::make('sale_price')->money('AED')->placeholder('-'),
            self::arrayEntry('best_for')->label('Best fit')->placeholder('-')->columnSpanFull(),
            TextEntry::make('cancellation_policy')->placeholder('-')->columnSpanFull(),
            self::arrayEntry('important_notices')->label('Before you go')->placeholder('-')->columnSpanFull(),
            IconEntry::make('is_featured')->boolean(),
            IconEntry::make('is_active')->boolean(),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
            TextEntry::make('gallery_videos')->placeholder('-')->columnSpanFull(),
        ]);
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
