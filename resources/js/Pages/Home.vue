<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const page = usePage();

const props = defineProps({
    seo: Object,
    hero: Object,
    homeSections: Object,
    collections: Array,
    featuredExperiences: Array,
    mustDoExperiences: Array,
    topRatedExperiences: Array,
    heroGallery: Array,
    packages: Array,
    recommendations: Array,
    serviceFocus: Array,
    testimonials: Array,
});

const videoRef = ref(null);
const mobileMediaRibbon = ref(
    typeof window !== 'undefined' && window.matchMedia('(max-width: 768px)').matches,
);
const reduceMotion = ref(
    typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches,
);

/** Doubled list only for desktop marquee; single pass for swipe / reduced-motion. */
const ribbonItems = computed(() => {
    if (mobileMediaRibbon.value || reduceMotion.value) {
        return props.heroGallery;
    }

    return [...props.heroGallery, ...props.heroGallery];
});

const showTopRated = computed(
    () => props.topRatedExperiences && props.topRatedExperiences.length > 0,
);

function serviceFocusWhatsappUrl(item) {
    const raw = page.props.site?.contact?.whatsappNumber;
    const number = raw ? String(raw).replace(/[^0-9]/g, '') : '';
    if (!number) {
        return null;
    }
    const text = encodeURIComponent(
        `Hi Acute Tourism, I'd like to book or enquire about ${item.title}.`,
    );

    return `https://wa.me/${number}?text=${text}`;
}

let detachMediaListeners = () => {};

onMounted(() => {
    const mqMobile = window.matchMedia('(max-width: 768px)');
    const mqReduce = window.matchMedia('(prefers-reduced-motion: reduce)');

    const syncMobile = () => {
        mobileMediaRibbon.value = mqMobile.matches;
    };

    const syncReduce = () => {
        reduceMotion.value = mqReduce.matches;
        if (mqReduce.matches) {
            const el = videoRef.value;
            el?.pause?.();
        }
    };

    syncMobile();
    syncReduce();

    if (mqReduce.matches) {
        const el = videoRef.value;
        el?.pause?.();
    }

    mqMobile.addEventListener('change', syncMobile);
    mqReduce.addEventListener('change', syncReduce);

    detachMediaListeners = () => {
        mqMobile.removeEventListener('change', syncMobile);
        mqReduce.removeEventListener('change', syncReduce);
    };
});

