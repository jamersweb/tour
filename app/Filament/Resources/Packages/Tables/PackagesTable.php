<?php

namespace App\Filament\Resources\Packages\Tables;

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

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('slug')->searchable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('short_description')->limit(45)->searchable(),
            TextColumn::make('location')->searchable(),
            TextColumn::make('collections.name')
                ->label('Categories')
                ->badge()
                ->separator(',')
                ->toggleable(),
            TextColumn::make('duration'),
            TextColumn::make('price_from')->money('AED')->sortable()->placeholder('-'),
            IconColumn::make('is_featured')->boolean(),
            IconColumn::make('is_active')->boolean(),
            TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('collections')
                ->label('Package category')
                ->relationship('collections', 'name', fn ($query) => $query->where('collection_group', 'package')->orderBy('sort_order')->orderBy('name'))
                ->multiple()
                ->preload(),
            TernaryFilter::make('is_featured'),
            TernaryFilter::make('is_active'),
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
