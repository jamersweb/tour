<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FaqInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('question')->columnSpanFull(),
            TextEntry::make('category'),
            TextEntry::make('sort_order')->numeric(),
            TextEntry::make('answer')->columnSpanFull(),
            IconEntry::make('is_featured')->boolean(),
            IconEntry::make('is_published')->boolean(),
            TextEntry::make('created_at')->dateTime()->placeholder('-'),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
        ]);
    }
}
