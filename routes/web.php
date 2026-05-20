<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\NetworkWebhookController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentDocumentController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/acute-landing', [PageController::class, 'acuteLanding'])->name('landing.acute');
Route::get('/experiences', [PageController::class, 'experiences'])->name('experiences.index');
Route::get('/experiences/{slug}', [PageController::class, 'experience'])->name('experiences.show');
Route::get('/tours', [PageController::class, 'tours'])->name('tours.index');
Route::get('/tours/{slug}', [PageController::class, 'tour'])->name('tours.show');
Route::get('/packages', [PageController::class, 'packages'])->name('packages.index');
Route::get('/packages/{slug}', [PageController::class, 'package'])->name('packages.show');
Route::get('/visa-services', [PageController::class, 'visaServices'])->name('visa.index');
Route::get('/schengen-visa', [PageController::class, 'schengenVisa'])->name('visa.schengen');
Route::get('/collections/{slug}', [PageController::class, 'collection'])->name('collections.show');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/cancellation-policy', [PageController::class, 'cancellationPolicy'])->name('cancellation-policy');
Route::get('/terms-and-conditions', [PageController::class, 'termsAndConditions'])->name('terms-and-conditions');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/corporate-events', [PageController::class, 'corporateEvents'])->name('corporate-events');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{key}', [CartController::class, 'update'])->name('cart.update')->where('key', '.*');
Route::delete('/cart/{key}', [CartController::class, 'destroy'])->name('cart.destroy')->where('key', '.*');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');
Route::get('/checkout/cart', [CheckoutController::class, 'cart'])->name('checkout.cart.show');
Route::post('/checkout/cart', [CheckoutController::class, 'startCart'])->name('checkout.cart.start');
Route::get('/checkout/experiences/{slug}', [CheckoutController::class, 'experience'])->name('checkout.experiences.show');
Route::post('/checkout/experiences/{slug}', [CheckoutController::class, 'startExperience'])->name('checkout.experiences.start');
Route::get('/checkout/tours/{slug}', [CheckoutController::class, 'tour'])->name('checkout.tours.show');
Route::post('/checkout/tours/{slug}', [CheckoutController::class, 'startTour'])->name('checkout.tours.start');
Route::get('/checkout/packages/{slug}', [CheckoutController::class, 'package'])->name('checkout.packages.show');
Route::post('/checkout/packages/{slug}', [CheckoutController::class, 'startPackage'])->name('checkout.packages.start');
Route::get('/payments/network/callback', [CheckoutController::class, 'callback'])->name('payments.network.callback');
Route::post('/payments/network/webhook', NetworkWebhookController::class)->name('payments.network.webhook');
Route::get('/checkout/result/{transaction}', [CheckoutController::class, 'result'])->name('checkout.result');
Route::get('/journal', [PageController::class, 'journal'])->name('journal');
Route::get('/journal/{slug}', [PageController::class, 'article'])->name('journal.show');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/account', [AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/account/orders/{transaction}', [AccountController::class, 'order'])->name('account.orders.show');
    Route::get('/account/orders/{transaction}/invoice', [PaymentDocumentController::class, 'accountInvoice'])->name('account.orders.invoice');
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::patch('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::patch('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/account/feedback', [AccountController::class, 'storeFeedback'])->name('account.feedback.store');
    Route::middleware('admin')->group(function () {
        Route::get('/admin/payment-transactions/{transaction}/invoice', [PaymentDocumentController::class, 'adminInvoice'])->name('admin.payment-transactions.invoice');
    });
});
