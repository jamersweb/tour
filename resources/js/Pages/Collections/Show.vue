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

    <section class="hero-section">
        <div class="container hero-grid">
            <div>
                <p class="eyebrow">Collection</p>
                <h1 class="page-title">{{ collection.title }}</h1>
                <p class="page-copy">{{ collection.description }}</p>

                <div class="hero-actions">
                    <Link class="button-primary" href="/dubai-tours-and-tickets">Browse experiences</Link>
                    <Link class="button-secondary" href="/contact">Plan this collection</Link>
                </div>
            </div>

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

            <div class="experience-masonry">
                <article v-for="experience in collection.experiences" :key="experience.title" class="experience-tile">
                    <div v-if="experience.heroImageUrl" class="card-media">
                        <img :src="experience.heroImageUrl" :alt="experience.title" />
                    </div>
                    <div class="showcase-meta">
                        <span class="card-tag-ghost">{{ experience.category }}</span>
                        <span class="card-tag-accent">{{ experience.duration }}</span>
                    </div>
                    <h3>{{ experience.title }}</h3>
                    <div class="experience-tile-footer">
                        <span>{{ experience.duration }}</span>
                        <strong>{{ experience.priceFrom }}</strong>
                    </div>
                    <Link class="button-primary card-button" :href="experience.href">{{ experience.label || 'View experience' }}</Link>
                </article>
            </div>
        </div>
    </section>
</template>
