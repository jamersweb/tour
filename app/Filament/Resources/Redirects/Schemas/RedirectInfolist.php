<?php

namespace App\Filament\Resources\Redirects\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RedirectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('source_path'),
            TextEntry::make('destination_url'),
            TextEntry::make('status_code'),
            IconEntry::make('is_active')->boolean(),
            TextEntry::make('match_hits')->numeric(),
            TextEntry::make('last_matched_at')->dateTime()->placeholder('-'),
            TextEntry::make('updated_at')->dateTime()->placeholder('-'),
        ]);
    }
}
