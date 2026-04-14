<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    payment: Object,
});

const page = usePage();

const contact = computed(() => page.props.site?.contact ?? {});

const supportLines = computed(() => {
    const c = contact.value;
    const lines = [];
    if (c.email) {
        lines.push({ label: 'Email', href: `mailto:${c.email}`, text: c.email });
    }
    if (c.phone) {
        const tel = String(c.phone).replace(/[^\d+]/g, '');
        lines.push({
            label: 'Phone',
            href: tel.length >= 8 ? `tel:${tel}` : null,
            text: c.phone,
        });
    }
    if (c.whatsappNumber) {
        const wa = String(c.whatsappNumber).replace(/\D/g, '');
        if (wa) {
            lines.push({
                label: 'WhatsApp',
                href: `https://wa.me/${wa}`,
                text: c.whatsappNumber,
            });
        }
    }
    return lines;
});

const backHref = computed(() =>
    props.payment?.itemType === 'Package' ? '/packages' : '/experiences',
);

const backLabel = computed(() =>
    props.payment?.itemType === 'Package' ? 'packages' : 'experiences',
);
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">Payment Result</p>
            <h1 class="page-title">{{ payment.headline }}</h1>
            <p class="page-copy">{{ payment.message }}</p>
            <p v-if="page.props.flash?.error" class="error-banner">{{ page.props.flash.error }}</p>

            <aside
                v-if="!payment.isSuccess && supportLines.length"
                class="support-hint"
                aria-label="Contact options"
            >
                <p class="support-hint-title">Need help with this payment?</p>
                <ul class="support-hint-list">
                    <li v-for="(line, idx) in supportLines" :key="idx">
                        <template v-if="line.href">
                            <a :href="line.href">{{ line.label }}: {{ line.text }}</a>
                        </template>
                        <template v-else>{{ line.label }}: {{ line.text }}</template>
                    </li>
                </ul>
                <p class="page-copy subtle">
                    Please include your reference
                    <strong>{{ payment.reference }}</strong>
                    so we can find your booking quickly.
                </p>
            </aside>

            <article class="info-card">
                <p class="card-tag">Booking</p>
                <h3>{{ payment.itemTitle }}</h3>
                <p class="page-copy">Status: <strong>{{ payment.status }}</strong></p>
                <p class="page-copy">Reference: {{ payment.reference }}</p>
                <p class="page-copy">Amount: {{ payment.amount }}</p>
                <p class="page-copy">Customer: {{ payment.customerName }}</p>
            </article>

            <div class="hero-actions">
                <Link
                    v-if="payment.retryCheckoutUrl && !payment.isSuccess"
                    class="button-primary"
                    :href="payment.retryCheckoutUrl"
                >
                    Try checkout again
                </Link>
                <Link class="button-primary" href="/contact">Need assistance</Link>
                <Link class="button-secondary" :href="backHref">Back to {{ backLabel }}</Link>
                <Link class="button-secondary" href="/">Home</Link>
            </div>
        </div>
    </section>
</template>
