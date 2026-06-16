<?php

namespace App\Filament\Resources\Collections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CollectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('collection_group', ['location', 'activity']))
            ->columns([
                TextColumn::make('name')
                    ->label('Subcategory')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('collection_group')
                    ->label('Parent category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'location' ? 'By Location' : 'By Activity Type')
                    ->sortable(),
                TextColumn::make('summary')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('experiences_count')
                    ->counts('experiences')
                    ->label('Experiences')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Shown on menu')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('collection_group')
                    ->label('Parent category')
                    ->options([
                        'location' => 'By Location',
                        'activity' => 'By Activity Type',
                    ]),
                TernaryFilter::make('is_featured')
                    ->label('Shown on menu'),
            ])
            ->groups([
                Group::make('collection_group')
                    ->label('Parent category')
                    ->getTitleFromRecordUsing(fn ($record): string => $record->collection_group === 'location'
                        ? 'By Location'
                        : 'By Activity Type')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])
            ->defaultGroup('collection_group')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
