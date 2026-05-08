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
            IconEntry::make('is_featured')->boolean(),
            IconEntry::make('is_active')->boolean(),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
            TextEntry::make('gallery_videos')->placeholder('-')->columnSpanFull(),
        ]);
    }
}
