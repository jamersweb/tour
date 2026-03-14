<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    collection: Object,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" :image="seo.image" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">Collection</p>
            <h1 class="page-title">{{ collection.title }}</h1>
            <p class="page-copy">{{ collection.description }}</p>
            <div v-if="collection.heroImageUrl" class="page-hero-media">
                <img :src="collection.heroImageUrl" :alt="collection.title" />
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Included Experiences</p>
                <h2>{{ collection.summary }}</h2>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="experience in collection.experiences" :key="experience.title" class="info-card">
                    <div v-if="experience.heroImageUrl" class="card-media">
                        <img :src="experience.heroImageUrl" :alt="experience.title" />
                    </div>
                    <p class="card-tag">{{ experience.category }}</p>
                    <h3>{{ experience.title }}</h3>
                    <p>{{ experience.duration }}</p>
                    <p class="price-line">{{ experience.priceFrom }}</p>
                    <Link class="card-link" :href="`/experiences/${experience.slug}`">View experience</Link>
                </article>
            </div>
        </div>
    </section>
</template>
