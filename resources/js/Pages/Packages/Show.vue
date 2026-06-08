<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';
import { useMobileAutoCarousel } from '../../Composables/useMobileAutoCarousel';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    packageItem: Object,
    relatedPackages: Array,
});

const page = usePage();

const mediaItems = computed(() => props.packageItem.mediaItems || []);
const interestOptions = computed(() => page.props.site?.interestOptions || []);
const packageInquiryInterest = computed(() => (
    interestOptions.value.find((option) => /package|general/i.test(option))
    || interestOptions.value[0]
    || 'General Planning'
));

const heroTiles = computed(() => {
    if (!mediaItems.value.length) {
        return [];
    }

    return Array.from({ length: Math.min(5, Math.max(5, mediaItems.value.length)) }, (_, index) => mediaItems.value[index % mediaItems.value.length]);
});

const importantNotices = computed(() => [
    'Please keep your contact number active after booking so final coordination can be shared easily.',
    'Hotel, transfers, and operating sequence may vary slightly depending on date and availability.',
    'Carry passport or valid identification where required for included attractions.',
]);

const contactPhone = '(+971) 58 516 1554';
const contactEmail = 'info@acutetourism.org';
const bestFor = computed(() => [
    'First-time visitors who want Dubai highlights arranged in one plan.',
    'Families and groups who need hotels, transfers, and attractions coordinated together.',
    'Travelers who prefer a final quote based on dates, room type, guest count, and add-ons.',
]);
const quickFacts = computed(() => [
    { label: 'Duration', value: props.packageItem.duration || 'Flexible' },
    { label: 'Destinations', value: props.packageItem.location || 'Dubai & UAE' },
    { label: 'Hotel', value: 'With daily breakfast' },
    { label: 'Best For', value: bestFor.value[0] || 'Custom holiday planning' },
]);
const packageOptions = computed(() => [
    {
        title: 'Standard package',
        copy: 'Keep the planned inclusions and travel sequence, then confirm dates and guest details before payment.',
    },
    {
        title: 'Private transfer upgrade',
        copy: 'Use private transfers when the group needs smoother pickup timing, family comfort, or tighter daily pacing.',
    },
    {
        title: 'Custom travel add-ons',
        copy: 'Add visa support, flights, insurance, extra nights, or attraction upgrades once the base itinerary is clear.',
    },
]);
const packageFlexibility = computed(() => [
    'Hotel category can be adjusted based on budget and travel style.',
    'Private transfers can be upgraded to premium vehicle options.',
    'Attraction sequence may change based on operating days and availability.',
    'Visa assistance, flights, travel insurance, and extra nights can be added on request.',
]);
const reviewStars = computed(() => '★★★★★');
const packageFaqs = computed(() => [
    {
        question: 'Can this package be customized?',
        answer: 'Yes. Hotel category, travel dates, extra nights, attractions, transfers, and add-ons can be adjusted based on your preference.',
    },
    {
        question: 'Are flights included?',
        answer: 'Flights are not included by default but can be added if required.',
    },
    {
        question: 'Is visa included?',
        answer: 'Visa is not included by default. Visa assistance can be added depending on nationality and travel requirements.',
    },
    {
        question: 'Can I choose the hotel?',
        answer: 'Yes. Hotel options can be shared based on your budget, preferred area, room type, and travel dates.',
    },
    {
        question: 'Why can the final price change?',
        answer: 'Hotel rates, attraction availability, travel dates, number of guests, and selected upgrades can affect the final quotation.',
    },
]);
const activeMediaIndex = ref(null);
const openItineraryIndex = ref(null);
const mosaicRef = useMobileAutoCarousel();
const activeMediaItem = computed(() => (
    activeMediaIndex.value === null ? null : mediaItems.value[activeMediaIndex.value] ?? null
));
const showStickyCta = ref(false);
let stickyCtaTimer = null;

const form = useForm({
    travel_date: '',
    guest_count: 2,
});

const cartForm = useForm({
    type: 'package',
    slug: props.packageItem.slug,
    travel_date: '',
    guest_count: 2,
});

