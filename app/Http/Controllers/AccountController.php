<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\StoreFeedbackRequest;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Models\Feedback;
use App\Models\PaymentTransaction;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function dashboard(): Response
    {
        $user = request()->user()->load([
            'paymentTransactions' => fn ($query) => $query->latest()->limit(5),
            'inquiries' => fn ($query) => $query->latest()->limit(5),
            'feedback' => fn ($query) => $query->latest()->limit(5),
        ]);

        return Inertia::render('Account/Dashboard', [
            'seo' => [
                'title' => 'My Dashboard',
                'description' => 'Review your orders, inquiries, and feedback.',
            ],
            'account' => [
                'name' => $user->name,
                'email' => $user->email,
                'stats' => [
                    'orders' => $user->paymentTransactions()->count(),
                    'paidOrders' => $user->paymentTransactions()->where('status', 'paid')->count(),
                    'inquiries' => $user->inquiries()->count(),
                'feedback' => $user->feedback()->count(),
                ],
                'orders' => $user->paymentTransactions->map(fn ($transaction) => [
                    'id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'status' => $transaction->status,
                    'amount' => "{$transaction->currency} ".number_format((float) $transaction->amount, 2),
                    'itemTitle' => $transaction->payable?->title,
                    'createdAt' => $transaction->created_at?->format('F j, Y'),
                    'invoiceUrl' => route('account.orders.invoice', $transaction),
                ]),
                'inquiries' => $user->inquiries->map(fn ($inquiry) => [
                    'interest' => $inquiry->interest,
                    'experienceTitle' => $inquiry->experience_title,
                    'status' => $inquiry->status,
                    'createdAt' => $inquiry->created_at?->format('F j, Y'),
                ]),
                'feedback' => $user->feedback->map(fn ($feedback) => [
                    'subject' => $feedback->subject,
                    'category' => $feedback->category,
                    'rating' => $feedback->rating,
                    'status' => $feedback->status,
                    'createdAt' => $feedback->created_at?->format('F j, Y'),
                ]),
            ],
            'feedbackOptions' => [
                'general',
                'booking',
                'experience',
                'support',
            ],
        ]);
    }

    public function profile(): Response
    {
        return Inertia::render('Account/Profile', [
            'seo' => [
                'title' => 'My Profile',
                'description' => 'Manage your Acute Tourism account profile.',
            ],
            'profile' => [
                'name' => request()->user()->name,
                'email' => request()->user()->email,
            ],
        ]);
    }

    public function order(PaymentTransaction $transaction): Response
    {
        abort_unless($transaction->user_id === request()->user()->id, 404);

        $transaction->loadMissing('payable', 'travelers');

        return Inertia::render('Account/OrderShow', [
            'seo' => [
                'title' => 'Order Receipt',
                'description' => 'Review your booking receipt and traveler details.',
            ],
            'order' => [
                'id' => $transaction->id,
                'reference' => $transaction->reference,
                'status' => $transaction->status,
                'amount' => "{$transaction->currency} ".number_format((float) $transaction->amount, 2),
                'itemTitle' => $transaction->payable?->title,
                'itemType' => class_basename($transaction->payable_type),
                'travelDate' => $transaction->travel_date?->format('F j, Y'),
                'guestCount' => $transaction->guest_count,
                'customerName' => $transaction->customer_name,
                'customerEmail' => $transaction->customer_email,
                'customerPhone' => $transaction->customer_phone,
                'paidAt' => $transaction->paid_at?->format('F j, Y g:i A'),
                'confirmationSentAt' => $transaction->confirmation_sent_at?->format('F j, Y g:i A'),
                'invoiceUrl' => route('account.orders.invoice', $transaction),
                'travelers' => $transaction->travelers->map(fn ($traveler) => [
                    'position' => $traveler->position,
                    'name' => $traveler->name,
                    'email' => $traveler->email,
                    'phone' => $traveler->phone,
                ]),
            ],
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated()['password'],
        ]);

        return back()->with('success', 'Password updated.');
    }

    public function storeFeedback(StoreFeedbackRequest $request): RedirectResponse
    {
        Feedback::query()->create($request->validated() + [
            'user_id' => $request->user()->id,
            'status' => 'new',
        ]);

        return back()->with('success', 'Feedback submitted.');
    }
}
