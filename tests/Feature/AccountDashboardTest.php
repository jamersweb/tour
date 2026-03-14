<?php

namespace Tests\Feature;

use App\Models\ExperienceInquiry;
use App\Models\Feedback;
use App\Models\Package;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AccountDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_guest_is_redirected_from_account_dashboard(): void
    {
        $this->get('/account')->assertRedirect('/login');
    }

    public function test_user_can_register_and_reach_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Nadia Ali',
            'email' => 'nadia@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticated();
    }

    public function test_dashboard_shows_orders_inquiries_and_feedback(): void
    {
        $user = User::factory()->create();
        $package = Package::query()->firstOrFail();

        PaymentTransaction::query()->create([
            'user_id' => $user->id,
            'payable_type' => $package::class,
            'payable_id' => $package->id,
            'reference' => 'txn-1',
            'status' => 'paid',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'amount' => 200,
            'amount_minor' => 20000,
            'currency' => 'AED',
        ]);

        ExperienceInquiry::query()->create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'interest' => 'General Planning',
            'message' => 'Need help',
            'source' => 'contact-page',
            'status' => 'new',
        ]);

        Feedback::query()->create([
            'user_id' => $user->id,
            'category' => 'general',
            'rating' => 5,
            'subject' => 'Great support',
            'message' => 'Fast response time.',
            'status' => 'new',
        ]);

        $response = $this->actingAs($user)->get('/account');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Account/Dashboard')
            ->where('account.stats.orders', 1)
            ->where('account.stats.inquiries', 1)
            ->where('account.stats.feedback', 1)
        );
    }

    public function test_authenticated_user_can_submit_feedback(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/account/feedback', [
            'category' => 'support',
            'rating' => 4,
            'subject' => 'Helpful team',
            'message' => 'Support was helpful during booking.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('feedback', [
            'user_id' => $user->id,
            'subject' => 'Helpful team',
            'category' => 'support',
        ]);
    }

    public function test_user_can_open_own_order_receipt(): void
    {
        $user = User::factory()->create();
        $package = Package::query()->firstOrFail();

        $transaction = PaymentTransaction::query()->create([
            'user_id' => $user->id,
            'payable_type' => $package::class,
            'payable_id' => $package->id,
            'reference' => 'txn-2',
            'status' => 'paid',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '+971500000100',
            'guest_count' => 2,
            'amount' => 200,
            'amount_minor' => 20000,
            'currency' => 'AED',
        ]);

        $transaction->travelers()->create([
            'position' => 1,
            'name' => 'Traveler One',
            'email' => 'traveler@example.com',
            'phone' => '+971500000101',
        ]);

        $response = $this->actingAs($user)->get("/account/orders/{$transaction->id}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Account/OrderShow')
            ->where('order.reference', 'txn-2')
            ->where('order.travelers.0.name', 'Traveler One')
        );
    }

    public function test_user_can_download_own_invoice_pdf(): void
    {
        $user = User::factory()->create();
        $package = Package::query()->firstOrFail();

        $transaction = PaymentTransaction::query()->create([
            'user_id' => $user->id,
            'payable_type' => $package::class,
            'payable_id' => $package->id,
            'reference' => 'txn-invoice-1',
            'status' => 'paid',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'amount' => 200,
            'amount_minor' => 20000,
            'currency' => 'AED',
        ]);

        $response = $this->actingAs($user)->get("/account/orders/{$transaction->id}/invoice");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_user_can_update_password_from_profile(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $response = $this->actingAs($user)->patch('/account/password', [
            'current_password' => 'old-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => 'old-password',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Hash::check('brand-new-password', $user->fresh()->password));
    }
}
