<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ArticleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('title'),
            TextEntry::make('slug'),
            TextEntry::make('blogCategory.name')->label('Category'),
            TextEntry::make('excerpt')->columnSpanFull(),
            TextEntry::make('content')->columnSpanFull(),
            TextEntry::make('read_time')->suffix(' min'),
            TextEntry::make('published_at')->dateTime()->placeholder('-'),
            TextEntry::make('sort_order')->numeric(),
            IconEntry::make('is_featured')->boolean(),
            IconEntry::make('is_published')->boolean(),
            TextEntry::make('created_at')->dateTime()->placeholder('-'),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
        ]);
    }
}
