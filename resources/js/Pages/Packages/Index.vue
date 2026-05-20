<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    packages: Array,
});

const activeFilter = ref('all');
const activeSort = ref('recommended');

const filterOptions = [
    { key: 'all', label: 'All Packages' },
    { key: 'family', label: 'Family Holidays' },
    { key: 'short', label: 'Short Stays' },
    { key: 'event', label: 'Event Packages' },
    { key: 'luxury', label: 'Luxury Packages' },
    { key: 'budget', label: 'Budget Friendly' },
];

const packageTags = (item) => {
    const haystack = `${item.title} ${item.summary} ${item.duration} ${item.location}`.toLowerCase();
    const tags = ['all'];

    if (/family|kids|children|group/.test(haystack)) tags.push('family');
    if (/event|ufc|fight|concert|weekend/.test(haystack)) tags.push('event');
    if (/luxury|premium|decadence|private|5 star|five star/.test(haystack)) tags.push('luxury');
    if (/budget|friendly|value|affordable/.test(haystack)) tags.push('budget');
    if (/2 days|3 days|4 days|short|weekend/.test(haystack)) tags.push('short');

    return tags;
};

const numericPrice = (item) => Number.parseFloat(String(item.priceFrom || '').replace(/[^0-9.]/g, '')) || 0;

const visiblePackages = computed(() => {
    const filtered = (props.packages || []).filter((item) => packageTags(item).includes(activeFilter.value));

    return [...filtered].sort((a, b) => {
        if (activeSort.value === 'price-low') return numericPrice(a) - numericPrice(b);
        if (activeSort.value === 'price-high') return numericPrice(b) - numericPrice(a);
        return 0;
    });
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="listing-page package-category-page">
        <section class="about-hero listing-hero package-category-hero">
            <div class="container about-hero__grid">
                <div>
                    <p class="about-kicker">Packages</p>
                    <h1 class="about-title">Dubai Holiday Packages, Curated for Effortless Travel</h1>
                    <p class="about-copy">
                        Browse ready-made packages with hotels, transfers, tours, attractions, and planning support
                        arranged in one smooth itinerary.
                    </p>

                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Contact Acute Tourism</Link>
                        <Link class="button-secondary" href="/experiences">Browse experiences</Link>
                    </div>
                </div>

                <div class="about-card about-card--primary">
                    <p class="about-card__label">What to expect</p>
                    <ul class="about-list about-list--tight">
                        <li>Hotel stay options with breakfast</li>
                        <li>Private transfers and planned activities</li>
                        <li>Flexible itinerary options</li>
                        <li>Packages suitable for solo travelers, couples, families, and groups</li>
                        <li>Customization available based on budget and travel dates</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="client-trust-strip">
            <div class="container client-trust-strip__grid">
                <div><strong>Local support</strong><span>Dubai-based coordination</span></div>
                <div><strong>Clear inclusions</strong><span>Know what is covered</span></div>
                <div><strong>Customizable packages</strong><span>Adjust dates and budget</span></div>
                <div><strong>Secure payment</strong><span>Checkout through the app</span></div>
            </div>
        </section>

        <section class="section-block listing-section">
            <div class="container">
                <div class="section-heading package-category-heading">
                    <div>
                        <p class="eyebrow">Holiday planning</p>
                        <h2>Choose your package style</h2>
                        <p>
                            Use filters to browse by travel style, duration, destination, or budget. Package details can
                            still be customized after you open an itinerary.
                        </p>
                    </div>
                </div>

                <div class="listing-controls">
                    <div class="filter-row" aria-label="Package filters">
                        <button
                            v-for="option in filterOptions"
                            :key="option.key"
                            class="filter-chip"
                            :class="{ active: activeFilter === option.key }"
                            type="button"
                            @click="activeFilter = option.key"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                    <select v-model="activeSort" class="select-field" aria-label="Sort packages">
                        <option value="recommended">Sort by recommended</option>
                        <option value="price-low">Price: low to high</option>
                        <option value="price-high">Price: high to low</option>
                    </select>
                </div>

                <div class="card-grid card-grid-three">
                    <article v-for="item in visiblePackages" :key="item.slug" class="info-card package-card package-card--client">
                        <div v-if="item.heroImageUrl" class="card-media package-card__media">
                            <img :src="item.heroImageUrl" :alt="item.title" />
                            <span class="package-label">{{ packageTags(item).includes('short') ? 'Short Break' : 'Curated Package' }}</span>
                        </div>
                        <p class="card-tag">Package</p>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.summary }}</p>
                        <div class="package-meta-list">
                            <span>{{ item.duration || 'Flexible duration' }}</span>
                            <span>{{ item.location || 'Dubai & UAE' }}</span>
                        </div>
                        <p v-if="item.priceFrom" class="price-line"><span>From</span>{{ item.priceFrom }}</p>
                        <Link class="button-primary card-button" :href="`/packages/${item.slug}`">View itinerary</Link>
                    </article>
                </div>

                <div v-if="!visiblePackages.length" class="pricing-note">
                    No packages match this filter yet. Choose another package style or contact Acute Tourism for a custom quote.
                </div>

                <div class="pricing-note">
                    Package prices may vary based on travel dates, hotel availability, room type, number of guests, and
                    selected add-ons. Final quote is confirmed before payment.
                </div>
            </div>
        </section>

        <section class="section-block package-guide-section">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">First-time visitor guide</p>
                        <h2>Not sure which package to open first?</h2>
                        <p>These points answer the doubts most travelers have before comparing holiday packages.</p>
                    </div>
                </div>

                <div class="guide-grid">
                    <article class="about-card">
                        <p class="about-card__label">Start with duration</p>
                        <p>Short stays work best when the route is tight. Longer holidays allow Abu Dhabi, theme parks, and slower family pacing.</p>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Match the traveler type</p>
                        <p>Families usually need gentler timing and private transfers; couples often prefer premium dinners, desert moments, and skyline views.</p>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Keep add-ons flexible</p>
                        <p>Visa, flights, insurance, extra nights, and attraction upgrades can be added once the core itinerary is clear.</p>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Confirm before payment</p>
                        <p>Hotel availability, room type, guest count, and final travel dates should be confirmed before checkout.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="client-confidence-band">
            <div class="container client-confidence-band__inner">
                <div>
                    <p class="eyebrow">Booking confidence</p>
                    <h2>Book with more confidence, not guesswork.</h2>
                    <p>
                        Acute Tourism helps convert package ideas into confirmed travel details, with a clearer route
                        from comparison to payment.
                    </p>
                </div>
                <Link class="button-primary" href="/contact">Build a custom package</Link>
            </div>
        </section>
    </div>
</template>
