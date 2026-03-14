<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    packages: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container">
            <p class="eyebrow">Packages</p>
            <h1 class="page-title">Event-led and stay-inclusive packages for higher-intent travel.</h1>
            <p class="page-copy">
                Packages combine ticketed experiences, accommodation, and planning support into a
                cleaner conversion format for special events and short-stay itineraries.
            </p>

            <div class="card-grid card-grid-three">
                <article v-for="item in packages" :key="item.slug" class="info-card">
                    <div v-if="item.heroImageUrl" class="card-media">
                        <img :src="item.heroImageUrl" :alt="item.title" />
                    </div>
                    <p class="card-tag">Package</p>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.summary }}</p>
                    <p class="meta-copy">{{ item.duration }}<span v-if="item.location"> • {{ item.location }}</span></p>
                    <p v-if="item.priceFrom" class="price-line">{{ item.priceFrom }}</p>
                    <Link class="card-link" :href="`/packages/${item.slug}`">View package</Link>
                </article>
            </div>
        </div>
    </section>
</template>
