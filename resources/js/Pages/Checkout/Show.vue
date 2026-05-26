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
    preferred_time: '',
    preferred_language: '',
    special_request: '',
    traveler_contacts: [
        { name: '', email: '', phone: '' },
    ],
});

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

                    <div v-else class="checkout-selection-summary">
                        <span>Selected booking</span>
                        <strong>{{ checkout.title }}</strong>
                        <p>
                            <template v-if="form.travel_date">Date: {{ form.travel_date }} · </template>
                            Travelers: {{ form.guest_count }}
                        </p>
                    </div>
                </article>
            </div>

            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Pay with N-Genius Online (Network International)</p>
                    <h3>{{ checkout.amount }}</h3>
                    <p class="meta-copy">{{ checkout.isCart ? 'Total for all cart items' : 'Base price per guest' }}</p>
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

                        <label v-if="!checkout.isCart" class="field">
                            <span>Preferred tour time</span>
                            <select v-model="form.preferred_time">
                                <option value="">Flexible</option>
                                <option value="Morning">Morning</option>
                                <option value="Afternoon">Afternoon</option>
                                <option value="Evening">Evening</option>
                            </select>
                            <small v-if="form.errors.preferred_time">{{ form.errors.preferred_time }}</small>
                        </label>

                        <label v-if="!checkout.isCart" class="field">
                            <span>Preferred language</span>
                            <select v-model="form.preferred_language">
                                <option value="">English</option>
                                <option value="Arabic">Arabic</option>
                                <option value="Russian">Russian</option>
                                <option value="Hindi / Urdu">Hindi / Urdu</option>
                            </select>
                            <small v-if="form.errors.preferred_language">{{ form.errors.preferred_language }}</small>
                        </label>

                        <label class="field field-full">
                            <span>Special request</span>
                            <textarea v-model="form.special_request" rows="4" placeholder="Pickup notes, celebration details, hotel name, or accessibility requests"></textarea>
                            <small v-if="form.errors.special_request">{{ form.errors.special_request }}</small>
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
