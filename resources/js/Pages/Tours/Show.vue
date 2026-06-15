<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';
import { useMobileAutoCarousel } from '../../Composables/useMobileAutoCarousel';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    tour: Object,
    relatedTours: Array,
});

const page = usePage();

const mediaItems = computed(() => props.tour.mediaItems || []);

const heroTiles = computed(() => {
    if (!mediaItems.value.length) {
        return [];
    }

    return Array.from({ length: Math.min(5, Math.max(5, mediaItems.value.length)) }, (_, index) => mediaItems.value[index % mediaItems.value.length]);
});

const defaultImportantNotices = [
    'Please share your contact number during booking for pickup and coordination updates.',
    'Start time and exact meeting point are reconfirmed after booking.',
    'Carry valid identification where required for venue or attraction access.',
];

const importantNotices = computed(() => (
    props.tour.importantNotices?.length ? props.tour.importantNotices : defaultImportantNotices
));

const expectationSteps = computed(() => props.tour.expectationSteps || []);
const bestFor = computed(() => props.tour.bestFor || []);
const faqItems = computed(() => props.tour.faqs || []);
const cancellationPolicy = computed(() => (
    props.tour.cancellationPolicy || 'For a full refund, cancel at least 24 hours in advance of the start date of the tour.'
));
const bookingHighlights = computed(() => [
    props.tour.duration,
    props.tour.location,
    'Instant payment confirmation',
].filter(Boolean));
const quickFacts = computed(() => [
    { label: 'Duration', value: props.tour.duration || 'Flexible' },
    { label: 'Experience Type', value: props.tour.experienceType || props.tour.category || 'Tour' },
    { label: 'Transfer Option', value: props.tour.transferOption || props.tour.pickupNote || 'Transfer availability confirmed after booking' },
    { label: 'Booking Type', value: props.tour.bookingType || 'Subject to Availability' },
]);
const reviewStars = computed(() => '★★★★★');
const activeMediaIndex = ref(null);
const mosaicRef = useMobileAutoCarousel();
const activeMediaItem = computed(() => (
    activeMediaIndex.value === null ? null : mediaItems.value[activeMediaIndex.value] ?? null
));
const showStickyCta = ref(false);
let stickyCtaTimer = null;
const bookingOptions = computed(() => props.tour.bookingOptions || []);
const selectedBookingOptionKey = ref(bookingOptions.value[0]?.key || '');
const selectedBookingOption = computed(() => (
    bookingOptions.value.find((option) => option.key === selectedBookingOptionKey.value) || bookingOptions.value[0] || null
));
const unavailableDates = computed(() => new Set(props.tour.availability?.unavailableDates || []));
const unavailablePeriods = computed(() => props.tour.availability?.unavailablePeriods || []);
const selectedDateUnavailable = computed(() => {
    if (!form.travel_date) {
        return false;
    }

    if (unavailableDates.value.has(form.travel_date)) {
        return true;
    }

    return unavailablePeriods.value.some((period) => (
        period.start && period.end && form.travel_date >= period.start && form.travel_date <= period.end
    ));
});
const availabilityMessage = computed(() => (
    selectedDateUnavailable.value ? 'This date is currently unavailable. Please choose another date.' : ''
));

const form = useForm({
    travel_date: '',
    adult_count: 1,
    child_count: 0,
    guest_count: 1,
});

const cartForm = useForm({
    type: 'tour',
    slug: props.tour.slug,
    travel_date: '',
    adult_count: 1,
    child_count: 0,
    guest_count: 1,
    booking_option: selectedBookingOptionKey.value,
});

const bookingGuestCount = computed(() => {
    const adults = Math.max(0, Number.parseInt(form.adult_count, 10) || 0);
    const kids = Math.max(0, Number.parseInt(form.child_count, 10) || 0);

    return Math.max(1, adults + kids);
});

