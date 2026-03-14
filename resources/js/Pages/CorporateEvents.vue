<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    services: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">Corporate Events</p>
            <h1 class="page-title">Executive hosting, incentive travel, and group-led premium planning.</h1>
            <p class="page-copy">
                Corporate travel should have a direct sales path with clearer service language,
                faster follow-up, and products that fit incentive and executive requirements.
            </p>

            <div class="hero-actions">
                <Link class="button-primary" href="/contact">Request a corporate proposal</Link>
                <Link class="button-secondary" href="/experiences">Browse experiences</Link>
            </div>

            <div class="tag-row">
                <span v-for="service in services" :key="service" class="filter-chip active">{{ service }}</span>
            </div>
        </div>
    </section>
</template>
