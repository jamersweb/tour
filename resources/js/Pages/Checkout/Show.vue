<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    checkout: Object,
});

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 1,
    traveler_contacts: [
        { name: '', email: '', phone: '' },
    ],
});

const totalAmount = computed(() => {
    const guestCount = Math.max(1, Number.parseInt(form.guest_count, 10) || 1);
    const unitAmount = Number.parseFloat(props.checkout.unitAmountValue || 0);

    return `${props.checkout.currency} ${new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(unitAmount * guestCount)}`;
});

const submit = () => {
    const guestCount = Math.max(1, Number.parseInt(form.guest_count, 10) || 1);

    form.guest_count = guestCount;
    form.traveler_contacts = Array.from({ length: guestCount }, () => ({
        name: form.name,
        email: form.email,
        phone: form.phone,
    }));

    form.post(`/checkout/${props.checkout.type}s/${props.checkout.slug}`);
};
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container detail-grid">
            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">{{ checkout.label }}</p>
                    <h1 class="page-title">{{ checkout.title }}</h1>
                    <p class="page-copy">{{ checkout.summary }}</p>

                    <div v-if="checkout.image" class="page-hero-media">
                        <img :src="checkout.image" :alt="checkout.title" />
                    </div>
                </article>
            </div>

            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Pay with N-Genius Online (Network International)</p>
                    <h3>{{ checkout.amount }}</h3>
                    <p class="meta-copy">Base price per guest</p>
                    <p class="hero-copy">
                        You complete card payment on Network’s secure hosted page, then return here
                        so we can confirm your booking and send confirmation by email.
                    </p>
                    <p v-if="!page.props.payments?.networkCheckoutReady" class="meta-copy">
                        Online payment is not fully configured (enable N-Genius and set outlet + API
                        credentials). Submitting the form will fail until checkout is ready.
                    </p>

                    <div v-if="page.props.flash.error" class="error-banner">
                        {{ page.props.flash.error }}
                    </div>

                    <form class="lead-form" @submit.prevent="submit">
                        <label class="field">
                            <span>Name</span>
                            <input v-model="form.name" type="text" autocomplete="name" />
                            <small v-if="form.errors.name">{{ form.errors.name }}</small>
                        </label>

                        <label class="field">
                            <span>Email</span>
                            <input v-model="form.email" type="email" autocomplete="email" />
                            <small v-if="form.errors.email">{{ form.errors.email }}</small>
                        </label>

                        <label class="field">
                            <span>Phone</span>
                            <input v-model="form.phone" type="text" autocomplete="tel" />
                            <small v-if="form.errors.phone">{{ form.errors.phone }}</small>
                        </label>

                        <label class="field">
                            <span>Travel Date</span>
                            <input v-model="form.travel_date" type="date" />
                            <small v-if="form.errors.travel_date">{{ form.errors.travel_date }}</small>
                        </label>

                        <label class="field">
                            <span>Guests</span>
                            <input v-model="form.guest_count" type="number" min="1" max="100" />
                            <small v-if="form.errors.guest_count">{{ form.errors.guest_count }}</small>
                        </label>

                        <div class="field">
                            <span>Total Price</span>
                            <strong class="detail-price">{{ totalAmount }}</strong>
                        </div>

                        <button
                            class="button-primary"
                            type="submit"
                            :disabled="form.processing || !page.props.payments?.networkCheckoutReady"
                        >
                            {{
                                !page.props.payments?.networkCheckoutReady
                                    ? 'Payment setup required'
                                    : form.processing
                                      ? 'Redirecting...'
                                      : 'Proceed to Payment'
                            }}
                        </button>
                    </form>
                </article>
            </div>
        </div>
    </section>
</template>