onUnmounted(() => detachMediaListeners());
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <!-- Full-viewport video hero -->
    <section class="home-hero-video" aria-label="Welcome">
        <div class="home-hero-video__media">
            <video
                v-if="hero.videoUrl"
                ref="videoRef"
                class="home-hero-video__el"
                :src="hero.videoUrl"
                :poster="hero.heroImageUrl || undefined"
                autoplay
                muted
                loop
                playsinline
            ></video>
            <img
                v-else
                class="home-hero-video__el home-hero-video__el--img"
                :src="hero.heroImageUrl"
                :alt="hero.title"
            />
        </div>
        <div class="home-hero-video__scrim" aria-hidden="true"></div>
        <div class="home-hero-video__content">
            <p class="home-hero-video__eyebrow">{{ hero.eyebrow }}</p>
            <h1 class="home-hero-video__title">{{ hero.title }}</h1>
            <p class="home-hero-video__lead">{{ hero.description }}</p>
            <div class="home-hero-video__actions">
                <Link class="button-primary home-hero-video__btn-primary" :href="hero.primaryCta.href">
                    {{ hero.primaryCta.label }}
                </Link>
                <Link class="button-secondary home-hero-video__btn-secondary" :href="hero.secondaryCta.href">
                    {{ hero.secondaryCta.label }}
                </Link>
                <Link
                    v-if="hero.tertiaryCta"
                    class="button-secondary home-hero-video__btn-secondary"
                    :href="hero.tertiaryCta.href"
                >
                    {{ hero.tertiaryCta.label }}
                </Link>
            </div>
        </div>
    </section>

    <!-- 1: white — Curated Travel Moments -->
    <section v-if="heroGallery.length" class="section-strip section-strip--light media-ribbon-section">
        <div class="container">
            <div class="section-heading section-heading--home">
                <p class="eyebrow">{{ homeSections.ribbonEyebrow }}</p>
                <h2>{{ homeSections.ribbonTitle }}</h2>
            </div>
            <div
                class="media-ribbon-shell"
                tabindex="0"
                role="region"
                :aria-label="`${homeSections.ribbonTitle}: swipe sideways for more experiences`"
            >
                <div class="media-ribbon-track">
                    <Link
                        v-for="(item, index) in ribbonItems"
                        :key="`${item.slug}-${index}`"
                        class="media-ribbon-card"
                        :href="`/experiences/${item.slug}`"
                    >
                        <img :src="item.image" :alt="item.title" />
                        <div class="media-ribbon-overlay">
                            <span class="media-ribbon-tag">{{ item.tag }}</span>
                            <strong>{{ item.title }}</strong>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- 2: royal — Explore Other Emirates -->
    <section class="section-strip section-strip--royal">
        <div class="container">
            <div class="section-heading section-heading--home section-heading--on-dark">
                <p class="eyebrow">{{ homeSections.collectionsEyebrow }}</p>
                <h2>{{ homeSections.collectionsTitle }}</h2>
            </div>
            <div class="collection-rail collection-rail--home">
                <Link
                    v-for="collection in collections"
                    :key="collection.slug"
                    class="collection-pill collection-pill--on-dark"
                    :href="collection.href || `/collections/${collection.slug}`"
                >
                    <span>{{ collection.name }}</span>
                    <small>{{ collection.summary }}</small>
                </Link>
            </div>
        </div>
    </section>

    <!-- 3: white — Dubai's Must-Do (service pillars) -->
    <section class="section-strip section-strip--light">
        <div class="container">
            <div class="section-heading section-heading--home">
                <p class="eyebrow">{{ homeSections.mustDoEyebrow }}</p>
                <h2>{{ homeSections.mustDoTitle }}</h2>
            </div>
            <div class="card-grid card-grid-three">
                <article
                    v-for="item in serviceFocus"
                    :key="item.title"
                    class="showcase-card recommendation-card service-focus-card"
                >
                    <p class="card-tag">{{ item.tag }}</p>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.copy }}</p>
                    <div class="service-focus-card__actions">
                        <Link class="button-primary card-button" :href="item.href">{{ item.cta }}</Link>
                        <a
                            v-if="serviceFocusWhatsappUrl(item)"
                            class="button-secondary card-button service-focus-card__whatsapp"
                            :href="serviceFocusWhatsappUrl(item)"
                            target="_blank"
                            rel="noreferrer"
                        >
                            Book now — WhatsApp
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- 4: royal — Top-Rated Dubai Experiences -->
    <section class="section-strip section-strip--royal home-top-rated-section">
        <div class="container">
            <div class="section-heading section-heading--home section-heading--on-dark">
                <p class="eyebrow">{{ homeSections.topRatedEyebrow }}</p>
                <h2>{{ homeSections.topRatedTitle }}</h2>
            </div>
            <div v-if="showTopRated" class="experience-masonry experience-masonry--on-dark">
                <article v-for="experience in topRatedExperiences" :key="experience.slug" class="experience-tile experience-tile--on-dark">
                    <div v-if="experience.heroImageUrl" class="showcase-media experience-tile-media">
                        <img :src="experience.heroImageUrl" :alt="experience.title" />
                    </div>
                    <div class="showcase-meta">
                        <span class="card-tag-ghost">{{ experience.category }}</span>
                        <span class="card-tag-accent">{{ experience.tag || experience.duration }}</span>
                    </div>
                    <h3>{{ experience.title }}</h3>
                    <p>{{ experience.summary }}</p>
                    <div class="experience-tile-footer">
                        <span>{{ experience.location || experience.duration }}</span>
                        <strong>{{ experience.priceFrom }}</strong>
                    </div>
                    <Link class="button-primary card-button" :href="`/experiences/${experience.slug}`">View experience</Link>
                </article>
            </div>
            <div v-else class="experience-masonry experience-masonry--on-dark">
                <article
                    v-for="experience in mustDoExperiences"
                    :key="`fallback-${experience.slug}`"
                    class="experience-tile experience-tile--on-dark"
                >
                    <div v-if="experience.heroImageUrl" class="showcase-media experience-tile-media">
                        <img :src="experience.heroImageUrl" :alt="experience.title" />
                    </div>
                    <div class="showcase-meta">
                        <span class="card-tag-ghost">{{ experience.category }}</span>
                        <span class="card-tag-accent">{{ experience.tag || experience.duration }}</span>
                    </div>
                    <h3>{{ experience.title }}</h3>
                    <p>{{ experience.summary }}</p>
                    <div class="experience-tile-footer">
                        <span>{{ experience.location || experience.duration }}</span>
                        <strong>{{ experience.priceFrom }}</strong>
                    </div>
                    <Link class="button-primary card-button" :href="`/experiences/${experience.slug}`">View experience</Link>
                </article>
            </div>
        </div>
    </section>

    <!-- 5: white — Packages -->
    <section class="section-strip section-strip--light">
        <div class="container package-showcase">
            <div class="section-heading section-heading--home">
                <p class="eyebrow">{{ homeSections.packagesEyebrow }}</p>
                <h2>{{ homeSections.packagesTitle }}</h2>
            </div>
            <div class="card-grid card-grid-three">
                <article v-for="item in packages" :key="item.slug" class="info-card package-card">
                    <div v-if="item.heroImageUrl" class="card-media">
                        <img :src="item.heroImageUrl" :alt="item.title" />
                    </div>
                    <p class="card-tag">Package</p>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.summary }}</p>
                    <p class="meta-copy">
                        {{ item.duration }}<span v-if="item.location"> | {{ item.location }}</span>
                    </p>
                    <p v-if="item.priceFrom" class="price-line">{{ item.priceFrom }}</p>
                    <Link class="button-primary card-button" :href="`/packages/${item.slug}`">View package</Link>
                </article>
            </div>
        </div>
    </section>

    <!-- 6: royal — Visa & travel -->
    <section class="section-strip section-strip--royal">
        <div class="container">
            <div class="section-heading section-heading--home section-heading--on-dark">
                <p class="eyebrow">{{ homeSections.recommendationsEyebrow }}</p>
                <h2>{{ homeSections.recommendationsTitle }}</h2>
            </div>
            <div class="card-grid card-grid-three">
                <article
                    v-for="item in recommendations"
                    :key="item.title"
                    class="showcase-card recommendation-card visa-home-card visa-home-card--on-dark"
                >
                    <p class="card-tag">{{ item.tag }}</p>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.summary }}</p>
                    <Link class="button-primary card-button" :href="item.href">View service</Link>
                </article>
            </div>
        </div>
    </section>

    <!-- 7: white — Company trust -->
    <section class="section-strip section-strip--light">
        <div class="container concierge-band concierge-band--home">
            <div>
                <p class="eyebrow">{{ homeSections.companyEyebrow }}</p>
                <h2 class="section-heading-title">{{ homeSections.companyTitle }}</h2>
                <p class="page-copy">{{ homeSections.companyCopy }}</p>
            </div>
            <div class="hero-actions">
                <Link class="button-primary" href="/visa-services">Visa services</Link>
                <Link class="button-secondary" href="/packages">Browse packages</Link>
            </div>
        </div>
    </section>

    <!-- 8: royal — Reviews -->
    <section class="section-strip section-strip--royal section-strip--reviews">
        <div class="container">
            <div class="section-heading section-heading--home section-heading--on-dark">
                <p class="eyebrow">{{ homeSections.testimonialsEyebrow }}</p>
                <h2>{{ homeSections.testimonialsTitle }}</h2>
            </div>
            <div class="card-grid card-grid-three">
                <article v-for="t in testimonials" :key="t.name" class="testimonial-card">
                    <p class="testimonial-card__quote">“{{ t.quote }}”</p>
                    <p class="testimonial-card__name">{{ t.name }}</p>
                    <p class="testimonial-card__tag">{{ t.tag }}</p>
                </article>
            </div>
        </div>
    </section>
</template>