const customPackageForm = useForm({
    source: 'package-custom-request',
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 2,
    interest: packageInquiryInterest.value,
    hotel_preference: 'Flexible',
    add_ons: 'Not sure yet',
    message: '',
});

const totalAmount = computed(() => {
    const guestCount = Math.max(2, Number.parseInt(form.guest_count, 10) || 2);
    const rawAmount = String(props.packageItem.priceFrom || '0').replace(/[^0-9.]/g, '');
    const unitAmount = Number.parseFloat(rawAmount || '0');

    return `AED ${new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(unitAmount * guestCount)}`;
});

const focusBookingForm = (field = 'date') => {
    const bookingWidget = document.querySelector('#booking-widget');

    bookingWidget?.scrollIntoView({ behavior: 'smooth', block: 'center' });

    window.setTimeout(() => {
        const inputSelector = field === 'guests' ? 'input[type="number"]' : 'input[type="date"]';
        bookingWidget?.querySelector(inputSelector)?.focus();
    }, 450);
};

const addToCart = () => {
    const guestCount = Math.max(2, Number.parseInt(form.guest_count, 10) || 2);

    if (!form.travel_date) {
        focusBookingForm('date');
        return;
    }

    if (!guestCount) {
        focusBookingForm('guests');
        return;
    }

    cartForm.travel_date = form.travel_date;
    cartForm.guest_count = guestCount;
    cartForm.post('/cart', { preserveScroll: true });
};

const bookNow = () => {
    const guestCount = Math.max(2, Number.parseInt(form.guest_count, 10) || 2);

    if (!form.travel_date) {
        focusBookingForm('date');
        return;
    }

    if (!guestCount) {
        focusBookingForm('guests');
        return;
    }

    const params = new URLSearchParams({
        travel_date: form.travel_date,
        guest_count: String(guestCount),
    });

    window.location.assign(`/checkout/packages/${props.packageItem.slug}?${params.toString()}`);
};

const submitCustomPackageRequest = () => {
    const guestCount = Math.max(2, Number.parseInt(customPackageForm.guest_count, 10) || 2);
    const requestNote = customPackageForm.message?.trim() || 'No additional notes provided.';
    const visibleMessage = customPackageForm.message;

    customPackageForm.guest_count = guestCount;
    customPackageForm.interest = packageInquiryInterest.value;
    customPackageForm.message = [
        `Package request: ${props.packageItem.title}`,
        `Package slug: ${props.packageItem.slug}`,
        `Base price: ${props.packageItem.priceFrom || 'On request'}`,
        `Duration: ${props.packageItem.duration || 'Flexible'}`,
        `Destination: ${props.packageItem.location || 'Dubai & UAE'}`,
        `Preferred travel date: ${customPackageForm.travel_date || 'Not provided'}`,
        `Travelers: ${guestCount}`,
        `Hotel preference: ${customPackageForm.hotel_preference}`,
        `Add-ons: ${customPackageForm.add_ons}`,
        '',
        'Customer request:',
        requestNote,
    ].join('\n');

    customPackageForm.post('/inquiries', {
        preserveScroll: true,
        onError: () => {
            customPackageForm.message = visibleMessage;
        },
        onSuccess: () => {
            customPackageForm.reset('name', 'email', 'phone', 'travel_date', 'guest_count', 'message');
            customPackageForm.hotel_preference = 'Flexible';
            customPackageForm.add_ons = 'Not sure yet';
            customPackageForm.interest = packageInquiryInterest.value;
        },
    });
};

const toggleItinerary = (index) => {
    openItineraryIndex.value = openItineraryIndex.value === index ? null : index;
};

const openMedia = (index) => {
    activeMediaIndex.value = index % mediaItems.value.length;
};

const closeMedia = () => {
    activeMediaIndex.value = null;
};

const updateStickyCta = () => {
    const hero = document.querySelector('.package-detail-top-grid') || document.querySelector('.experience-operator-head');
    const heroBottom = hero?.getBoundingClientRect().bottom ?? 0;

    showStickyCta.value = heroBottom < 0;
};

