<script setup>
import { computed, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    experience: Object,
    inquiryDefaults: Object,
    relatedExperiences: Array,
});

const page = usePage();

const checkoutHref = computed(() => `/checkout/experiences/${props.experience.slug}`);

const canPayOnline = computed(
    () => Boolean(props.experience.priceFrom && page.props.payments?.networkCheckoutReady),
);

const heroGalleryUrls = computed(() => {
    const out = [];
    if (props.experience.heroImageUrl) {
        out.push(props.experience.heroImageUrl);
    }
    for (const u of props.experience.galleryImageUrls || []) {
        if (u && !out.includes(u)) {
            out.push(u);
        }
    }
    return out;
});

const activeGalleryIndex = ref(0);

watch(heroGalleryUrls, (urls) => {
    if (activeGalleryIndex.value >= urls.length) {
        activeGalleryIndex.value = 0;
    }
});

const whatsappUrl = computed(() => {
    const raw = page.props.site?.contact?.whatsappNumber;
    const number = raw ? String(raw).replace(/[^0-9]/g, '') : '';
    if (!number) {
        return null;
    }
    const title = props.experience?.title || 'an experience';
    const text = encodeURIComponent(`Hi Acute Tourism, I'm interested in ${title}.`);

    return `https://wa.me/${number}?text=${text}`;
});

const form = useForm({
    experience_slug: props.experience.slug,
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 2,
    interest: props.inquiryDefaults.interest,
    message: props.inquiryDefaults.message,
});

