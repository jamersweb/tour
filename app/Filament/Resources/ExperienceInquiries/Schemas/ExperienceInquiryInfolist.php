<?php

namespace App\Filament\Resources\ExperienceInquiries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExperienceInquiryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('travel_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('guest_count')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('interest'),
                TextEntry::make('experience_title')
                    ->label('Experience')
                    ->placeholder('-'),
                TextEntry::make('message')
                    ->columnSpanFull(),
                TextEntry::make('source'),
                TextEntry::make('source_url')
                    ->label('Source URL')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('contacted_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('follow_up_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('admin_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
