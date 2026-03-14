<?php

namespace App\Filament\Resources\Redirects\Schemas;

use App\Models\Redirect;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class RedirectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Redirect Rule')
                ->schema([
                    TextInput::make('source_path')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->afterStateUpdated(fn (?string $state, Set $set) => $set('source_path', Redirect::normalizePath($state ?? '/')))
                        ->formatStateUsing(fn (?string $state) => Redirect::normalizePath($state ?? '/')),
                    TextInput::make('destination_url')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Use a relative path like /experiences or a full external URL.'),
                    Select::make('status_code')
                        ->required()
                        ->options([
                            301 => '301 Permanent',
                            302 => '302 Temporary',
                        ])
                        ->default(301),
                    Toggle::make('is_active')
                        ->required()
                        ->inline(false)
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
