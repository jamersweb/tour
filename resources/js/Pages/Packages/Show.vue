<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    packageItem: Object,
    relatedPackages: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" :image="seo.image" />

    <section class="hero-section experience-hero">
        <div class="container experience-hero-grid">
            <div class="experience-story">
                <p class="eyebrow">Package</p>
                <h1 class="hero-title">{{ packageItem.title }}</h1>
                <p class="hero-copy">{{ packageItem.shortDescription }}</p>

                <div class="tag-row">
                    <span v-if="packageItem.duration" class="filter-chip active">{{ packageItem.duration }}</span>
                    <span v-if="packageItem.location" class="filter-chip active">{{ packageItem.location }}</span>
                    <span v-if="packageItem.groupSize" class="filter-chip active">Group {{ packageItem.groupSize }}</span>
                    <span v-if="packageItem.days && packageItem.nights" class="filter-chip active">
                        {{ packageItem.days }}D / {{ packageItem.nights }}N
                    </span>
                </div>

                <div class="experience-metrics">
                    <article class="landing-stat">
                        <strong>{{ packageItem.priceFrom || 'On request' }}</strong>
                        <span>Package rate</span>
                    </article>
                    <article class="landing-stat">
                        <strong>{{ packageItem.duration || 'Flexible' }}</strong>
                        <span>Duration</span>
                    </article>
                    <article class="landing-stat">
                        <strong>{{ packageItem.location || 'Dubai / Abu Dhabi' }}</strong>
                        <span>Destination</span>
                    </article>
                </div>
            </div>

            <div class="experience-stage">
                <div v-if="packageItem.heroImageUrl" class="page-hero-media experience-hero-media">
                    <img :src="packageItem.heroImageUrl" :alt="packageItem.title" />
                </div>

                <article class="hero-panel experience-service-card">
                    <p class="panel-label">Package Booking</p>
                    <h2 v-if="packageItem.priceFrom" class="detail-price">{{ packageItem.priceFrom }}</h2>
                    <p v-if="packageItem.salePrice" class="meta-copy">Reference sale price: {{ packageItem.salePrice }}</p>
                    <p class="meta-copy">
                        Built for event-led trips, short-stay planning, and cleaner buyer intent
                        than one-off product browsing.
                    </p>

                    <div class="hero-actions">
                        <Link class="button-primary" :href="`/checkout/packages/${packageItem.slug}`">Pay online</Link>
                        <Link class="button-secondary" href="/contact">Request this package</Link>
                        <Link class="button-secondary" href="/packages">Back to packages</Link>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container detail-grid">
            <div class="detail-stack">
                <article class="info-card package-card">
                    <p class="card-tag">Overview</p>
                    <h3>{{ packageItem.shortDescription }}</h3>
                    <p>{{ packageItem.description }}</p>
                </article>

                <article v-if="packageItem.galleryImageUrls.length" class="info-card package-card">
                    <p class="card-tag">Gallery</p>
                    <div class="gallery-grid">
                        <div v-for="image in packageItem.galleryImageUrls" :key="image" class="gallery-card">
                            <img :src="image" :alt="packageItem.title" />
                        </div>
                    </div>
                </article>

                <article v-if="packageItem.itinerary.length" class="info-card package-card">
                    <p class="card-tag">Itinerary</p>
                    <div class="detail-stack">
                        <div v-for="stop in packageItem.itinerary" :key="`${stop.dayLabel}-${stop.title}`" class="itinerary-item">
                            <p class="card-tag">{{ stop.dayLabel }}</p>
                            <h3>{{ stop.title }}</h3>
                            <div v-if="stop.image" class="card-media">
                                <img :src="stop.image" :alt="stop.title" />
                            </div>
                            <p>{{ stop.description }}</p>
                        </div>
                    </div>
                </article>
            </div>

            <div class="detail-stack">
                <article class="info-card package-card">
                    <p class="card-tag">Highlights</p>
                    <ul class="feature-list">
                        <li v-for="highlight in packageItem.highlights" :key="highlight">{{ highlight }}</li>
                    </ul>
                </article>

                <article class="info-card package-card">
                    <p class="card-tag">Inclusions</p>
                    <ul class="feature-list">
                        <li v-for="item in packageItem.inclusions" :key="item">{{ item }}</li>
                    </ul>
                </article>

                <article class="info-card package-card">
                    <p class="card-tag">Exclusions</p>
                    <ul class="feature-list">
                        <li v-for="item in packageItem.exclusions" :key="item">{{ item }}</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section v-if="relatedPackages.length" class="section-block section-contrast">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Related Packages</p>
                <h2>More stay-inclusive and event-led products.</h2>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="item in relatedPackages" :key="item.slug" class="info-card package-card">
                    <div v-if="item.heroImageUrl" class="card-media">
                        <img :src="item.heroImageUrl" :alt="item.title" />
                    </div>
                    <p class="card-tag">Package</p>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.summary }}</p>
                    <p class="meta-copy">{{ item.duration }}</p>
                    <Link class="button-primary card-button" :href="`/packages/${item.slug}`">View package</Link>
                </article>
            </div>
        </div>
    </section>
</template>
