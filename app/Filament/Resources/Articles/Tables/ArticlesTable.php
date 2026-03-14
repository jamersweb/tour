<?php

namespace App\Filament\Resources\Articles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('category')->badge()->searchable(),
            TextColumn::make('excerpt')->limit(45)->searchable(),
            TextColumn::make('read_time')->suffix(' min')->sortable(),
            TextColumn::make('published_at')->dateTime()->sortable(),
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
