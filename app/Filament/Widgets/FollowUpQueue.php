<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ExperienceInquiries\ExperienceInquiryResource;
use App\Models\ExperienceInquiry;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class FollowUpQueue extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Follow-Up Queue';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ExperienceInquiry::query()
                    ->openPipeline()
                    ->whereNotNull('follow_up_at')
                    ->orderBy('follow_up_at')
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('experience_title')
                    ->label('Experience')
                    ->placeholder('General inquiry')
                    ->limit(28),
                TextColumn::make('follow_up_at')
                    ->dateTime()
                    ->color(fn (ExperienceInquiry $record): string => $record->follow_up_at && $record->follow_up_at->isPast() ? 'danger' : 'warning'),
                TextColumn::make('admin_notes')
                    ->placeholder('-')
                    ->limit(45),
            ])
            ->recordAction(null)
            ->actions([
                Action::make('edit')
                    ->label('Update')
                    ->url(fn (ExperienceInquiry $record): string => ExperienceInquiryResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
