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
                TextEntry::make('pickup_note')
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
