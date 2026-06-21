<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    packages: Array,
    packageFilters: Array,
});

const activeFilter = ref('all');
const activeSort = ref('recommended');

const fallbackFilterOptions = [
    { key: 'family-holidays', label: 'Family Holidays' },
    { key: 'short-stays', label: 'Short Stays' },
    { key: 'event-packages', label: 'Event Packages' },
    { key: 'luxury-packages', label: 'Luxury Packages' },
    { key: 'budget-friendly', label: 'Budget Friendly' },
];

const filterOptions = computed(() => [
    { key: 'all', label: 'All Packages' },
    ...((props.packageFilters?.length ? props.packageFilters : fallbackFilterOptions)),
]);

const packageTags = (item) => {
    const haystack = `${item.title} ${item.summary} ${item.duration} ${item.location}`.toLowerCase();
    const tags = ['all', ...(item.categories || []).map((category) => category.slug).filter(Boolean)];

    if (/family|kids|children|group/.test(haystack)) tags.push('family-holidays');
    if (/event|ufc|fight|concert|weekend/.test(haystack)) tags.push('event-packages');
    if (/luxury|premium|decadence|private|5 star|five star/.test(haystack)) tags.push('luxury-packages');
    if (/budget|friendly|value|affordable/.test(haystack)) tags.push('budget-friendly');
    if (/2 days|3 days|4 days|short|weekend/.test(haystack)) tags.push('short-stays');

    return [...new Set(tags)];
};

const trustItems = [
    { icon: '📍', label: 'Local support' },
    { icon: '📋', label: 'Clear inclusions' },
    { icon: '🧩', label: 'Customizable packages' },
    { icon: '🔒', label: 'Secure payment' },
];

const packageLabel = (item) => {
    const title = String(item.title || '').toLowerCase();

    if (title.includes('dazzling dubai')) return 'First-Time Dubai';
    if (title.includes('decadence')) return 'Best for Families';
    if (title.includes('delight')) return 'Short Break';
    if (title.includes('golden escape')) return 'Dubai + Abu Dhabi';
    if (title.includes('ufc')) return 'Event Weekend';
    if (title.includes('happy family')) return 'Family Holiday';
    if (item.categories?.length) return item.categories[0].name;
    if (packageTags(item).includes('short-stays')) return 'Short Break';

    return 'Curated Package';
};

const packageAudience = (item) => {
    const title = String(item.title || '').toLowerCase();

    if (title.includes('dazzling dubai')) return 'Best for first-time visitors';
    if (title.includes('decadence') || title.includes('happy family')) return 'Best for families';
    if (title.includes('delight')) return 'Best for short stays';
    if (title.includes('golden escape')) return 'Best for 2 emirates';
    if (title.includes('ufc')) return 'Best for event weekend';

    return item.location || 'Dubai & UAE';
};

const guideCards = [
    {
        number: '01',
        title: 'Will I miss the key Dubai highlights?',
        copy: 'Choose packages that include Burj Khalifa, desert safari, Dubai Marina, city tour, and at least one major attraction day.',
    },
    {
        number: '02',
        title: 'Is the itinerary too rushed?',
        copy: 'Short packages are best for quick highlights. Longer packages give more comfort, free time, and smoother pacing.',
    },
    {
        number: '03',
        title: 'What is actually included?',
        copy: 'Check hotel, breakfast, transfers, tour tickets, attraction entries, and whether visa or flights are separate.',
    },
    {
        number: '04',
        title: 'Can I change the hotel or activities?',
        copy: 'Most packages can be adjusted by hotel category, room type, dates, guest count, and preferred attractions.',
    },
];

const trustList = [
    'Packages are arranged by a local team that understands Dubai hotels, transfers, tours, and attraction timing',
    'You see clear inclusions before payment, so there is less confusion about what is covered',
    'Packages can be adjusted to your budget, dates, hotel preference, and travel style',
    'Final quotation is confirmed before payment, helping you avoid surprise changes later',
    'Secure payment options and written booking confirmation',
    'Options available for solo travelers, couples, families, groups, and event travelers',
];

