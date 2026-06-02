<script setup>
import { computed, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';
import { useMobileAutoCarousel } from '../../Composables/useMobileAutoCarousel';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    experience: Object,
    relatedExperiences: Array,
});

const page = usePage();

const mediaItems = computed(() => props.experience.mediaItems || []);

const heroTiles = computed(() => {
    if (!mediaItems.value.length) {
        return [];
    }

    return Array.from({ length: Math.min(5, Math.max(5, mediaItems.value.length)) }, (_, index) => mediaItems.value[index % mediaItems.value.length]);
});

const defaultImportantNotices = [
    'Please share your contact number during booking for easier pickup coordination.',
    'Pickup times are reconfirmed after booking based on your location and date.',
    'Comfortable clothing and light walking shoes are recommended.',
];

const importantNotices = computed(() => (
    props.experience.importantNotices?.length ? props.experience.importantNotices : defaultImportantNotices
));

const quickFacts = computed(() => [
    { label: 'Duration', value: props.experience.duration || 'Flexible' },
    { label: 'Experience Type', value: props.experience.experienceType || props.experience.category || 'Experience' },
    { label: 'Transfer Option', value: props.experience.transferOption || props.experience.pickupNote || 'Transfer availability confirmed after booking' },
    { label: 'Booking Type', value: props.experience.bookingType || 'Subject to Availability' },
]);
const defaultExpectationSteps = [
    {
        label: 'Hotel Pickup',
        copy: 'Your driver collects you from your hotel or selected location in Dubai.',
    },
    {
        label: 'Desert Arrival',
        copy: 'Enjoy the desert scenery and take photos during the golden sunset period.',
    },
    {
        label: 'Hosted Camp Experience',
        copy: 'Relax at the camp with refreshments, dinner, and a more comfortable evening setup.',
    },
    {
        label: 'Dinner & Evening Atmosphere',
        copy: 'Enjoy your dinner experience with relaxed pacing and hosted service.',
    },
    {
        label: 'Return Transfer',
        copy: 'After the experience, you will be dropped back at your hotel or selected location.',
    },
];

const expectationSteps = computed(() => (
    props.experience.expectationSteps?.length ? props.experience.expectationSteps : defaultExpectationSteps
));

const defaultBestFor = [
    'Couples looking for a private desert evening',
    'Families who want comfort and smooth coordination',
    'Travelers who dislike crowded shared tours',
    'Guests celebrating birthdays, anniversaries, or special occasions',
    'Visitors who want a premium desert experience in Dubai',
];

const bestFor = computed(() => (
    props.experience.bestFor?.length ? props.experience.bestFor : defaultBestFor
));

const defaultFaqItems = [
    {
        question: 'Is this a private desert safari?',
        answer: 'Yes. This is designed as a private premium experience with private pickup and a more relaxed pace.',
    },
    {
        question: 'Is hotel pickup included?',
        answer: 'Yes. Hotel pickup and drop-off are included. Final pickup timing is confirmed after booking based on your location.',
    },
    {
        question: 'Can I customize the experience?',
        answer: 'Yes. Selected upgrades such as photography, celebration setup, quad biking, or private seating may be available on request.',
    },
    {
        question: 'What should I wear?',
        answer: 'Comfortable clothing and closed-toe shoes are recommended. Sunglasses are also useful for the desert.',
    },
    {
        question: 'What is the cancellation policy?',
        answer: 'You can cancel at least 24 hours before the tour start time for a full refund.',
    },
];

const faqItems = computed(() => (
    props.experience.faqs?.length ? props.experience.faqs : defaultFaqItems
));

const cancellationPolicy = computed(() => (
    props.experience.cancellationPolicy || 'For a full refund, cancel at least 24 hours in advance of the start date of the experience.'
));
const reviewStars = computed(() => '★★★★★');
const activeMediaIndex = ref(null);
const mosaicRef = useMobileAutoCarousel();
const activeMediaItem = computed(() => (
    activeMediaIndex.value === null ? null : mediaItems.value[activeMediaIndex.value] ?? null
));

