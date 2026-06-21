<?php

namespace App\Filament\Resources\PackageCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PackageCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('slug'),
            TextEntry::make('summary')->placeholder('-'),
            TextEntry::make('description')->placeholder('-')->columnSpanFull(),
            TextEntry::make('packages_count')->counts('packages')->label('Packages')->numeric(),
            TextEntry::make('sort_order')->numeric(),
            IconEntry::make('is_featured')->label('Shown as public filter')->boolean(),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
        ]);
    }
}
