<?php

namespace App\Filament\Resources\Collections\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CollectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('collection_group')
                    ->label('Parent category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'location' => 'By Location',
                        'package' => 'Package Category',
                        default => 'By Activity Type',
                    }),
                TextEntry::make('summary')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('sort_order')
                    ->numeric(),
                IconEntry::make('is_featured')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
