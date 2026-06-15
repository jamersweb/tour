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
    phone_country_code: '+971',
    phone: '',
    travel_date: props.checkout.defaults?.travel_date || '',
    guest_count: props.checkout.defaults?.guest_count || 1,
    adult_count: props.checkout.defaults?.adult_count || props.checkout.defaults?.guest_count || 1,
    child_count: props.checkout.defaults?.child_count || 0,
    booking_option: props.checkout.defaults?.booking_option || '',
    tour_option: '',
    preferred_time: props.checkout.preferenceOptions?.times?.[0] || '',
    hotel_pickup_location: '',
    special_request: '',
    traveler_contacts: [
        { name: '', email: '', phone: '' },
    ],
});

const supportsTourPreferences = computed(() => Boolean(props.checkout.supportsTourPreferences));
const supportsPickupLocation = computed(() => Boolean(props.checkout.supportsPickupLocation));

const preferenceOptions = computed(() => props.checkout.preferenceOptions || {});
const productDetails = computed(() => props.checkout.productDetails || {});
const selectedBookingOption = computed(() => props.checkout.selectedBookingOption || null);

const tourOptions = computed(() => preferenceOptions.value.tourOptions || []);
const timeOptions = computed(() => {
    const options = preferenceOptions.value.times || [];

    return options;
});
const showPreferredTime = computed(() => supportsTourPreferences.value && timeOptions.value.length > 0);

const phoneCountryOptions = [
    { code: '+971', flag: 'AE', label: 'United Arab Emirates' },
    { code: '+966', flag: 'SA', label: 'Saudi Arabia' },
    { code: '+974', flag: 'QA', label: 'Qatar' },
    { code: '+965', flag: 'KW', label: 'Kuwait' },
    { code: '+973', flag: 'BH', label: 'Bahrain' },
    { code: '+968', flag: 'OM', label: 'Oman' },
    { code: '+44', flag: 'GB', label: 'United Kingdom' },
    { code: '+1', flag: 'US', label: 'United States / Canada' },
    { code: '+91', flag: 'IN', label: 'India' },
    { code: '+92', flag: 'PK', label: 'Pakistan' },
    { code: '+20', flag: 'EG', label: 'Egypt' },
];

const formattedTravelers = computed(() => {
    const adults = Math.max(0, Number.parseInt(form.adult_count, 10) || 0);
    const kids = Math.max(0, Number.parseInt(form.child_count, 10) || 0);
    const total = Math.max(1, adults + kids || Number.parseInt(form.guest_count, 10) || 1);
    const parts = [`${adults || total} adult${(adults || total) === 1 ? '' : 's'}`];

    if (kids) {
        parts.push(`${kids} kid${kids === 1 ? '' : 's'}`);
    }

    return `${total} (${parts.join(', ')})`;
});

const totalAmount = computed(() => {
    if (props.checkout.isCart) {
        return props.checkout.amount;
    }

    const adults = Math.max(0, Number.parseInt(form.adult_count, 10) || 0);
    const kids = Math.max(0, Number.parseInt(form.child_count, 10) || 0);
    const unitAmount = Number.parseFloat(props.checkout.unitAmountValue || 0);
    const childUnitAmount = Number.parseFloat(props.checkout.childUnitAmountValue ?? unitAmount) || unitAmount;
    const fallbackGuestCount = Math.max(1, Number.parseInt(form.guest_count, 10) || 1);
    const total = adults || kids
        ? (unitAmount * adults) + (childUnitAmount * kids)
        : unitAmount * fallbackGuestCount;

    return `${props.checkout.currency} ${new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(total)}`;
});

