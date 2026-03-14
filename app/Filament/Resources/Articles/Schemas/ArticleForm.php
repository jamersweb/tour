<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Article Details')
                ->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                    TextInput::make('category')->required()->maxLength(80),
                    TextInput::make('read_time')->numeric()->required()->default(5)->suffix('min'),
                    Textarea::make('excerpt')->required()->rows(3)->maxLength(280)->columnSpanFull(),
                    Textarea::make('content')->required()->rows(12)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Media')
                ->schema([
                    FileUpload::make('hero_image_path')
                        ->label('Hero image')
                        ->image()
                        ->disk('public')
                        ->directory('journal')
                        ->imageEditor(),
                ]),
            Section::make('Publishing')
                ->schema([
                    DateTimePicker::make('published_at'),
                    TextInput::make('sort_order')->numeric()->required()->default(0),
                    Toggle::make('is_featured')->required()->inline(false),
                    Toggle::make('is_published')->required()->inline(false),
                ])
                ->columns(2),
            Section::make('SEO')
                ->schema([
                    TextInput::make('seo_title')->maxLength(255),
                    Textarea::make('seo_description')->rows(3)->maxLength(320)->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
