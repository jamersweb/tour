<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    payment: Object,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">Payment Result</p>
            <h1 class="page-title">{{ payment.itemTitle }}</h1>

            <article class="info-card">
                <p class="card-tag">Status</p>
                <h3>{{ payment.status }}</h3>
                <p class="page-copy">Reference: {{ payment.reference }}</p>
                <p class="page-copy">Amount: {{ payment.amount }}</p>
                <p class="page-copy">Customer: {{ payment.customerName }}</p>
            </article>

            <div class="hero-actions">
                <Link class="button-primary" href="/contact">Need assistance</Link>
                <Link class="button-secondary" href="/experiences">Back to experiences</Link>
            </div>
        </div>
    </section>
</template>
