<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    activeCategory: String,
    categories: Array,
    experiences: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container">
            <p class="eyebrow">Experiences</p>
            <h1 class="page-title">Private, premium, and concierge-led Dubai experiences.</h1>
            <p class="page-copy">
                Browse the curated collection across desert, yacht, city, and family categories.
                Each listing is designed to move buyers into a clearer product page, not a crowded
                marketplace maze.
            </p>

            <div class="hero-actions">
                <Link class="button-primary" href="/contact">Plan with concierge</Link>
                <Link class="button-secondary" href="/packages">Browse packages</Link>
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container">
            <div class="tag-row">
                <Link class="filter-chip" :class="{ active: !activeCategory }" href="/experiences">All</Link>
                <Link
                    v-for="category in categories"
                    :key="category"
                    class="filter-chip"
                    :href="`/experiences?category=${encodeURIComponent(category)}`"
                    :class="{ active: activeCategory === category }"
                >
                    {{ category }}
                </Link>
            </div>

            <div class="experience-masonry">
                <article v-for="experience in experiences" :key="experience.title" class="experience-tile">
                    <div v-if="experience.heroImageUrl" class="card-media">
                        <img :src="experience.heroImageUrl" :alt="experience.title" />
                    </div>
                    <div class="showcase-meta">
                        <span class="card-tag-ghost">{{ experience.category }}</span>
                        <span class="card-tag-accent">{{ experience.duration }}</span>
                    </div>
                    <h3>{{ experience.title }}</h3>
                    <p>{{ experience.summary }}</p>
                    <div class="experience-tile-footer">
                        <span>{{ experience.duration }}</span>
                        <strong>{{ experience.priceFrom }}</strong>
                    </div>
                    <Link class="button-primary card-button" :href="`/experiences/${experience.slug}`">View experience</Link>
                </article>
            </div>
        </div>
    </section>
</template>
