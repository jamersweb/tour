<?php

namespace App\Filament\Resources\PaymentTransactions\Pages;

use App\Filament\Resources\PaymentTransactions\PaymentTransactionResource;
use App\Services\Payments\PaymentOperationsService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentTransaction extends ViewRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('invoice')
                ->label('Invoice PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => route('admin.payment-transactions.invoice', $this->record))
                ->openUrlInNewTab(),
            Action::make('reconcile')
                ->icon('heroicon-o-check-badge')
                ->form([
                    Textarea::make('notes')->rows(3),
                ])
                ->action(function (array $data): void {
                    app(PaymentOperationsService::class)->reconcile(
                        $this->record,
                        auth()->user(),
                        status: 'paid',
                        notes: $data['notes'] ?? null,
                    );

                    Notification::make()->title('Payment reconciled as paid.')->success()->send();
                    $this->refreshFormData(['status', 'paid_at', 'reconciled_at', 'reconciled_by', 'notes']);
                }),
            Action::make('refund')
                ->label('Refund')
                ->icon('heroicon-o-arrow-uturn-left')
                ->form([
                    Textarea::make('notes')->rows(3)->required(),
                ])
                ->action(function (array $data): void {
                    app(PaymentOperationsService::class)->refund(
                        $this->record,
                        auth()->user(),
                        notes: $data['notes'] ?? null,
                    );

                    Notification::make()->title('Payment marked as refunded.')->success()->send();
                    $this->refreshFormData(['status', 'refunded_at', 'refunded_by', 'notes']);
                }),
            Action::make('resendConfirmation')
                ->label('Resend Confirmation')
                ->icon('heroicon-o-envelope')
                ->action(function (): void {
                    app(PaymentOperationsService::class)->resendConfirmation($this->record);

                    Notification::make()->title('Confirmation resent.')->success()->send();
                    $this->refreshFormData(['confirmation_sent_at']);
                }),
            EditAction::make(),
        ];
    }
}
