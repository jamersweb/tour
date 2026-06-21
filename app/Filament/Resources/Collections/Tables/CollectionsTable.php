<?php

namespace App\Filament\Resources\Collections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CollectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Subcategories')
            ->description('Create child subcategories under By Location, By Activity Type, or Package Category, assign content, and control website visibility.')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('collection_group', ['location', 'activity', 'package']))
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
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'location' => 'By Location',
                        'package' => 'Package Category',
                        default => 'By Activity Type',
                    })
                    ->sortable(),
                TextColumn::make('summary')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('experiences_count')
                    ->counts('experiences')
                    ->label('Experiences')
                    ->sortable(),
                TextColumn::make('packages_count')
                    ->counts('packages')
                    ->label('Packages')
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
                        'package' => 'Package Category',
                    ]),
                TernaryFilter::make('is_featured')
                    ->label('Shown on menu'),
            ])
            ->headerActions([
                CreateAction::make('createLocation')
                    ->label('Add Location Subcategory')
                    ->modalHeading('New Location Subcategory')
                    ->fillForm([
                        'collection_group' => 'location',
                        'is_featured' => true,
                        'sort_order' => 0,
                    ])
                    ->createAnother(false),
                CreateAction::make('createActivity')
                    ->label('Add Activity Type Subcategory')
                    ->modalHeading('New Activity Type Subcategory')
                    ->fillForm([
                        'collection_group' => 'activity',
                        'is_featured' => true,
                        'sort_order' => 0,
                    ])
                    ->createAnother(false),
                CreateAction::make('createPackageCategory')
                    ->label('Add Package Category')
                    ->modalHeading('New Package Category')
                    ->fillForm([
                        'collection_group' => 'package',
                        'is_featured' => true,
                        'sort_order' => 0,
                    ])
                    ->createAnother(false),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