const totalAmount = computed(() => {
    const adults = Math.max(0, Number.parseInt(form.adult_count, 10) || 0) || bookingGuestCount.value;
    const kids = Math.max(0, Number.parseInt(form.child_count, 10) || 0);
    const adultAmount = Number.parseFloat(selectedBookingOption.value?.amountValue ?? props.tour.priceFromValue ?? '0') || 0;
    const childAmount = Number.parseFloat(selectedBookingOption.value?.childAmountValue ?? props.tour.childPriceFromValue ?? adultAmount) || adultAmount;
    const total = (adultAmount * adults) + (childAmount * kids);

    return `AED ${new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(total)}`;
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
    const guestCount = bookingGuestCount.value;

    if (!form.travel_date || selectedDateUnavailable.value) {
        focusBookingForm('date');
        return;
    }

    if (!guestCount) {
        focusBookingForm('guests');
        return;
    }

    cartForm.travel_date = form.travel_date;
    cartForm.adult_count = Math.max(0, Number.parseInt(form.adult_count, 10) || 0) || guestCount;
    cartForm.child_count = Math.max(0, Number.parseInt(form.child_count, 10) || 0);
    cartForm.guest_count = guestCount;
    cartForm.booking_option = selectedBookingOption.value?.key || '';
    cartForm.post('/cart', { preserveScroll: true });
};

const bookNow = () => {
    const guestCount = bookingGuestCount.value;

    if (!form.travel_date || selectedDateUnavailable.value) {
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
        adult_count: String(Math.max(0, Number.parseInt(form.adult_count, 10) || 0) || guestCount),
        child_count: String(Math.max(0, Number.parseInt(form.child_count, 10) || 0)),
    });
    if (selectedBookingOption.value?.key) {
        params.set('booking_option', selectedBookingOption.value.key);
    }

    window.location.assign(`/checkout/tours/${props.tour.slug}?${params.toString()}`);
};

const openMedia = (index) => {
    activeMediaIndex.value = index % mediaItems.value.length;
};

const closeMedia = () => {
    activeMediaIndex.value = null;
};

const showMedia = (index) => {
    if (!mediaItems.value.length) {
        return;
    }

    const total = mediaItems.value.length;
    activeMediaIndex.value = ((index % total) + total) % total;
};

