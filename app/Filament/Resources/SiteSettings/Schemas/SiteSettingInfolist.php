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
            TextEntry::make('contact_phone')->label('Primary phone'),
            TextEntry::make('contact_phone_secondary')->label('Secondary phone'),
            TextEntry::make('whatsapp_number'),
            TextEntry::make('license_number')->label('DTCM / DED license number')->placeholder('Not provided'),
            TextEntry::make('google_reviews_url')->label('Google reviews URL')->placeholder('Not provided'),
            TextEntry::make('tripadvisor_reviews_url')->label('Tripadvisor reviews URL')->placeholder('Not provided'),
            TextEntry::make('contact_address')->columnSpanFull(),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }
}
