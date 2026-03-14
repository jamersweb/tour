<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    hero: Object,
    homeSections: Object,
    collections: Array,
    featuredExperiences: Array,
    trustPoints: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="hero-section">
        <div class="container hero-grid">
            <div>
                <p class="eyebrow">{{ hero.eyebrow }}</p>
                <h1 class="hero-title">{{ hero.title }}</h1>
                <p class="hero-copy">{{ hero.description }}</p>

                <div class="hero-actions">
                    <Link class="button-primary" :href="hero.primaryCta.href">{{ hero.primaryCta.label }}</Link>
                    <Link class="button-secondary" :href="hero.secondaryCta.href">{{ hero.secondaryCta.label }}</Link>
                </div>
            </div>

            <div class="hero-panel">
                <p class="panel-label">{{ homeSections.trustHeading }}</p>
                <ul class="feature-list">
                    <li v-for="point in trustPoints" :key="point">{{ point }}</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">{{ homeSections.collectionsEyebrow }}</p>
                <h2>{{ homeSections.collectionsTitle }}</h2>
            </div>

            <div class="card-grid card-grid-two">
                <article v-for="collection in collections" :key="collection.slug" class="info-card">
                    <div v-if="collection.heroImageUrl" class="card-media">
                        <img :src="collection.heroImageUrl" :alt="collection.name" />
                    </div>
                    <p class="card-tag">Collection</p>
                    <h3>{{ collection.name }}</h3>
                    <p>{{ collection.summary }}</p>
                    <Link class="card-link" :href="`/collections/${collection.slug}`">Open collection</Link>
                </article>
            </div>
        </div>
    </section>

    <section class="section-block section-contrast">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">{{ homeSections.featuredEyebrow }}</p>
                <h2>{{ homeSections.featuredTitle }}</h2>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="experience in featuredExperiences" :key="experience.title" class="showcase-card">
                    <div v-if="experience.heroImageUrl" class="showcase-media">
                        <img :src="experience.heroImageUrl" :alt="experience.title" />
                    </div>
                    <div class="showcase-meta">
                        <span>{{ experience.category }}</span>
                        <span>{{ experience.tag }}</span>
                    </div>
                    <h3>{{ experience.title }}</h3>
                    <p>{{ experience.priceFrom }}</p>
                    <Link class="card-link" :href="`/experiences/${experience.slug}`">View experience</Link>
                </article>
            </div>
        </div>
    </section>
</template>
