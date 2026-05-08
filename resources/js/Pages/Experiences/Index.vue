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

    <div class="listing-page">
        <section class="about-hero listing-hero">
            <div class="container about-hero__grid">
                <div>
                    <p class="about-kicker">Experiences</p>
                    <h1 class="about-title">Dubai experiences presented with clearer structure and better buying context.</h1>
                    <p class="about-copy">
                        Browse private, family, desert, yacht, and city-led experiences designed to move visitors
                        into a cleaner detail page and a more direct booking flow.
                    </p>

                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Contact Acute Tourism</Link>
                        <Link class="button-secondary" href="/packages">Browse packages</Link>
                    </div>
                </div>

                <div class="about-card about-card--primary">
                    <p class="about-card__label">Browse by category</p>
                    <div class="tag-row listing-hero__tags">
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
                </div>
            </div>
        </section>

        <section class="section-block listing-section">
            <div class="container">
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
    </div>
</template>
