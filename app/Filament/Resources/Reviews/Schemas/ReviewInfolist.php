<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('reviewable_type')->label('Content Type')->formatStateUsing(fn (string $state) => class_basename($state)),
            TextEntry::make('reviewable.title')->label('Content Item')->placeholder('-'),
            TextEntry::make('author_name'),
            TextEntry::make('rating')->suffix(' stars'),
            TextEntry::make('title')->placeholder('-'),
            TextEntry::make('tag')->placeholder('-'),
            TextEntry::make('source')->placeholder('-'),
            TextEntry::make('sort_order')->numeric(),
            TextEntry::make('quote')->columnSpanFull(),
            IconEntry::make('is_featured')->boolean(),
            IconEntry::make('is_published')->boolean(),
            TextEntry::make('reviewed_at')->dateTime()->placeholder('-'),
            TextEntry::make('created_at')->dateTime()->placeholder('-'),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
        ]);
    }
}
