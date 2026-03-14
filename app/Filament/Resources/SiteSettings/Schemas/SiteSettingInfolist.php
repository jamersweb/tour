<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiteSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('site_name'),
            TextEntry::make('site_tagline')->columnSpanFull(),
            TextEntry::make('contact_email'),
            TextEntry::make('contact_phone'),
            TextEntry::make('whatsapp_number'),
            TextEntry::make('contact_address')->columnSpanFull(),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }
}
