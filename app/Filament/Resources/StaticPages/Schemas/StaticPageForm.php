<?php

namespace App\Filament\Resources\StaticPages\Schemas;

use App\Filament\Support\MediaUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StaticPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page')
                ->schema([
                    TextInput::make('admin_title')->required()->maxLength(255),
                    TextInput::make('page_key')->required()->maxLength(120)->unique(ignoreRecord: true),
                    TextInput::make('route_uri')->maxLength(255),
                    Toggle::make('is_active')->required()->inline(false)->default(true),
                ])
                ->columns(2),
            Section::make('SEO')
                ->schema([
                    TextInput::make('seo_title')->maxLength(255),
                    Textarea::make('seo_description')->rows(3)->maxLength(320)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Hero')
                ->schema([
                    TextInput::make('hero_eyebrow')->maxLength(120),
                    TextInput::make('primary_cta_label')->maxLength(80),
                    TextInput::make('primary_cta_url')->maxLength(255),
                    TextInput::make('secondary_cta_label')->maxLength(80),
                    TextInput::make('secondary_cta_url')->maxLength(255),
                    Textarea::make('hero_title')->rows(2)->columnSpanFull(),
                    Textarea::make('hero_description')->rows(4)->columnSpanFull(),
                    FileUpload::make('hero_image_path')
                        ->label('Hero image upload')
                        ->image()
                        ->disk('uploads')
                        ->directory('static-pages/heroes')
                        ->formatStateUsing(fn ($state) => MediaUpload::formatState($state))
                        ->dehydrateStateUsing(fn ($state, $record) => MediaUpload::dehydrateState($state, $record?->hero_image_path))
                        ->imageEditor(),
                    TextInput::make('hero_image_url')->label('Hero image URL')->url()->maxLength(255),
                ])
                ->columns(3),
            Section::make('Sidebar / Highlight Card')
                ->schema([
                    TextInput::make('sidebar_label')->maxLength(120),
                    Textarea::make('sidebar_title')->rows(2)->columnSpanFull(),
                    Textarea::make('sidebar_body')->rows(3)->columnSpanFull(),
                    TagsInput::make('sidebar_items')->placeholder('Add checklist item')->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Metrics')
                ->schema([
                    Repeater::make('metrics')
                        ->schema([
                            TextInput::make('value')->required()->maxLength(80),
                            TextInput::make('label')->required()->maxLength(160),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
            Section::make('Content Sections')
                ->description('Use cards for grouped content. Use list items for policy/checklist sections.')
                ->schema([
                    Repeater::make('content_sections')
                        ->schema([
                            TextInput::make('eyebrow')->maxLength(120),
                            TextInput::make('label')->maxLength(120),
                            Textarea::make('title')->rows(2)->columnSpanFull(),
                            Textarea::make('body')->rows(4)->columnSpanFull(),
                            TagsInput::make('items')->placeholder('Add list item')->columnSpanFull(),
                            Repeater::make('cards')
                                ->schema([
                                    TextInput::make('label')->maxLength(120),
                                    TextInput::make('title')->required()->maxLength(180),
                                    Textarea::make('body')->rows(3)->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
            Section::make('FAQs')
                ->schema([
                    Repeater::make('faqs')
                        ->schema([
                            TextInput::make('question')->required()->maxLength(255),
                            Textarea::make('answer')->required()->rows(3)->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
