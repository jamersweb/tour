<script setup>
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
            <p class="eyebrow">About the Rebuild</p>
            <h1 class="page-title">The new site will sell quality and trust, not template volume.</h1>
            <p class="page-copy">
                The existing production site mixes generic marketplace patterns with premium offers.
                This rebuild resets the brand around a narrower, better-presented product strategy.
            </p>

            <ul class="feature-list">
                <li v-for="pillar in pillars" :key="pillar">{{ pillar }}</li>
            </ul>
        </div>
    </section>
</template>