const form = useForm({
    travel_date: '',
    guest_count: 1,
});

const cartForm = useForm({
    type: 'experience',
    slug: props.experience.slug,
    travel_date: '',
    guest_count: 1,
});

const totalAmount = computed(() => {
    const guestCount = Math.max(1, Number.parseInt(form.guest_count, 10) || 1);
    const rawAmount = String(props.experience.priceFrom || '0').replace(/[^0-9.]/g, '');
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
    const guestCount = Math.max(1, Number.parseInt(form.guest_count, 10) || 1);

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
    const guestCount = Math.max(1, Number.parseInt(form.guest_count, 10) || 1);

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

    window.location.assign(`/checkout/experiences/${props.experience.slug}?${params.toString()}`);
};

const openMedia = (index) => {
    activeMediaIndex.value = index % mediaItems.value.length;
};

const closeMedia = () => {
    activeMediaIndex.value = null;
};
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" :image="seo.image" />

    <div class="experience-tour-page experience-tour-page--operator">
        <section class="experience-operator-shell section-block">
            <div class="container">
                <nav class="experience-breadcrumb" aria-label="Breadcrumb">
                    <Link href="/">Home</Link>
                    <span class="experience-breadcrumb__sep" aria-hidden="true">/</span>
                    <Link href="/experiences">Experiences</Link>
                    <span class="experience-breadcrumb__sep" aria-hidden="true">/</span>
                    <span class="experience-breadcrumb__current">{{ experience.title }}</span>
                </nav>

                <div class="experience-operator-head experience-operator-head--stack">
                    <p class="experience-operator-head__eyebrow">{{ experience.category }}</p>
                    <h1 class="experience-operator-head__title">{{ experience.title }}</h1>
                    <div class="experience-operator-reviews">
                        <span class="experience-operator-reviews__stars">{{ reviewStars }}</span>
                        <strong>{{ experience.averageRating }}</strong>
                        <a v-if="experience.reviewCount" href="#experience-reviews">{{ experience.reviewCount }} reviews</a>
                    </div>
                </div>

                <div class="experience-operator-layout experience-operator-layout--package">
                    <div class="experience-operator-main">
                        <div class="experience-top-grid package-detail-top-grid">
                        <section ref="mosaicRef" class="experience-operator-mosaic package-detail-mosaic" aria-label="Experience photo gallery">
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
                                    :alt="experience.title"
                                    loading="eager"
                                    decoding="async"
                                />
                                <span v-if="idx === 4 && mediaItems.length > 5" class="experience-operator-mosaic__more">
                                    +{{ mediaItems.length - 4 }}
                                </span>
                            </button>
                        </section>

                            <article id="booking-widget" class="experience-booking-card experience-operator-booking package-detail-booking" aria-label="Booking calendar">
                                <div class="experience-booking-card__top">
                                    <div>
                                        <span class="experience-operator-booking__label">Reserve</span>
                                    </div>
                                    <div v-if="experience.priceFrom" class="experience-booking-price">
                                        <span>From</span>
                                        <strong>{{ experience.priceFrom }}</strong>
                                        <span>per person</span>
                                    </div>
                                    <p v-else class="experience-operator-booking__price experience-operator-booking__price--muted">
                                        Send an enquiry for current pricing.
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
                                        <span>Date</span>
                                        <input v-model="form.travel_date" type="date" />
                                        <small v-if="cartForm.errors.travel_date">{{ cartForm.errors.travel_date }}</small>
                                    </label>

                                    <label class="field">
                                        <span>Participants</span>
                                        <input v-model="form.guest_count" type="number" min="1" max="100" />
                                        <small v-if="cartForm.errors.guest_count">{{ cartForm.errors.guest_count }}</small>
                                    </label>

                                    <div class="experience-operator-total">
                                        <span>Estimated total</span>
                                        <strong>{{ totalAmount }}</strong>
                                    </div>
                                </div>

                                <div class="experience-booking-actions package-detail-booking__actions">
                                    <button
                                        class="button-secondary add-cart-button"
                                        type="button"
                                        :disabled="cartForm.processing || !experience.priceFrom"
                                        @click="addToCart"
                                    >
                                        {{ cartForm.processing ? 'Adding...' : 'Add to Cart' }}
                                    </button>
                                    <button class="button-primary add-cart-button" type="button" @click="bookNow">
                                        Book Now
                                    </button>
                                </div>
                            </article>
                        </div>

                        <section class="detail-fact-grid detail-fact-grid--tour-product" aria-label="Experience quick facts">
                            <article v-for="fact in quickFacts" :key="fact.label" class="detail-fact">
                                <span>{{ fact.label }}</span>
                                <strong>{{ fact.value }}</strong>
                            </article>
                        </section>

                        <article class="experience-operator-section experience-operator-section--overview">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Experience overview</span>
                                <h2>Overview</h2>
                            </div>
                            <p class="experience-operator-section__lede">{{ experience.description || experience.shortDescription }}</p>
                        </article>

                        <article v-if="experience.highlights?.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">What stands out</span>
                                <h2>Why This Safari Feels Different</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--chips">
                                <li v-for="highlight in experience.highlights" :key="highlight">{{ highlight }}</li>
                            </ul>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Included in your booking</span>
                                <h2>What's Included</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--checks">
                                <li v-for="item in experience.inclusions" :key="item">{{ item }}</li>
                            </ul>
                        </article>

                        <article v-if="experience.exclusions?.length" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Plan ahead</span>
                                <h2>What's Not Included</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--muted">
                                <li v-for="item in experience.exclusions" :key="item">{{ item }}</li>
                            </ul>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Evening flow</span>
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

                        <article class="experience-operator-section">
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
                                <span class="experience-operator-section__kicker">Need help?</span>
                                <h2>Contact Us</h2>
                            </div>
                            <p>
                                If you have questions about this tour or need help making your booking, we would be happy
                                to help. Just call <strong>(+971) 58 516 1554</strong> or email info@acutetourism.org.
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

                        <article v-if="experience.reviews?.length" id="experience-reviews" class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Guest reviews</span>
                                <h2>What travelers say</h2>
                            </div>
                            <div class="experience-review-grid">
                                <article v-for="review in experience.reviews" :key="`${review.authorName}-${review.quote}`" class="experience-review-card">
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
                    </div>

                </div>
            </div>
        </section>

        <section v-if="relatedExperiences.length" class="section-block section-contrast">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Similar tours</p>
                    <h2>You may also like</h2>
                </div>

                <div class="card-grid card-grid-three">
                    <article v-for="item in relatedExperiences" :key="item.slug" class="info-card package-card">
                        <div v-if="item.heroImageUrl" class="card-media">
                            <img :src="item.heroImageUrl" :alt="item.title" />
                        </div>
                        <p class="card-tag">{{ item.category }}</p>
                        <h3>{{ item.title }}</h3>
                        <p class="meta-copy">{{ item.duration }}</p>
                        <p v-if="item.priceFrom" class="detail-price">{{ item.priceFrom }}</p>
                        <Link class="button-primary card-button" :href="`/experiences/${item.slug}`">View tour</Link>
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
                    :alt="experience.title"
                />
            </div>
        </div>

        <div class="detail-mobile-cta">
            <div class="detail-mobile-cta__copy">
                <strong>{{ experience.title }}</strong>
                <span>{{ experience.priceFrom || 'Current price on request' }} · {{ experience.duration || 'Flexible duration' }}</span>
            </div>
            <div class="detail-mobile-cta__actions">
                <a class="button-secondary" href="#booking-widget">Add to Cart</a>
                <a class="button-primary" href="#booking-widget">Book Now</a>
            </div>
        </div>
    </div>
</template>
