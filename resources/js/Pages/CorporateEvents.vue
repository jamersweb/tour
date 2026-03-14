<script setup>
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
            <h1 class="page-title">A dedicated sales page for incentive and executive bookings.</h1>
            <p class="page-copy">
                Corporate travel and event revenue should not be buried inside a general tourism
                catalog. This page gives it a direct conversion path.
            </p>

            <div class="tag-row">
                <span v-for="service in services" :key="service" class="filter-chip active">{{ service }}</span>
            </div>
        </div>
    </section>
</template>
