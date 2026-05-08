<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('author_name')->searchable(),
            TextColumn::make('reviewable_type')
                ->label('Type')
                ->formatStateUsing(fn (string $state) => class_basename($state))
                ->badge(),
            TextColumn::make('reviewable.title')->label('Content')->searchable(),
            TextColumn::make('rating')->suffix(' stars')->sortable(),
            TextColumn::make('tag')->limit(24),
            IconColumn::make('is_featured')->boolean(),
            IconColumn::make('is_published')->boolean(),
            TextColumn::make('reviewed_at')->dateTime()->sortable()->placeholder('-'),
            TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('reviewable_type')
                ->label('Content Type')
                ->options([
                    \App\Models\Experience::class => 'Experience',
                    \App\Models\Package::class => 'Package',
                    \App\Models\Tour::class => 'Tour',
                ]),
            TernaryFilter::make('is_featured'),
            TernaryFilter::make('is_published'),
        ])->recordActions([
            ViewAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
