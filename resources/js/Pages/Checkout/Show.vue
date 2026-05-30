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
    travel_date: props.checkout.defaults?.travel_date || '',
    guest_count: props.checkout.defaults?.guest_count || 1,
    tour_option: '',
    preferred_time: '',
    special_request: '',
    traveler_contacts: [
        { name: '', email: '', phone: '' },
    ],
});

const supportsTourPreferences = computed(() => Boolean(props.checkout.supportsTourPreferences));

const preferenceOptions = computed(() => props.checkout.preferenceOptions || {});

const tourOptions = computed(() => preferenceOptions.value.tourOptions || []);

const totalAmount = computed(() => {
    if (props.checkout.isCart) {
        return props.checkout.amount;
    }

    const guestCount = Math.max(1, Number.parseInt(form.guest_count, 10) || 1);
    const unitAmount = Number.parseFloat(props.checkout.unitAmountValue || 0);

    return `${props.checkout.currency} ${new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(unitAmount * guestCount)}`;
});

const selectedRows = computed(() => {
    if (props.checkout.isCart) {
        return [];
    }

    const rows = [
        { label: 'Selected item', value: props.checkout.title },
        { label: 'Date', value: form.travel_date || 'Selected during booking' },
        { label: 'Travelers', value: form.guest_count },
    ];

    if (supportsTourPreferences.value) {
        if (tourOptions.value.length) {
            rows.push({ label: 'Tour option', value: form.tour_option || 'Selected later' });
        }

        rows.push(
            { label: 'Tour time', value: form.preferred_time || 'Flexible' },
        );
    }

    return rows;
});

const submit = () => {
    const guestCount = Math.max(1, Number.parseInt(form.guest_count, 10) || 1);

    form.guest_count = props.checkout.isCart ? props.checkout.defaults?.guest_count || guestCount : guestCount;
    form.traveler_contacts = Array.from({ length: guestCount }, () => ({
        name: form.name,
        email: form.email,
        phone: form.phone,
    }));

    form.post(props.checkout.isCart ? '/checkout/cart' : `/checkout/${props.checkout.type}s/${props.checkout.slug}`);
};
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro checkout-page">
        <div class="container checkout-layout">
            <article class="info-card checkout-form-card">
                <p class="card-tag">{{ checkout.label }}</p>
                <h1 class="checkout-title">Enter your contact details</h1>
                <p class="meta-copy">Required fields are used for booking confirmation and customer support.</p>

                <div v-if="page.props.flash.error" class="error-banner">
                    {{ page.props.flash.error }}
                </div>

                <form class="lead-form checkout-lead-form" @submit.prevent="submit">
                    <label class="field">
                        <span>Name</span>
                        <input v-model="form.name" type="text" autocomplete="name" placeholder="Full name *" />
                        <small v-if="form.errors.name">{{ form.errors.name }}</small>
                    </label>

                    <label class="field">
                        <span>Contact number</span>
                        <input v-model="form.phone" type="text" autocomplete="tel" placeholder="Mobile number *" />
                        <small v-if="form.errors.phone">{{ form.errors.phone }}</small>
                    </label>

                    <label class="field">
                        <span>Email address</span>
                        <input v-model="form.email" type="email" autocomplete="email" placeholder="Email address *" />
                        <small v-if="form.errors.email">{{ form.errors.email }}</small>
                    </label>

                    <label v-if="supportsTourPreferences && tourOptions.length" class="field">
                        <span>Tour option</span>
                        <select v-model="form.tour_option">
                            <option value="">Select option later</option>
                            <option v-for="option in tourOptions" :key="option" :value="option">{{ option }}</option>
                        </select>
                        <small v-if="form.errors.tour_option">{{ form.errors.tour_option }}</small>
                    </label>

                    <label v-if="supportsTourPreferences" class="field">
                        <span>Preferred tour time</span>
                        <input
                            v-model="form.preferred_time"
                            type="text"
                            placeholder="Flexible / preferred time"
                        />
                        <small v-if="form.errors.preferred_time">{{ form.errors.preferred_time }}</small>
                    </label>

                    <label class="field field-full">
                        <span>Special request</span>
                        <textarea
                            v-model="form.special_request"
                            rows="5"
                            placeholder="Type your special request here..."
                        ></textarea>
                        <small v-if="form.errors.special_request">{{ form.errors.special_request }}</small>
                    </label>

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

            <aside class="checkout-summary-card" aria-label="Selected booking summary">
                <div v-if="checkout.image" class="checkout-summary-media">
                    <img :src="checkout.image" :alt="checkout.title" />
                </div>

                <div class="checkout-summary-body">
                    <p class="card-tag">Selected booking</p>
                    <h2>{{ checkout.title }}</h2>
                    <p v-if="checkout.summary" class="meta-copy">{{ checkout.summary }}</p>

                    <div v-if="checkout.isCart" class="checkout-cart-items">
                        <article v-for="item in checkout.items" :key="`${item.type}-${item.slug}`" class="checkout-cart-item">
                            <img v-if="item.image" :src="item.image" :alt="item.title" />
                            <div>
                                <strong>{{ item.title }}</strong>
                                <span>{{ item.guestCount }} guests<span v-if="item.travelDate"> | {{ item.travelDate }}</span></span>
                            </div>
                            <b>{{ item.lineTotalFormatted }}</b>
                        </article>
                    </div>

                    <dl v-else class="checkout-selected-list">
                        <div v-for="row in selectedRows" :key="row.label">
                            <dt>{{ row.label }}</dt>
                            <dd>{{ row.value }}</dd>
                        </div>
                    </dl>

                    <div class="checkout-total-row">
                        <span>Total</span>
                        <strong>{{ totalAmount }}</strong>
                    </div>

                    <p class="checkout-payment-note">
                        You complete card payment on Network's secure hosted page, then return here for confirmation.
                    </p>
                    <p v-if="!page.props.payments?.networkCheckoutReady" class="meta-copy">
                        Online payment is not fully configured. Submitting the form will fail until checkout is ready.
                    </p>
                </div>
            </aside>
        </div>
    </section>
</template>