onMounted(() => {
    updateStickyCta();
    window.addEventListener('scroll', updateStickyCta, { passive: true });
    window.addEventListener('resize', updateStickyCta);
    stickyCtaTimer = window.setInterval(updateStickyCta, 250);
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateStickyCta);
    window.removeEventListener('resize', updateStickyCta);
    if (stickyCtaTimer) {
        window.clearInterval(stickyCtaTimer);
    }
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" :image="seo.image" />

    <div class="experience-tour-page experience-tour-page--operator">
        <section class="experience-operator-shell section-block">
            <div class="container">
                <nav class="experience-breadcrumb" aria-label="Breadcrumb">
                    <Link href="/">Home</Link>
                    <span class="experience-breadcrumb__sep" aria-hidden="true">/</span>
                    <Link href="/packages">Packages</Link>
                    <span class="experience-breadcrumb__sep" aria-hidden="true">/</span>
                    <span class="experience-breadcrumb__current">{{ packageItem.title }}</span>
                </nav>

                <div class="experience-operator-head experience-operator-head--stack">
                    <p class="experience-operator-head__eyebrow">Package</p>
                    <h1 class="experience-operator-head__title">{{ packageItem.title }}</h1>
                    <div class="experience-operator-reviews">
                        <span class="experience-operator-reviews__stars">{{ reviewStars }}</span>
                        <strong>{{ packageItem.averageRating }}</strong>
                        <a v-if="packageItem.reviewCount" href="#package-reviews">{{ packageItem.reviewCount }} reviews</a>
                    </div>
                </div>

                <div class="experience-operator-layout experience-operator-layout--package">
                    <div class="experience-operator-main">
                        <div class="package-detail-top-grid">
                            <section ref="mosaicRef" class="experience-operator-mosaic package-detail-mosaic">
                                <button
                                    v-for="(item, idx) in heroTiles"
                                    :key="`${item.type}-${item.url}-${idx}`"
                                    type="button"
                                    class="experience-operator-mosaic__tile"
                                    :class="`experience-operator-mosaic__tile--${idx + 1}`"
                                    @click="openMedia(idx)"
                                >
                                    <video
                                        v-if="item.type === 'video'"
                                        class="experience-operator-mosaic__media"
                                        :src="item.url"
                                        autoplay
                                        loop
                                        muted
                                        playsinline
                                        preload="metadata"
                                    ></video>
                                    <img
                                        v-else
                                        class="experience-operator-mosaic__media"
                                        :src="item.url"
                                        :alt="packageItem.title"
                                        loading="eager"
                                        decoding="async"
                                    />
                                    <span v-if="idx === 4 && mediaItems.length > 5" class="experience-operator-mosaic__more">
                                        +{{ mediaItems.length - 4 }}
                                    </span>
                                </button>
                            </section>

                            <article id="booking-widget" class="experience-operator-booking package-detail-booking">
                                <div>
                                    <div class="package-detail-booking__topline">
                                        <p class="experience-operator-booking__badge">Best Seller</p>
                                        <span v-if="packageItem.duration">{{ packageItem.duration }}</span>
                                    </div>
                                    <p class="experience-operator-booking__label">Reserve package</p>
                                    <h2 v-if="packageItem.priceFrom" class="experience-operator-booking__price">
                                        {{ packageItem.priceFrom }} <span>per person</span>
                                    </h2>
                                    <p v-else class="experience-operator-booking__price experience-operator-booking__price--muted">
                                        Current pricing is available at checkout.
                                    </p>
                                </div>

                                <div v-if="page.props.flash.success" class="success-banner">
                                    {{ page.props.flash.success }}
                                </div>
                                <div v-if="page.props.flash.error" class="error-banner">
                                    {{ page.props.flash.error }}
                                </div>

                                <div class="experience-operator-cart-fields package-detail-booking__fields">
                                    <label class="field">
                                        <span>Travel Date</span>
                                        <input v-model="form.travel_date" type="date" />
                                        <small v-if="cartForm.errors.travel_date">{{ cartForm.errors.travel_date }}</small>
                                    </label>

                                    <label class="field">
                                        <span>Guests</span>
                                        <input v-model="form.guest_count" type="number" min="2" max="100" />
                                        <small v-if="cartForm.errors.guest_count">{{ cartForm.errors.guest_count }}</small>
                                    </label>

                                    <div class="experience-operator-total">
                                        <span>Estimated Total</span>
                                        <strong>{{ totalAmount }}</strong>
                                    </div>
                                </div>

                                <div class="package-detail-booking__actions">
                                    <button
                                        class="button-secondary add-cart-button"
                                        type="button"
                                        :disabled="cartForm.processing || !packageItem.priceFrom"
                                        @click="addToCart"
                                    >
                                        {{ cartForm.processing ? 'Adding...' : 'Add to Cart' }}
                                    </button>
                                    <button class="button-primary add-cart-button" type="button" @click="bookNow">
                                        Book Now
                                    </button>
                                    <a class="button-secondary add-cart-button package-detail-booking__customize" href="#customize-package">
                                        Customize Your Package
                                    </a>
                                </div>
                            </article>
                        </div>

                        <section class="detail-fact-grid" aria-label="Package quick facts">
                            <article v-for="fact in quickFacts" :key="fact.label" class="detail-fact">
                                <span>{{ fact.label }}</span>
                                <strong>{{ fact.value }}</strong>
                            </article>
                        </section>

                        <article class="experience-operator-section experience-operator-section--overview">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Package overview</span>
                                <h2>Overview</h2>
                            </div>
                            <p class="experience-operator-section__lede">{{ packageItem.description || packageItem.shortDescription }}</p>
                        </article>

                        <article v-if="packageItem.highlights?.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">What stands out</span>
                                <h2>Notable Highlights</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--chips">
                                <li v-for="highlight in packageItem.highlights" :key="highlight">{{ highlight }}</li>
                            </ul>
                        </article>

                        <article v-if="packageItem.inclusions?.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Included in your booking</span>
                                <h2>What's Included</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--checks">
                                <li v-for="item in packageItem.inclusions" :key="item">{{ item }}</li>
                            </ul>
                        </article>

                        <article v-if="packageItem.exclusions?.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Plan ahead</span>
                                <h2>What's Not Included</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--muted">
                                <li v-for="item in packageItem.exclusions" :key="item">{{ item }}</li>
                            </ul>
                        </article>

                        <article v-if="packageItem.itinerary?.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Day-by-day planning</span>
                                <h2>Itinerary</h2>
                            </div>
                            <div class="experience-operator-itinerary">
                                <article
                                    v-for="(stop, index) in packageItem.itinerary"
                                    :key="`${stop.dayLabel}-${stop.title}-${index}`"
                                    class="experience-operator-itinerary__item"
                                    :class="{ 'experience-operator-itinerary__item--open': openItineraryIndex === index }"
                                >
                                    <button
                                        type="button"
                                        class="experience-operator-itinerary__trigger"
                                        :aria-expanded="openItineraryIndex === index"
                                        :aria-controls="`itinerary-day-${index}`"
                                        @click="toggleItinerary(index)"
                                    >
                                        <span>
                                            <small>{{ stop.dayLabel || `Day ${index + 1}` }}</small>
                                            <strong>{{ stop.title }}</strong>
                                        </span>
                                        <span class="experience-operator-itinerary__chevron" aria-hidden="true"></span>
                                    </button>
                                    <div
                                        v-show="openItineraryIndex === index"
                                        :id="`itinerary-day-${index}`"
                                        class="experience-operator-itinerary__panel"
                                    >
                                        <p>{{ stop.description }}</p>
                                    </div>
                                </article>
                            </div>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Package choices</span>
                                <h2>Package Options</h2>
                            </div>
                            <div class="detail-option-grid">
                                <article v-for="option in packageOptions" :key="option.title" class="detail-option-card">
                                    <h3>{{ option.title }}</h3>
                                    <p>{{ option.copy }}</p>
                                </article>
                            </div>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Hotel &amp; package flexibility</span>
                                <h2>Flexible Package Planning</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--chips">
                                <li v-for="item in packageFlexibility" :key="item">{{ item }}</li>
                            </ul>
                        </article>

                        <article id="customize-package" class="experience-operator-section package-custom-panel">
                            <div class="package-custom-panel__copy">
                                <span class="experience-operator-section__kicker">Customize your trip</span>
                                <h2>Adjust this package around your dates, hotel, and add-ons</h2>
                                <p>
                                    Use this package as the base plan, then ask the team to tune the hotel category,
                                    transfers, visa support, flights, extra nights, or attractions before final payment.
                                </p>
                            </div>
                            <form class="package-custom-form" @submit.prevent="submitCustomPackageRequest">
                                <label>
                                    <span>Name</span>
                                    <input v-model="customPackageForm.name" type="text" autocomplete="name" />
                                    <small v-if="customPackageForm.errors.name">{{ customPackageForm.errors.name }}</small>
                                </label>
                                <label>
                                    <span>Email</span>
                                    <input v-model="customPackageForm.email" type="email" autocomplete="email" />
                                    <small v-if="customPackageForm.errors.email">{{ customPackageForm.errors.email }}</small>
                                </label>
                                <label>
                                    <span>Phone / WhatsApp</span>
                                    <input v-model="customPackageForm.phone" type="tel" autocomplete="tel" />
                                    <small v-if="customPackageForm.errors.phone">{{ customPackageForm.errors.phone }}</small>
                                </label>
                                <label>
                                    <span>Travel date</span>
                                    <input v-model="customPackageForm.travel_date" type="date" />
                                    <small v-if="customPackageForm.errors.travel_date">{{ customPackageForm.errors.travel_date }}</small>
                                </label>
                                <label>
                                    <span>Travelers</span>
                                    <input v-model="customPackageForm.guest_count" type="number" min="2" max="100" placeholder="2" />
                                    <small v-if="customPackageForm.errors.guest_count">{{ customPackageForm.errors.guest_count }}</small>
                                </label>
                                <label>
                                    <span>Hotel preference</span>
                                    <select v-model="customPackageForm.hotel_preference">
                                        <option>Flexible</option>
                                        <option>3-star hotel</option>
                                        <option>4-star hotel</option>
                                        <option>5-star hotel</option>
                                        <option>Luxury hotel</option>
                                    </select>
                                </label>
                                <label>
                                    <span>Add-ons</span>
                                    <select v-model="customPackageForm.add_ons">
                                        <option>Not sure yet</option>
                                        <option>Visa assistance</option>
                                        <option>Flights</option>
                                        <option>Travel insurance</option>
                                        <option>Extra nights</option>
                                    </select>
                                </label>
                                <label class="package-custom-form__wide">
                                    <span>Request</span>
                                    <textarea v-model="customPackageForm.message" placeholder="Tell us what you want to change."></textarea>
                                    <small v-if="customPackageForm.errors.message">{{ customPackageForm.errors.message }}</small>
                                </label>
                                <div v-if="page.props.flash.success" class="success-banner package-custom-form__wide">
                                    {{ page.props.flash.success }}
                                </div>
                                <button class="button-primary package-custom-form__wide" type="submit" :disabled="customPackageForm.processing">
                                    {{ customPackageForm.processing ? 'Sending...' : 'Send Custom Package Request' }}
                                </button>
                            </form>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Best fit</span>
                                <h2>Who This Package Is Best For</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--chips">
                                <li v-for="item in bestFor" :key="item">{{ item }}</li>
                            </ul>
                        </article>

                        <article class="experience-operator-section detail-price-note">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Quote clarity</span>
                                <h2>Final Price Depends on Travel Details</h2>
                            </div>
                            <p>
                                Hotel rates, attraction availability, travel dates, number of guests, room type, and selected
                                upgrades can affect the final quotation. The checkout form keeps the route functional, while
                                the team can reconfirm any custom changes before final operation.
                            </p>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Booking terms</span>
                                <h2>Cancellation Policy</h2>
                            </div>
                            <p>For a full refund, cancel at least 24 hours in advance of the start date of the experience.</p>
                        </article>

                        <article class="experience-operator-section package-support-strip">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Need help?</span>
                                <h2>Package support</h2>
                            </div>
                            <p>
                                If you want to confirm hotel options, timing, add-ons, or payment before checkout, contact
                                the Acute Tourism team at <strong>{{ contactPhone }}</strong> or {{ contactEmail }}.
                            </p>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Before you go</span>
                                <h2>Important Notice</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--muted">
                                <li v-for="notice in importantNotices" :key="notice">{{ notice }}</li>
                            </ul>
                        </article>

                        <article v-if="packageItem.reviews?.length" id="package-reviews" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Guest reviews</span>
                                <h2>What travelers say</h2>
                            </div>
                            <div class="experience-review-grid">
                                <article v-for="review in packageItem.reviews" :key="`${review.authorName}-${review.quote}`" class="experience-review-card">
                                    <p class="experience-review-card__stars">{{ '★'.repeat(review.rating) }}</p>
                                    <p v-if="review.title" class="experience-review-card__title">{{ review.title }}</p>
                                    <p class="experience-review-card__quote">"{{ review.quote }}"</p>
                                    <div class="experience-review-card__meta">
                                        <strong>{{ review.authorName }}</strong>
                                        <span>{{ review.tag || review.source }}</span>
                                    </div>
                                </article>
                            </div>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Common questions</span>
                                <h2>Frequently Asked Questions</h2>
                            </div>
                            <div class="experience-operator-mini-flow">
                                <details
                                    v-for="(item, index) in packageFaqs"
                                    :key="item.question"
                                    class="experience-operator-mini-flow__item"
                                    :open="index === 0"
                                >
                                    <summary>{{ item.question }}</summary>
                                    <span>{{ item.answer }}</span>
                                </details>
                            </div>
                        </article>
                    </div>

                </div>
            </div>
        </section>

        <section v-if="relatedPackages.length" class="section-block section-contrast">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Related packages</p>
                    <h2>You may also like</h2>
                </div>

                <div class="card-grid card-grid-three">
                    <article v-for="item in relatedPackages" :key="item.slug" class="info-card package-card">
                        <div v-if="item.heroImageUrl" class="card-media">
                            <img :src="item.heroImageUrl" :alt="item.title" />
                        </div>
                        <p class="card-tag">Package</p>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.summary }}</p>
                        <p class="meta-copy">{{ item.duration }}</p>
                        <Link class="button-primary card-button" :href="`/packages/${item.slug}`">View package</Link>
                    </article>
                </div>
            </div>
        </section>

        <div v-if="activeMediaItem" class="experience-lightbox" @click.self="closeMedia">
            <button type="button" class="experience-lightbox__close" @click="closeMedia">Close</button>
            <div class="experience-lightbox__dialog">
                <video
                    v-if="activeMediaItem.type === 'video'"
                    class="experience-lightbox__media"
                    :src="activeMediaItem.url"
                    controls
                    autoplay
                    playsinline
                ></video>
                <img
                    v-else
                    class="experience-lightbox__media"
                    :src="activeMediaItem.url"
                    :alt="packageItem.title"
                />
            </div>
        </div>

        <div v-if="showStickyCta" class="detail-mobile-cta">
            <div class="detail-mobile-cta__copy">
                <strong>{{ packageItem.title }}</strong>
                <span>{{ packageItem.priceFrom || 'Current price on request' }} · {{ packageItem.duration || 'Flexible duration' }}</span>
            </div>
            <div class="detail-mobile-cta__actions">
                <a class="button-secondary" href="#booking-widget">Add to Cart</a>
                <a class="button-primary" href="#booking-widget">Book Now</a>
            </div>
        </div>
    </div>
</template>
