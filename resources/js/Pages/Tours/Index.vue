<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    tours: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="listing-page">
        <section class="about-hero listing-hero">
            <div class="container about-hero__grid">
                <div>
                    <p class="about-kicker">Tours</p>
                    <h1 class="about-title">City, cultural, and private tours organized as direct bookable products.</h1>
                    <p class="about-copy">
                        Browse guided tour products that support mixed media, clearer itinerary framing,
                        and direct booking from the detail page.
                    </p>

                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Contact Acute Tourism</Link>
                        <Link class="button-secondary" href="/experiences">Browse experiences</Link>
                    </div>
                </div>

                <div class="about-card about-card--primary">
                    <p class="about-card__label">Tour focus</p>
                    <ul class="about-list about-list--tight">
                        <li>City tours and cultural itineraries</li>
                        <li>Private and guided formats</li>
                        <li>Direct payment from the detail page</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section-block listing-section">
            <div class="container">
                <div class="card-grid card-grid-three">
                    <article v-for="item in tours" :key="item.slug" class="info-card package-card">
                        <div v-if="item.heroImageUrl" class="card-media">
                            <img :src="item.heroImageUrl" :alt="item.title" />
                        </div>
                        <p class="card-tag">{{ item.category || 'Tour' }}</p>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.summary }}</p>
                        <p class="meta-copy">{{ item.duration }}<span v-if="item.location"> | {{ item.location }}</span></p>
                        <p v-if="item.priceFrom" class="price-line">{{ item.priceFrom }}</p>
                        <Link class="button-primary card-button" :href="`/tours/${item.slug}`">View tour</Link>
                    </article>
                </div>
            </div>
        </section>
    </div>
</template>
