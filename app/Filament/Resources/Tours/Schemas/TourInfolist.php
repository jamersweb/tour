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
            TextEntry::make('price_from')->money('AED')->placeholder('-'),
            TextEntry::make('currency')->placeholder('-'),
            TextEntry::make('highlights')->placeholder('-')->columnSpanFull(),
            TextEntry::make('inclusions')->placeholder('-')->columnSpanFull(),
            TextEntry::make('exclusions')->placeholder('-')->columnSpanFull(),
            TextEntry::make('important_notices')->placeholder('-')->columnSpanFull(),
            TextEntry::make('expectation_steps')->placeholder('-')->columnSpanFull(),
            TextEntry::make('best_for')->placeholder('-')->columnSpanFull(),
            TextEntry::make('faqs')->placeholder('-')->columnSpanFull(),
            TextEntry::make('cancellation_policy')->placeholder('-')->columnSpanFull(),
            TextEntry::make('gallery_videos')->placeholder('-')->columnSpanFull(),
            IconEntry::make('is_featured')->boolean(),
            IconEntry::make('is_private')->boolean(),
            IconEntry::make('is_active')->boolean(),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
        ]);
    }
}
