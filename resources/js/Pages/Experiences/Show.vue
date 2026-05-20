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

const importantNotices = computed(() => [
    'Please share your contact number during booking for easier pickup coordination.',
    'Pickup times are reconfirmed after booking based on your location and date.',
    'Comfortable clothing and light walking shoes are recommended.',
]);

const quickFacts = computed(() => [
    { label: 'Duration', value: props.experience.duration || 'Flexible' },
    { label: 'Location', value: props.experience.location || 'Dubai' },
    { label: 'Category', value: props.experience.category || 'Experience' },
    { label: 'Price From', value: props.experience.priceFrom || 'On request' },
]);
const expectationSteps = computed(() => [
    {
        label: 'Pickup',
        copy: 'Your driver collects you from your hotel or selected location when pickup is included.',
    },
    {
        label: 'Hosted experience',
        copy: 'The operating team manages the flow so the day feels structured without becoming rushed.',
    },
    {
        label: 'Return',
        copy: 'After the experience, you return to the agreed drop-off point with final timing confirmed after booking.',
    },
]);
const bestFor = computed(() => [
    'Travelers who want a polished Dubai experience with clear inclusions.',
    'Couples, families, and small groups who prefer direct booking and payment.',
    'Guests who want operational details reconfirmed after checkout.',
]);
const bookingHighlights = computed(() => [
    props.experience.duration,
    props.experience.location,
    'Instant payment confirmation',
].filter(Boolean));
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

const addToCart = () => {
    cartForm.travel_date = form.travel_date;
    cartForm.guest_count = Math.max(1, Number.parseInt(form.guest_count, 10) || 1);
    cartForm.post('/cart', { preserveScroll: true });
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
                                    :alt="experience.title"
                                    loading="eager"
                                    decoding="async"
                                />
                                <span v-if="idx === 4 && mediaItems.length > 5" class="experience-operator-mosaic__more">
                                    +{{ mediaItems.length - 4 }}
                                </span>
                            </button>
                        </section>

                        <section class="detail-fact-grid" aria-label="Experience quick facts">
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
                                <h2>Notable Highlights</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--chips">
                                <li v-for="highlight in experience.highlights" :key="highlight">{{ highlight }}</li>
                            </ul>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Guest flow</span>
                                <h2>What to Expect</h2>
                            </div>
                            <div class="detail-timeline">
                                <article v-for="step in expectationSteps" :key="step.label" class="detail-timeline__item">
                                    <strong>{{ step.label }}</strong>
                                    <p>{{ step.copy }}</p>
                                </article>
                            </div>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Best fit</span>
                                <h2>Who This Tour Is Best For</h2>
                            </div>
                            <ul class="experience-operator-list experience-operator-list--chips">
                                <li v-for="item in bestFor" :key="item">{{ item }}</li>
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
                                <span class="experience-operator-section__kicker">Booking terms</span>
                                <h2>Cancellation Policy</h2>
                            </div>
                            <p>For a full refund, cancel at least 24 hours in advance of the start date of the experience.</p>
                        </article>

                        <article class="experience-operator-section">
                            <div class="experience-operator-section__head">
                                <span class="experience-operator-section__kicker">Need help?</span>
                                <h2>Contact Us</h2>
                            </div>
                            <p>
                                If you have questions about this tour or need help making your booking, we would be happy to help.
                                Just call (+971) 58 516 1554 or email info@acutetourism.org.
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
                    </div>

                    <aside class="experience-operator-sidebar">
                        <article class="experience-operator-booking">
                            <p class="experience-operator-booking__badge">Best Seller</p>
                            <p class="experience-operator-booking__label">From</p>
                            <h2 v-if="experience.priceFrom" class="experience-operator-booking__price">
                                {{ experience.priceFrom }} <span>per person</span>
                            </h2>
                            <p v-else class="experience-operator-booking__price experience-operator-booking__price--muted">
                                Send an enquiry for current pricing.
                            </p>
                            <p class="experience-operator-booking__copy">
                                Select your date and guest count, add this experience to cart, then use the cart checkout button.
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
                                <label class="field">
                                    <span>Travel Date</span>
                                    <input v-model="form.travel_date" type="date" />
                                    <small v-if="cartForm.errors.travel_date">{{ cartForm.errors.travel_date }}</small>
                                </label>

                                <label class="field">
                                    <span>Guests</span>
                                    <input v-model="form.guest_count" type="number" min="1" max="100" />
                                    <small v-if="cartForm.errors.guest_count">{{ cartForm.errors.guest_count }}</small>
                                </label>

                                <div class="experience-operator-total">
                                    <span>Total</span>
                                    <strong>{{ totalAmount }}</strong>
                                </div>
                            </div>

                            <button
                                class="button-primary add-cart-button"
                                type="button"
                                :disabled="cartForm.processing || !experience.priceFrom"
                                @click="addToCart"
                            >
                                {{ cartForm.processing ? 'Adding...' : 'Add to Cart' }}
                            </button>
                            <Link class="button-secondary add-cart-button" href="/cart">View Cart</Link>
                        </article>
                    </aside>
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
                <button
                    class="button-primary"
                    type="button"
                    :disabled="cartForm.processing || !experience.priceFrom"
                    @click="addToCart"
                >
                    {{ cartForm.processing ? 'Adding...' : 'Add to Cart' }}
                </button>
                <Link class="button-secondary" href="/cart">Cart</Link>
            </div>
        </div>
    </div>
</template>
