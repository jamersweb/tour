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
                TextEntry::make('highlights')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('inclusions')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('exclusions')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('important_notices')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('expectation_steps')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('best_for')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('faqs')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('cancellation_policy')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('preferred_time_options')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('tour_options')
                    ->label('Tour language choices')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('booking_options')
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
                TextEntry::make('gallery_videos')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
