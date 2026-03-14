<?php

namespace App\Filament\Resources\Faqs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('question')->searchable()->limit(60),
            TextColumn::make('category')->badge()->searchable(),
            TextColumn::make('answer')->limit(50)->searchable(),
            TextColumn::make('sort_order')->numeric()->sortable(),
            IconColumn::make('is_featured')->boolean(),
            IconColumn::make('is_published')->boolean(),
            TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            TernaryFilter::make('is_featured'),
            TernaryFilter::make('is_published'),
        ])->recordActions([
            ViewAction::make(),
            EditAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
