<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PaymentDocumentController extends Controller
{
    public function accountInvoice(PaymentTransaction $transaction): Response
    {
        abort_unless($transaction->user_id === request()->user()?->id, 404);

        return $this->downloadInvoice($transaction);
    }

    public function adminInvoice(PaymentTransaction $transaction): Response
    {
        abort_unless(request()->user()?->is_admin, 403);

        return $this->downloadInvoice($transaction);
    }

    protected function downloadInvoice(PaymentTransaction $transaction): Response
    {
        $transaction->loadMissing('payable', 'travelers', 'reconciledBy', 'refundedBy');

        $pdf = Pdf::loadView('pdf.invoice', [
            'transaction' => $transaction,
        ])->setPaper('a4');

        return $pdf->download("acute-invoice-{$transaction->reference}.pdf");
    }
}
