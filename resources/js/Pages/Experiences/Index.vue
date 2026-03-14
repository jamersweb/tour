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
            <h1 class="page-title">Premium Dubai product architecture starts here.</h1>
            <p class="page-copy">
                This listing page is the replacement for the current broad template catalog. It is
                designed for curated categories, cleaner filters, and stronger conversion paths.
            </p>
        </div>
    </section>

    <section class="section-block">
        <div class="container">
            <div class="tag-row">
                <span class="filter-chip" :class="{ active: !activeCategory }">All</span>
                <span
                    v-for="category in categories"
                    :key="category"
                    class="filter-chip"
                    :class="{ active: activeCategory === category }"
                >
                    {{ category }}
                </span>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="experience in experiences" :key="experience.title" class="info-card">
                    <div v-if="experience.heroImageUrl" class="card-media">
                        <img :src="experience.heroImageUrl" :alt="experience.title" />
                    </div>
                    <p class="card-tag">{{ experience.category }}</p>
                    <h3>{{ experience.title }}</h3>
                    <p>{{ experience.summary }}</p>
                    <p>{{ experience.duration }}</p>
                    <p class="price-line">{{ experience.priceFrom }}</p>
                    <Link class="card-link" :href="`/experiences/${experience.slug}`">View experience</Link>
                </article>
            </div>
        </div>
    </section>
</template>
