<?php

namespace App\Filament\Resources\StaticPages\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StaticPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admin_title')->searchable()->sortable(),
                TextColumn::make('page_key')->searchable()->sortable(),
                TextColumn::make('route_uri')->searchable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('admin_title')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
