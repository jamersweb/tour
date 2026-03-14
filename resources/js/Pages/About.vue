<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    pillars: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">About Acute</p>
            <h1 class="page-title">Built around service clarity, premium presentation, and curated buying paths.</h1>
            <p class="page-copy">
                Acute Tourism is being rebuilt to focus on higher-intent Dubai experiences, better
                concierge support, and a cleaner premium brand than a generic travel catalog.
            </p>

            <div class="hero-actions">
                <Link class="button-primary" href="/experiences">Explore experiences</Link>
                <Link class="button-secondary" href="/contact">Talk to Acute</Link>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="pillar in pillars" :key="pillar" class="showcase-card recommendation-card">
                    <p class="card-tag">Brand Pillar</p>
                    <h3>{{ pillar }}</h3>
                </article>
            </div>
        </div>
    </section>
</template>
