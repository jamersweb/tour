<?php

namespace App\Filament\Resources\Experiences\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExperienceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('category'),
                TextEntry::make('short_description')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('duration')
                    ->placeholder('-'),
                TextEntry::make('location')
                    ->placeholder('-'),
                TextEntry::make('price_from')
                    ->label('Adult price from')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('child_price_from')
                    ->label('Kid price from')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('currency'),
                TextEntry::make('tag')
                    ->placeholder('-'),
                IconEntry::make('is_featured')
                    ->boolean(),
                IconEntry::make('is_private')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('hero_summary')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('hero_video_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('highlights')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('inclusions')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('exclusions')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('important_notices')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('expectation_steps')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('best_for')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('faqs')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('cancellation_policy')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('preferred_time_options')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('tour_options')
                    ->label('Tour language choices')
                    ->placeholder('-')
                    ->columnSpanFull(),
                self::arrayEntry('booking_options')
                    ->label('Priced booking options')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('pickup_note')
                    ->placeholder('-'),
                TextEntry::make('experience_type')
                    ->placeholder('-'),
                TextEntry::make('transfer_option')
                    ->placeholder('-'),
                TextEntry::make('booking_type')
                    ->placeholder('-'),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('seo_title')
                    ->placeholder('-'),
                TextEntry::make('seo_description')
                    ->placeholder('-'),
                self::arrayEntry('gallery_videos')
                    ->placeholder('-')
                    ->columnSpanFull(),
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
