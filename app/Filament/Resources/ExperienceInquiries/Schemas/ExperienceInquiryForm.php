<?php

namespace App\Filament\Resources\ExperienceInquiries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceInquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lead Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('phone')
                            ->tel()
                            ->disabled(fn (string $operation): bool => $operation === 'create'),
                        DatePicker::make('travel_date')
                            ->disabled(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('guest_count')
                            ->numeric()
                            ->disabled(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('interest')
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('experience_title')
                            ->label('Experience')
                            ->disabled(),
                        Textarea::make('message')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->disabled(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(2),
                Section::make('Pipeline')
                    ->schema([
                        TextInput::make('source')
                            ->required()
                            ->disabled(),
                        TextInput::make('source_url')
                            ->label('Source URL')
                            ->disabled()
                            ->columnSpanFull(),
                        Select::make('status')
                            ->required()
                            ->options([
                                'new' => 'New',
                                'contacted' => 'Contacted',
                                'quoted' => 'Quoted',
                                'won' => 'Won',
                                'lost' => 'Lost',
                            ]),
                        DateTimePicker::make('contacted_at'),
                        DateTimePicker::make('follow_up_at'),
                        Textarea::make('admin_notes')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