const faqItems = [
    {
        question: 'Can packages be customized?',
        answer: 'Yes. Hotel category, duration, attractions, transfers, visa assistance, and extra nights can usually be customized based on travel dates and availability.',
    },
    {
        question: 'Are flights included?',
        answer: 'Flights are not included by default unless specifically mentioned inside the package details.',
    },
    {
        question: 'Can visa assistance be included?',
        answer: 'Yes. Visa assistance can be added depending on nationality and travel requirements.',
    },
    {
        question: 'Why does the final price vary?',
        answer: 'Hotel availability, travel dates, room type, number of guests, attraction availability, and selected add-ons can affect the final quote.',
    },
    {
        question: 'Which package is best for first-time Dubai visitors?',
        answer: 'Packages that include Dubai city tour, Burj Khalifa, desert safari, Marina dinner, and Abu Dhabi are usually best for first-time travelers.',
    },
];

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

    <div class="package-category-reference">
        <section class="category-hero">
            <div class="container hero-grid">
                <div>
                    <p class="kicker">Holiday Packages</p>
                    <h1 class="hero-title">Dubai Holiday Packages</h1>
                    <p class="hero-copy">
                        Browse ready-made packages with hotels, transfers, tours, attractions, and planning support
                        arranged in one smooth itinerary.
                    </p>
                </div>

                <aside class="hero-side-card">
                    <p class="hero-side-card__label">What to expect</p>
                    <ul>
                        <li>Hotel stay options with breakfast</li>
                        <li>Private transfers and planned activities</li>
                        <li>Flexible itinerary options</li>
                        <li>Packages suitable for solo travelers, couples, families, and groups</li>
                        <li>Customization available based on budget and travel dates</li>
                    </ul>
                </aside>
            </div>
        </section>

        <section class="trust-mini">
            <div class="container trust-mini__grid">
                <div v-for="item in trustItems" :key="item.label" class="trust-mini__item">
                    <strong><span aria-hidden="true">{{ item.icon }}</span>{{ item.label }}</strong>
                </div>
            </div>
        </section>

        <section id="package-grid" class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Holiday planning</p>
                        <h2>Choose your package style</h2>
                        <p>
                            Use filters to browse by travel style, duration, destination, or budget. Package details can
                            still be customized after you open an itinerary.
                        </p>
                    </div>
                </div>

                <div class="navigation-panel">
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
                    <div class="sort-row sort-row--simple">
                        <select v-model="activeSort" class="select-field" aria-label="Sort packages">
                            <option value="recommended">Sort by recommended</option>
                            <option value="price-low">Price: low to high</option>
                            <option value="price-high">Price: high to low</option>
                        </select>
                    </div>
                </div>

                <div class="card-grid card-grid-three">
                    <article v-for="item in visiblePackages" :key="item.slug" class="info-card package-card">
                        <div v-if="item.heroImageUrl" class="card-media">
                            <img :src="item.heroImageUrl" :alt="item.title" />
                            <span class="package-label">{{ packageLabel(item) }}</span>
                        </div>
                        <div class="card-body">
                            <p class="card-tag">Package</p>
                            <h3>{{ item.title }}</h3>
                            <p class="package-card__desc">{{ item.summary }}</p>
                            <div class="package-meta-list">
                                <span>{{ item.duration || 'Flexible duration' }}</span>
                                <span>{{ packageAudience(item) }}</span>
                            </div>
                            <p v-if="item.priceFrom" class="price-line"><span>From</span>{{ item.priceFrom }}</p>
                            <Link class="card-link" :href="`/packages/${item.slug}`">View Itinerary</Link>
                        </div>
                    </article>
                </div>

                <div v-if="!visiblePackages.length" class="pricing-note">
                    No packages match this filter yet. Choose another package style or contact Acute Tourism for a custom quote.
                </div>

                <div class="pricing-note">
                    Package prices may vary based on travel dates, hotel availability, room type, number of guests, and selected add-ons. Final quote is confirmed before payment.
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">First-time visitor guide</p>
                        <h2>Not sure which package to open first?</h2>
                        <p>These points answer the doubts most travelers have before comparing holiday packages.</p>
                    </div>
                </div>

                <div class="guide-grid">
                    <article v-for="card in guideCards" :key="card.number" class="guide-card">
                        <div class="guide-card__num">{{ card.number }}</div>
                        <h3>{{ card.title }}</h3>
                        <p>{{ card.copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="trust-strip">
            <div class="container trust-box">
                <h2>Book with more confidence, not guesswork.</h2>
                <ul class="trust-list">
                    <li v-for="item in trustList" :key="item">{{ item }}</li>
                </ul>
            </div>
        </section>

        <section class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Common questions</p>
                        <h2>Holiday package FAQs</h2>
                        <p>Answers to the questions customers usually ask before opening or comparing package itineraries.</p>
                    </div>
                </div>
                <div class="faq-list">
                    <details v-for="(item, index) in faqItems" :key="item.question" class="faq-item" :open="index === 0">
                        <summary>{{ item.question }}</summary>
                        <p>{{ item.answer }}</p>
                    </details>
                </div>
            </div>
        </section>
    </div>
</template>