const submit = () => {
    form.post('/inquiries', {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'email', 'phone', 'travel_date', 'guest_count'),
    });
};
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" :image="seo.image" />

    <div class="experience-tour-page">
        <!-- Hero image + title overlay (saves vertical space vs separate header band) -->
        <section class="experience-tour-media" aria-label="Gallery">
            <div class="experience-tour-media__hero-wrap">
                <div class="experience-tour-media__main">
                    <img
                        v-if="heroGalleryUrls.length"
                        :src="heroGalleryUrls[activeGalleryIndex]"
                        class="experience-tour-media__img"
                        :alt="experience.title"
                        loading="eager"
                        decoding="async"
                    />
                    <div v-else class="experience-tour-media__placeholder">
                        <p>Gallery photos coming soon.</p>
                    </div>
                </div>

                <header class="experience-tour-media__overlay">
                    <div class="container experience-tour-header__inner">
                        <nav class="experience-breadcrumb experience-tour-media__breadcrumb" aria-label="Breadcrumb">
                            <Link href="/">Home</Link>
                            <span class="experience-breadcrumb__sep" aria-hidden="true">/</span>
                            <Link href="/experiences">Experiences</Link>
                            <span class="experience-breadcrumb__sep" aria-hidden="true">/</span>
                            <span class="experience-breadcrumb__current">{{ experience.title }}</span>
                        </nav>

                        <p class="eyebrow">{{ experience.category }}</p>
                        <h1 class="hero-title experience-tour-header__title">{{ experience.title }}</h1>
                        <p class="hero-copy experience-tour-header__lede experience-tour-header__lede--on-hero">
                            {{ experience.heroSummary || experience.shortDescription }}
                        </p>

                        <div class="tag-row experience-tour-header__tags">
                            <span v-if="experience.duration" class="filter-chip active">{{ experience.duration }}</span>
                            <span v-if="experience.location" class="filter-chip active">{{ experience.location }}</span>
                            <span v-if="experience.tag" class="filter-chip">{{ experience.tag }}</span>
                        </div>

                        <p v-if="experience.priceFrom" class="experience-tour-header__price">
                            From <strong>{{ experience.priceFrom }}</strong>
                        </p>
                    </div>
                </header>
            </div>
            <div v-if="heroGalleryUrls.length > 1" class="container experience-tour-media__thumbs-wrap">
                <div class="experience-tour-media__thumbs" role="tablist" aria-label="Gallery thumbnails">
                    <button
                        v-for="(url, idx) in heroGalleryUrls"
                        :key="`${url}-${idx}`"
                        type="button"
                        class="experience-tour-media__thumb"
                        :class="{ 'is-active': idx === activeGalleryIndex }"
                        :aria-pressed="idx === activeGalleryIndex"
                        :aria-label="`Photo ${idx + 1}`"
                        @click="activeGalleryIndex = idx"
                    >
                        <img :src="url" alt="" loading="lazy" decoding="async" />
                    </button>
                </div>
            </div>
        </section>

        <!-- Quick facts (same tokens as package metrics) -->
        <section class="experience-tour-stats" aria-label="At a glance">
            <div class="container experience-tour-stats__inner">
                <article class="landing-stat">
                    <strong>{{ experience.priceFrom || 'On request' }}</strong>
                    <span>From price</span>
                </article>
                <article class="landing-stat">
                    <strong>{{ experience.duration || 'Flexible' }}</strong>
                    <span>Duration</span>
                </article>
                <article class="landing-stat">
                    <strong>{{ experience.location || 'Dubai, UAE' }}</strong>
                    <span>Location</span>
                </article>
            </div>
        </section>

        <!-- Main + sticky booking column -->
        <section class="section-block experience-tour-body">
            <div class="container experience-tour-columns">
                <div class="experience-tour-main">
                    <article class="info-card experience-tour-card">
                        <p class="card-tag">Overview</p>
                        <h3 class="experience-tour-card__h">{{ experience.shortDescription }}</h3>
                        <div class="experience-tour-prose">
                            <p>{{ experience.description }}</p>
                        </div>
                    </article>

                    <article class="info-card experience-tour-card">
                        <p class="card-tag">Highlights</p>
                        <ul class="feature-list experience-tour-checklist">
                            <li v-for="highlight in experience.highlights" :key="highlight">{{ highlight }}</li>
                        </ul>
                    </article>

                    <article class="info-card experience-tour-card">
                        <p class="card-tag">Logistics</p>
                        <div class="experience-tour-logistics">
                            <div>
                                <strong>Pickup</strong>
                                <p>{{ experience.pickupNote || 'Pickup details are confirmed after planning.' }}</p>
                            </div>
                            <div v-if="experience.collections?.length">
                                <strong>Collections</strong>
                                <div class="tag-row">
                                    <Link
                                        v-for="collection in experience.collections"
                                        :key="collection.slug"
                                        class="filter-chip"
                                        :href="`/collections/${collection.slug}`"
                                    >
                                        {{ collection.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </article>

                    <div class="experience-tour-meta-grid">
                        <article class="info-card experience-tour-card">
                            <p class="card-tag">Included</p>
                            <ul class="feature-list">
                                <li v-for="item in experience.inclusions" :key="item">{{ item }}</li>
                            </ul>
                        </article>
                        <article class="info-card experience-tour-card">
                            <p class="card-tag">Not included</p>
                            <ul class="feature-list">
                                <li v-for="item in experience.exclusions" :key="item">{{ item }}</li>
                            </ul>
                        </article>
                    </div>
                </div>

                <aside class="experience-tour-aside experience-tour-aside--stack" aria-label="Booking and enquiry">
                    <article class="info-card hero-panel experience-tour-book-card">
                            <p class="panel-label">Book this experience</p>
                            <h2 v-if="experience.priceFrom" class="detail-price">{{ experience.priceFrom }}</h2>
                            <p v-else class="meta-copy">Request a quote for your dates.</p>
                            <p class="meta-copy experience-tour-book-card__hint">
                                Secure hosted payment when online checkout is enabled, or reach us by enquiry.
                            </p>
                            <div class="hero-actions experience-tour-book-card__actions">
                                <Link v-if="canPayOnline" class="button-primary" :href="checkoutHref">Buy now</Link>
                                <a v-else class="button-primary" href="#experience-inquiry">Book now</a>
                                <a
                                    v-if="canPayOnline"
                                    class="button-secondary"
                                    href="#experience-inquiry-form"
                                >
                                    Request availability
                                </a>
                                <a
                                    v-else-if="whatsappUrl"
                                    class="button-secondary"
                                    :href="whatsappUrl"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    WhatsApp
                                </a>
                                <Link v-else class="button-secondary" href="/contact">Contact us</Link>
                            </div>
                        </article>

                        <article id="experience-inquiry" class="info-card inquiry-card experience-tour-card experience-tour-inquiry-aside">
                            <p class="card-tag">{{ canPayOnline ? 'Enquiry' : 'Book this experience' }}</p>
                            <h3 class="experience-tour-card__h">
                                {{ canPayOnline ? 'Questions or a tailored quote?' : 'Send a booking enquiry' }}
                            </h3>
                            <p class="hero-copy experience-tour-inquiry-aside__intro">
                                <template v-if="canPayOnline">
                                    You can <strong>pay online</strong> from the buttons above. Use this form if you need
                                    custom dates, groups, or details before paying.
                                </template>
                                <template v-else>
                                    Tell us your dates and group size for
                                    <strong>{{ experience.title }}</strong>
                                    — we’ll confirm availability, pricing, and how to pay.
                                </template>
                            </p>

                            <div v-if="canPayOnline" class="hero-actions inquiry-book-cta experience-tour-inquiry-aside__mini-cta">
                                <Link class="button-primary" :href="checkoutHref">Buy now</Link>
                                <a class="button-secondary" href="#experience-inquiry-form">Message us first</a>
                            </div>
                            <p v-if="canPayOnline" class="inquiry-or-divider">or fill the form</p>

                            <div v-if="page.props.flash.success" class="success-banner">
                                {{ page.props.flash.success }}
                            </div>

                            <form id="experience-inquiry-form" class="lead-form experience-tour-inquiry-aside__form" @submit.prevent="submit">
                                <div class="form-grid">
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

                                    <label class="field">
                                        <span>Interest</span>
                                        <select v-model="form.interest">
                                            <option
                                                v-for="option in page.props.site.interestOptions"
                                                :key="option"
                                                :value="option"
                                            >
                                                {{ option }}
                                            </option>
                                        </select>
                                        <small v-if="form.errors.interest">{{ form.errors.interest }}</small>
                                    </label>
                                </div>

                                <label class="field">
                                    <span>Message</span>
                                    <textarea v-model="form.message" rows="4"></textarea>
                                    <small v-if="form.errors.message">{{ form.errors.message }}</small>
                                </label>

                                <button class="button-primary" type="submit" :disabled="form.processing">
                                    {{
                                        form.processing
                                            ? 'Sending...'
                                            : canPayOnline
                                              ? 'Send message'
                                              : 'Send booking enquiry'
                                    }}
                                </button>
                            </form>
                        </article>
                </aside>
            </div>
        </section>
    </div>

    <section v-if="relatedExperiences.length" class="section-block section-contrast">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Recommended Next</p>
                <h2>You may also like</h2>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="item in relatedExperiences" :key="item.slug" class="experience-tile">
                    <div v-if="item.heroImageUrl" class="showcase-media experience-tile-media">
                        <img :src="item.heroImageUrl" :alt="item.title" />
                    </div>
                    <div class="showcase-meta">
                        <span class="card-tag-ghost">{{ item.category }}</span>
                        <span class="card-tag-accent">{{ item.duration }}</span>
                    </div>
                    <h3>{{ item.title }}</h3>
                    <div class="experience-tile-footer">
                        <span>{{ item.category }}</span>
                        <strong>{{ item.priceFrom }}</strong>
                    </div>
                    <Link class="button-primary card-button" :href="`/experiences/${item.slug}`">View experience</Link>
                </article>
            </div>
        </div>
    </section>
</template>
