<?php

namespace App\Filament\Resources\PaymentTransactions\Pages;

use App\Filament\Resources\PaymentTransactions\PaymentTransactionResource;
use App\Services\Payments\PaymentTransactionLogger;
use Filament\Resources\Pages\EditRecord;

class EditPaymentTransaction extends EditRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function afterSave(): void
    {
        app(PaymentTransactionLogger::class)->record(
            $this->record->fresh(),
            'admin_edit_saved',
            'Transaction saved from admin edit form.',
            [],
            auth()->user(),
        );
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
