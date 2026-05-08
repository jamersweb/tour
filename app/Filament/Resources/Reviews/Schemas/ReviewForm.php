<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Models\Experience;
use App\Models\Package;
use App\Models\Tour;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Review Details')
                ->schema([
                    Select::make('reviewable_type')
                        ->label('Content Type')
                        ->options([
                            Experience::class => 'Experience',
                            Package::class => 'Package',
                            Tour::class => 'Tour',
                        ])
                        ->required()
                        ->live(),
                    Select::make('reviewable_id')
                        ->label('Content Item')
                        ->options(fn (Get $get): array => match ($get('reviewable_type')) {
                            Experience::class => Experience::query()->orderBy('title')->pluck('title', 'id')->all(),
                            Package::class => Package::query()->orderBy('title')->pluck('title', 'id')->all(),
                            Tour::class => Tour::query()->orderBy('title')->pluck('title', 'id')->all(),
                            default => [],
                        })
                        ->searchable()
                        ->required(),
                    TextInput::make('author_name')->required()->maxLength(120),
                    Select::make('rating')
                        ->options([
                            5 => '5 stars',
                            4 => '4 stars',
                            3 => '3 stars',
                            2 => '2 stars',
                            1 => '1 star',
                        ])
                        ->required()
                        ->default(5),
                    TextInput::make('title')->maxLength(160),
                    TextInput::make('tag')->maxLength(120),
                    TextInput::make('source')->default('Website')->required()->maxLength(80),
                    TextInput::make('sort_order')->numeric()->default(0)->required(),
                    DateTimePicker::make('reviewed_at')->seconds(false),
                    Textarea::make('quote')->required()->rows(5)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Publishing')
                ->schema([
                    Toggle::make('is_featured')->required()->inline(false),
                    Toggle::make('is_published')->required()->inline(false)->default(true),
                ])
                ->columns(2),
        ]);
    }
}