const selectedRows = computed(() => {
    if (props.checkout.isCart) {
        return [];
    }

    const rows = [
        { label: 'Selected item', value: props.checkout.title },
        { label: 'Duration', value: productDetails.value.duration },
        { label: 'Experience type', value: productDetails.value.experienceType },
        { label: 'Transfer option', value: productDetails.value.transferOption },
        { label: 'Booking type', value: productDetails.value.bookingType },
        { label: 'Booking option', value: selectedBookingOption.value?.label },
        { label: 'Date', value: form.travel_date || 'Selected during booking' },
        { label: 'Travelers', value: formattedTravelers.value },
    ].filter((row) => row.value);

    if (supportsTourPreferences.value) {
        if (tourOptions.value.length) {
            rows.push({ label: 'Tour language', value: form.tour_option || 'Selected later' });
        }

        if (showPreferredTime.value) {
            rows.push({ label: 'Tour time', value: form.preferred_time || 'Selected later' });
        }
    }

    return rows;
});

const submit = () => {
    const adultCount = Math.max(0, Number.parseInt(form.adult_count, 10) || 0);
    const childCount = Math.max(0, Number.parseInt(form.child_count, 10) || 0);
    const guestCount = Math.max(1, adultCount + childCount || Number.parseInt(form.guest_count, 10) || 1);

    form.guest_count = props.checkout.isCart ? props.checkout.defaults?.guest_count || guestCount : guestCount;
    form.adult_count = props.checkout.isCart ? props.checkout.defaults?.adult_count || adultCount || guestCount : adultCount || guestCount;
    form.child_count = props.checkout.isCart ? props.checkout.defaults?.child_count || childCount : childCount;
    form.traveler_contacts = Array.from({ length: guestCount }, () => ({
        name: form.name,
        email: form.email,
        phone: `${form.phone_country_code} ${form.phone}`.trim(),
    }));

    form
        .transform((data) => ({
            ...data,
            phone: `${data.phone_country_code} ${data.phone}`.trim(),
        }))
        .post(props.checkout.isCart ? '/checkout/cart' : `/checkout/${props.checkout.type}s/${props.checkout.slug}`);
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
                        <div class="phone-input-group">
                            <select v-model="form.phone_country_code" aria-label="Country code">
                                <option v-for="country in phoneCountryOptions" :key="country.code" :value="country.code">
                                    {{ country.flag }} {{ country.code }}
                                </option>
                            </select>
                            <input v-model="form.phone" type="text" autocomplete="tel" placeholder="Mobile number *" />
                        </div>
                        <small v-if="form.errors.phone">{{ form.errors.phone }}</small>
                    </label>

                    <label class="field">
                        <span>Email address</span>
                        <input v-model="form.email" type="email" autocomplete="email" placeholder="Email address *" />
                        <small v-if="form.errors.email">{{ form.errors.email }}</small>
                    </label>

                    <label v-if="supportsTourPreferences && tourOptions.length" class="field">
                        <span>Tour language</span>
                        <select v-model="form.tour_option">
                            <option value="">Select language later</option>
                            <option v-for="option in tourOptions" :key="option" :value="option">{{ option }}</option>
                        </select>
                        <small v-if="form.errors.tour_option">{{ form.errors.tour_option }}</small>
                    </label>

                    <label v-if="showPreferredTime" class="field">
                        <span>Preferred tour time</span>
                        <select v-model="form.preferred_time">
                            <option v-for="option in timeOptions" :key="option" :value="option">{{ option }}</option>
                        </select>
                        <small v-if="form.errors.preferred_time">{{ form.errors.preferred_time }}</small>
                    </label>

                    <label v-if="supportsPickupLocation" class="field">
                        <span>Hotel Pick up location (if applicable)</span>
                        <input
                            v-model="form.hotel_pickup_location"
                            type="text"
                            placeholder="Hotel name / address / meet-up location"
                            autocomplete="street-address"
                        />
                        <small v-if="form.errors.hotel_pickup_location">{{ form.errors.hotel_pickup_location }}</small>
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
                                <span v-if="item.bookingOption">{{ item.bookingOption.label }}</span>
                                <span>
                                    {{ item.guestCount }} guests
                                    <template v-if="item.adultCount || item.childCount">
                                        ({{ item.adultCount || item.guestCount }} adults<span v-if="item.childCount">, {{ item.childCount }} kids</span>)
                                    </template>
                                    <span v-if="item.travelDate"> | {{ item.travelDate }}</span>
                                </span>
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