const updateStickyCta = () => {
    const hero = document.querySelector('.experience-operator-mosaic') || document.querySelector('.experience-operator-head');
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
                    <Link href="/tours">Tours</Link>
                    <span class="experience-breadcrumb__sep" aria-hidden="true">/</span>
                    <span class="experience-breadcrumb__current">{{ tour.title }}</span>
                </nav>

                <div class="experience-operator-head experience-operator-head--stack">
                    <p class="experience-operator-head__eyebrow">{{ tour.category || 'Tour' }}</p>
                    <h1 class="experience-operator-head__title">{{ tour.title }}</h1>
                    <div class="experience-operator-reviews">
                        <span class="experience-operator-reviews__stars">{{ reviewStars }}</span>
                        <strong>{{ tour.averageRating }}</strong>
                        <a v-if="tour.reviewCount" href="#tour-reviews">{{ tour.reviewCount }} reviews</a>
                    </div>
                </div>

                <div class="experience-operator-layout">
                    <div class="experience-operator-main">
                        <section ref="mosaicRef" class="experience-operator-mosaic">
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
                                    :alt="tour.title"
                                    loading="eager"
                                    decoding="async"
                                />
                                <span v-if="idx === 4 && mediaItems.length > 5" class="experience-operator-mosaic__more">
                                    +{{ mediaItems.length - 4 }}
                                </span>
                            </button>
                        </section>

                        <section class="detail-fact-grid detail-fact-grid--tour-product" aria-label="Tour quick facts">
                            <article v-for="fact in quickFacts" :key="fact.label" class="detail-fact">
                                <span>{{ fact.label }}</span>
                                <strong>{{ fact.value }}</strong>
                            </article>
                        </section>

                        <article class="experience-operator-section experience-operator-section--overview">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Tour overview</span>
                                <h2>Overview</h2>
                            </div>
                            <p class="experience-operator-section__lede">{{ tour.description || tour.shortDescription }}</p>
                        </article>

                        <article v-if="tour.highlights?.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">What stands out</span>
                                <h2>Notable Highlights</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--chips">
                                <li v-for="highlight in tour.highlights" :key="highlight">{{ highlight }}</li>
                            </ul>
                        </article>

                        <article v-if="tour.inclusions?.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Included in your booking</span>
                                <h2>What's Included</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--checks">
                                <li v-for="item in tour.inclusions" :key="item">{{ item }}</li>
                            </ul>
                        </article>

                        <article v-if="tour.exclusions?.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Plan ahead</span>
                                <h2>What's Not Included</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--muted">
                                <li v-for="item in tour.exclusions" :key="item">{{ item }}</li>
                            </ul>
                        </article>

                        <article v-if="expectationSteps.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Tour flow</span>
                                <h2>What to Expect</h2>
                            </div>
                            <div class="experience-operator-mini-flow">
                                <details
                                    v-for="(step, index) in expectationSteps"
                                    :key="step.label"
                                    class="experience-operator-mini-flow__item"
                                    :open="index === 0"
                                >
                                    <summary>{{ step.label }}</summary>
                                    <span>{{ step.copy }}</span>
                                </details>
                            </div>
                        </article>

                        <article v-if="bestFor.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Good to know</span>
                                <h2>Who This Tour Is Best For</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--checks">
                                <li v-for="item in bestFor" :key="item">{{ item }}</li>
                            </ul>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Booking terms</span>
                                <h2>Cancellation Policy</h2>
                            </div>
                            <p>{{ cancellationPolicy }}</p>
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

                        <article v-if="faqItems.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Common questions</span>
                                <h2>Frequently Asked Questions</h2>
                            </div>
                            <div class="experience-operator-mini-flow">
                                <details
                                    v-for="(item, index) in faqItems"
                                    :key="item.question"
                                    class="experience-operator-mini-flow__item"
                                    :open="index === 0"
                                >
                                    <summary>{{ item.question }}</summary>
                                    <span>{{ item.answer }}</span>
                                </details>
                            </div>
                        </article>

                        <article v-if="tour.reviews?.length" id="tour-reviews" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Guest reviews</span>
                                <h2>What travelers say</h2>
                            </div>
                            <div class="experience-review-grid">
                                <article v-for="review in tour.reviews" :key="`${review.authorName}-${review.quote}`" class="experience-review-card">
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
                    </div>

                    <aside class="experience-operator-sidebar">
                        <article id="booking-widget" class="experience-operator-booking">
                            <p class="experience-operator-booking__badge">Best Seller</p>
                            <p class="experience-operator-booking__label">From</p>
                            <h2 v-if="tour.priceFrom" class="experience-operator-booking__price">
                                {{ tour.priceFrom }} <span>per person</span>
                                <span v-if="tour.childPriceFrom">Kids: {{ tour.childPriceFrom }}</span>
                            </h2>
                            <p v-else class="experience-operator-booking__price experience-operator-booking__price--muted">
                                Current pricing is available at checkout.
                            </p>
                            <p class="experience-operator-booking__copy">
                                Select your date and guest count, add this tour to cart, then use the cart checkout button.
                            </p>
                            <ul class="experience-operator-booking__summary">
                                <li v-for="item in bookingHighlights" :key="item">{{ item }}</li>
                            </ul>

                            <div v-if="page.props.flash.success" class="success-banner">
                                {{ page.props.flash.success }}
                            </div>
                            <div v-if="page.props.flash.error" class="error-banner">
                                {{ page.props.flash.error }}
                            </div>

                            <div class="experience-operator-cart-fields">
                                <label v-if="bookingOptions.length" class="field field-full">
                                    <span>Booking option</span>
                                    <select v-model="selectedBookingOptionKey">
                                        <option v-for="option in bookingOptions" :key="option.key" :value="option.key">
                                            {{ option.label }} - Adult {{ option.amount }}<template v-if="option.childAmount"> | Kids {{ option.childAmount }}</template>
                                        </option>
                                    </select>
                                    <small v-if="selectedBookingOption?.description">{{ selectedBookingOption.description }}</small>
                                </label>

                                <label class="field">
                                    <span>Travel Date</span>
                                    <input v-model="form.travel_date" type="date" />
                                    <small v-if="cartForm.errors.travel_date">{{ cartForm.errors.travel_date }}</small>
                                    <small v-else-if="availabilityMessage">{{ availabilityMessage }}</small>
                                </label>

                                <label class="field">
                                    <span>Adults (12+)</span>
                                    <input v-model="form.adult_count" type="number" min="1" max="100" />
                                    <small v-if="cartForm.errors.adult_count">{{ cartForm.errors.adult_count }}</small>
                                </label>

                                <label class="field">
                                    <span>Kids (3 to 11)</span>
                                    <input v-model="form.child_count" type="number" min="0" max="100" />
                                    <small v-if="cartForm.errors.child_count">{{ cartForm.errors.child_count }}</small>
                                </label>

                                <label class="field field-full">
                                    <span>Total participants</span>
                                    <input :value="bookingGuestCount" type="number" readonly />
                                    <small v-if="cartForm.errors.guest_count">{{ cartForm.errors.guest_count }}</small>
                                </label>

                                <div class="experience-operator-total">
                                    <span>Total</span>
                                    <strong>{{ totalAmount }}</strong>
                                </div>
                            </div>

                            <button
                                class="button-secondary add-cart-button"
                                type="button"
                                :disabled="cartForm.processing || !tour.priceFrom || selectedDateUnavailable"
                                @click="addToCart"
                            >
                                {{ cartForm.processing ? 'Adding...' : 'Add to Cart' }}
                            </button>
                            <button class="button-primary add-cart-button" type="button" :disabled="selectedDateUnavailable" @click="bookNow">
                                Book Now
                            </button>
                        </article>
                    </aside>
                </div>
            </div>
        </section>

        <section v-if="relatedTours.length" class="section-block section-contrast">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Similar tours</p>
                    <h2>You may also like</h2>
                </div>

                <div class="card-grid card-grid-three">
                    <article v-for="item in relatedTours" :key="item.slug" class="info-card package-card">
                        <div v-if="item.heroImageUrl" class="card-media">
                            <img :src="item.heroImageUrl" :alt="item.title" />
                        </div>
                        <p class="card-tag">{{ item.category || 'Tour' }}</p>
                        <h3>{{ item.title }}</h3>
                        <p class="meta-copy">{{ item.duration }}</p>
                        <p v-if="item.priceFrom" class="detail-price">{{ item.priceFrom }}</p>
                        <Link class="button-primary card-button" :href="`/tours/${item.slug}`">View tour</Link>
                    </article>
                </div>
            </div>
        </section>

        <div v-if="activeMediaItem" class="experience-lightbox" @click.self="closeMedia">
            <button type="button" class="experience-lightbox__close" @click="closeMedia">Close</button>
            <div class="experience-lightbox__dialog">
                <button
                    v-if="mediaItems.length > 1"
                    type="button"
                    class="experience-lightbox__nav experience-lightbox__nav--prev"
                    aria-label="Previous media"
                    @click="showMedia(activeMediaIndex - 1)"
                >
                    &lt;
                </button>
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
                    :alt="tour.title"
                />
                <button
                    v-if="mediaItems.length > 1"
                    type="button"
                    class="experience-lightbox__nav experience-lightbox__nav--next"
                    aria-label="Next media"
                    @click="showMedia(activeMediaIndex + 1)"
                >
                    &gt;
                </button>
            </div>
            <div v-if="mediaItems.length > 1" class="experience-lightbox__counter">
                {{ activeMediaIndex + 1 }} / {{ mediaItems.length }}
            </div>
        </div>

        <div v-if="showStickyCta" class="detail-mobile-cta">
            <div class="detail-mobile-cta__copy">
                <strong>{{ tour.title }}</strong>
                <span>{{ tour.priceFrom || 'Current price on request' }} | {{ tour.duration || 'Flexible duration' }}</span>
            </div>
            <div class="detail-mobile-cta__actions">
                <a class="button-secondary" href="#booking-widget">Add to Cart</a>
                <a class="button-primary" href="#booking-widget">Book Now</a>
            </div>
        </div>
    </div>
</template>
