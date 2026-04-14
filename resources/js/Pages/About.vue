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
            <p class="eyebrow">About Us</p>
            <h1 class="page-title">Travel Smart. Travel Seamless. Travel with Acute Tourism LLC</h1>
            <p class="page-copy">
                At Acute Tourism LLC, we design refined travel experiences across Dubai and the UAE, built on precision,
                expertise, and attention to detail. Every journey is thoughtfully curated to ensure comfort, efficiency,
                and a premium level of service from arrival to departure.
            </p>
            <p class="page-copy">
                From luxury hotel reservations and private airport transfers to expertly guided tours and exclusive desert
                experiences, we manage every element with professionalism and care. Our strong local network and destination
                knowledge allow us to offer priority access, competitive value, and truly personalized itineraries.
            </p>
            <p class="page-copy">
                Entrust us with your travel plans and experience Dubai with confidence, comfort, and distinction.
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
