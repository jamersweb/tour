<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('FAQ Details')
                ->schema([
                    TextInput::make('question')->required()->maxLength(255)->columnSpanFull(),
                    TextInput::make('category')->required()->maxLength(80),
                    TextInput::make('sort_order')->numeric()->required()->default(0),
                    Textarea::make('answer')->required()->rows(5)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Publishing')
                ->schema([
                    Toggle::make('is_featured')->required()->inline(false),
                    Toggle::make('is_published')->required()->inline(false),
                ])
                ->columns(2),
        ]);
    }
}
