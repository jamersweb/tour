<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    order: Object,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container detail-grid">
            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Receipt</p>
                    <h1 class="page-title">{{ order.itemTitle || 'Booking' }}</h1>
                    <p class="page-copy">Reference {{ order.reference }}</p>

                    <div class="account-list">
                        <div class="account-row">
                            <strong>Status</strong>
                            <p>{{ order.status }}</p>
                        </div>
                        <div class="account-row">
                            <strong>Amount</strong>
                            <p>{{ order.amount }}</p>
                        </div>
                        <div class="account-row">
                            <strong>Travel Date</strong>
                            <p>{{ order.itemType === 'Cart' ? 'See cart items' : (order.travelDate || 'To be confirmed') }}</p>
                        </div>
                        <div class="account-row">
                            <strong>Guests</strong>
                            <p>{{ order.guestCount || order.travelers.length }}</p>
                        </div>
                        <div class="account-row">
                            <strong>Payment Confirmed</strong>
                            <p>{{ order.paidAt || 'Pending' }}</p>
                        </div>
                    </div>
                </article>

                <article v-if="order.cartItems?.length" class="info-card">
                    <p class="card-tag">Cart Items</p>
                    <div class="account-list">
                        <div v-for="item in order.cartItems" :key="`${item.title}-${item.travelDate}`" class="account-row">
                            <strong>{{ item.title }}</strong>
                            <p>{{ item.travelDate || 'To be confirmed' }} | {{ item.guestCount }} guests | {{ item.lineTotal }}</p>
                        </div>
                    </div>
                </article>

                <article class="info-card">
                    <p class="card-tag">Travelers</p>
                    <div class="account-list">
                        <div v-for="traveler in order.travelers" :key="traveler.position" class="account-row">
                            <strong>{{ traveler.position }}. {{ traveler.name }}</strong>
                            <p>{{ traveler.email }} | {{ traveler.phone || 'No phone provided' }}</p>
                        </div>
                    </div>
                </article>
            </div>

            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Booked By</p>
                    <div class="account-list">
                        <div class="account-row">
                            <strong>Name</strong>
                            <p>{{ order.customerName }}</p>
                        </div>
                        <div class="account-row">
                            <strong>Email</strong>
                            <p>{{ order.customerEmail }}</p>
                        </div>
                        <div class="account-row">
                            <strong>Phone</strong>
                            <p>{{ order.customerPhone || 'No phone provided' }}</p>
                        </div>
                        <div class="account-row">
                            <strong>Confirmation Sent</strong>
                            <p>{{ order.confirmationSentAt || 'Pending' }}</p>
                        </div>
                    </div>

                    <div class="hero-actions">
                        <Link class="button-secondary" href="/account">Back to dashboard</Link>
                        <a class="button-secondary" :href="order.invoiceUrl">Download PDF</a>
                        <button class="button-primary" type="button" @click="window.print()">Print receipt</button>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
