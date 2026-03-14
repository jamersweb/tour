<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    hero: Object,
    homeSections: Object,
    collections: Array,
    featuredExperiences: Array,
    heroGallery: Array,
    packages: Array,
    trustPoints: Array,
    recommendations: Array,
    stats: Array,
});

const carouselItems = computed(() => [...props.heroGallery, ...props.heroGallery]);
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="hero-section landing-hero">
        <div class="container landing-hero-grid">
            <div class="landing-copy">
                <p class="eyebrow">{{ hero.eyebrow }}</p>
                <h1 class="hero-title">{{ hero.title }}</h1>
                <p class="hero-copy landing-copy-text">{{ hero.description }}</p>

                <div class="hero-actions">
                    <Link class="button-primary" :href="hero.primaryCta.href">{{ hero.primaryCta.label }}</Link>
                    <Link class="button-secondary" :href="hero.secondaryCta.href">{{ hero.secondaryCta.label }}</Link>
                </div>

                <div class="landing-stat-row">
                    <article v-for="stat in stats" :key="stat.label" class="landing-stat">
                        <strong>{{ stat.value }}</strong>
                        <span>{{ stat.label }}</span>
                    </article>
                </div>
            </div>

            <div class="landing-stage">
                <article v-if="hero.videoUrl" class="hero-panel hero-video-card">
                    <video class="hero-video" :src="hero.videoUrl" autoplay muted loop playsinline></video>
                </article>

                <article class="hero-panel landing-proof-card">
                    <p class="panel-label">{{ homeSections.trustHeading }}</p>
                    <ul class="feature-list">
                        <li v-for="point in trustPoints" :key="point">{{ point }}</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section v-if="heroGallery.length" class="section-block media-ribbon-section">
        <div class="container">
            <div class="media-ribbon-shell">
                <div class="media-ribbon-track">
                <Link
                    v-for="(item, index) in carouselItems"
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

    <section class="section-block">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">{{ homeSections.collectionsEyebrow }}</p>
                <h2>{{ homeSections.collectionsTitle }}</h2>
            </div>

            <div class="collection-rail">
                <Link
                    v-for="collection in collections"
                    :key="collection.slug"
                    class="collection-pill"
                    :href="`/collections/${collection.slug}`"
                >
                    <span>{{ collection.name }}</span>
                    <small>{{ collection.summary }}</small>
                </Link>
            </div>
        </div>
    </section>

    <section class="section-block section-contrast">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">{{ homeSections.featuredEyebrow }}</p>
                <h2>{{ homeSections.featuredTitle }}</h2>
                <p>
                    Sixteen homepage experiences create better discovery depth while still feeling
                    curated and premium.
                </p>
            </div>

            <div class="experience-masonry">
                <article v-for="experience in featuredExperiences" :key="experience.slug" class="experience-tile">
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

    <section class="section-block">
        <div class="container package-showcase">
            <div class="section-heading">
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
                    <p class="meta-copy">{{ item.duration }}<span v-if="item.location"> | {{ item.location }}</span></p>
                    <p v-if="item.priceFrom" class="price-line">{{ item.priceFrom }}</p>
                    <Link class="button-primary card-button" :href="`/packages/${item.slug}`">View package</Link>
                </article>
            </div>
        </div>
    </section>

    <section class="section-block recommendation-section">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">{{ homeSections.recommendationsEyebrow }}</p>
                <h2>{{ homeSections.recommendationsTitle }}</h2>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="item in recommendations" :key="item.title" class="showcase-card recommendation-card">
                    <p class="card-tag">Recommendation</p>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.copy }}</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container concierge-band">
            <div>
                <p class="eyebrow">Concierge Planning</p>
                <h2>Want the right shortlist instead of browsing everything?</h2>
                <p class="page-copy">
                    Tell us whether you want desert, yacht, city, family, or event-led planning
                    and we can point you to the best-fit options quickly.
                </p>
            </div>

            <div class="hero-actions">
                <Link class="button-primary" href="/contact">Talk to Acute</Link>
                <Link class="button-secondary" href="/packages">Browse packages</Link>
            </div>
        </div>
    </section>
</template>
