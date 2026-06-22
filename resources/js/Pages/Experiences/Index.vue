<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    activeCategory: String,
    activeLocation: String,
    activeType: String,
    categories: Array,
    experiences: Array,
    locationFilters: Array,
    typeFilters: Array,
    pageTitle: String,
    pageDescription: String,
});

const locationFilter = ref(props.activeLocation || 'all');
const typeFilter = ref(props.activeType || 'all');
const activeSort = ref('recommended');
const initialVisibleCount = 16;
const visibleLimit = ref(initialVisibleCount);

const trustItems = [
    { icon: '⭐', label: 'Licensed Dubai operator' },
    { icon: '👥', label: '2,500+ customers served' },
    { icon: '🔒', label: 'Secure payment' },
    { icon: '🏷️', label: 'Best price guarantee' },
];

const defaultLocationOptions = [
    { key: 'all', label: 'All Locations' },
    { key: 'dubai', label: 'Dubai' },
    { key: 'abu-dhabi', label: 'Abu Dhabi' },
    { key: 'other-emirates', label: 'Other Emirates' },
];

const defaultTypeOptions = [
    { key: 'all', label: 'All Activities' },
    { key: 'entry-tickets', label: 'Entry Tickets' },
    { key: 'desert-safari', label: 'Desert Safari' },
    { key: 'city-tours', label: 'City Tours' },
    { key: 'water-sports', label: 'Water Sports' },
    { key: 'water-parks', label: 'Water Parks' },
    { key: 'theme-parks', label: 'Theme Parks' },
    { key: 'yacht-cruises', label: 'Yacht & Cruises' },
];

const locationOptions = computed(() => (props.locationFilters?.length ? props.locationFilters : defaultLocationOptions)
    .map((option) => ({
        ...option,
        href: option.href || (option.key === 'all' ? '/dubai-tours-and-tickets' : `/dubai-tours-and-tickets/location/${option.key}`),
    })));

const typeOptions = computed(() => (props.typeFilters?.length ? props.typeFilters : defaultTypeOptions)
    .map((option) => ({
        ...option,
        href: option.href || (option.key === 'all' ? '/dubai-tours-and-tickets' : `/dubai-tours-and-tickets/category/${option.key}`),
    })));

const guideCards = [
    {
        number: '01',
        title: 'Only have half a day?',
        copy: 'Book short city tours, attraction tickets, yacht rides, or 1-hour desert adventure add-ons that fit easily into your day.',
    },
    {
        number: '02',
        title: 'Traveling with children?',
        copy: 'Choose water parks, theme parks, aquarium-style attractions, and activities with easy access or transfer options.',
    },
    {
        number: '03',
        title: 'Want one memorable evening?',
        copy: 'Book a desert safari, dinner cruise, sunset yacht, or premium night experience for a strong Dubai memory.',
    },
];

const faqItems = [
    {
        question: 'What is the difference between entry tickets and tours?',
        answer: 'Entry tickets usually give access to an attraction only. Tours may include transfers, guide service, meals, or multiple stops, making them better for customers who want a planned experience.',
    },
    {
        question: 'Can I filter activities by location?',
        answer: 'Yes. Activities can be grouped by Dubai, Abu Dhabi, and Other Emirates to make browsing easier.',
    },
    {
        question: 'Are transfers included?',
        answer: 'Some activities include transfers while others are ticket-only. Check the product page before booking so you know exactly what is included.',
    },
    {
        question: 'Which activities are best for families?',
        answer: 'Water parks, theme parks, aquarium visits, city tours, and selected desert safaris are usually the easiest options for families.',
    },
];

const normalizedText = (item) => `${item.title} ${item.category} ${item.location || ''} ${item.summary || ''}`.toLowerCase();

const collectionSlugs = (item, group) => (item.collections || [])
    .filter((collection) => !group || collection.group === group)
    .map((collection) => collection.slug);

const activityLocation = (item) => {
    const assignedLocations = collectionSlugs(item, 'location');

    if (assignedLocations.includes('dubai')) return 'dubai';
    if (assignedLocations.includes('abu-dhabi')) return 'abu-dhabi';
    if (assignedLocations.includes('other-emirates')) return 'other-emirates';

    const text = normalizedText(item);

    if (text.includes('abu dhabi') || text.includes('ferrari world') || text.includes('yas island')) return 'abu-dhabi';
    if (text.includes('sharjah') || text.includes('ras al khaimah') || text.includes('fujairah') || text.includes('ajman')) return 'other-emirates';

    return 'dubai';
};

const activityType = (item) => {
    const assignedTypes = collectionSlugs(item, 'activity');

    if (assignedTypes.includes('entry-tickets')) return 'entry-tickets';
    if (assignedTypes.includes('desert-safari')) return 'desert-safari';
    if (assignedTypes.includes('city-tours')) return 'city-tours';
    if (assignedTypes.includes('water-sports')) return 'water-sports';
    if (assignedTypes.includes('water-parks')) return 'water-parks';
    if (assignedTypes.includes('theme-parks')) return 'theme-parks';
    if (assignedTypes.includes('yacht-cruises')) return 'yacht-cruises';
    if (assignedTypes.includes('helicopter-sky')) return 'helicopter-sky';

    const text = normalizedText(item);

    if (text.includes('yacht') || text.includes('cruise') || text.includes('marina')) return 'yacht-cruises';
    if (text.includes('helicopter') || /\bsky\b/.test(text) || text.includes('balloon')) return 'helicopter-sky';
    if (text.includes('desert') || text.includes('safari') || text.includes('quad') || text.includes('buggy')) return 'desert-safari';
    if (text.includes('city') || text.includes('landmark') || text.includes('chauffeur')) return 'city-tours';
    if (text.includes('jet ski') || text.includes('parasail') || text.includes('water sport')) return 'water-sports';
    if (text.includes('water park') || text.includes('aquaventure') || text.includes('wild wadi')) return 'water-parks';
    if (text.includes('theme park') || text.includes('ferrari') || text.includes('img world') || text.includes('warner')) return 'theme-parks';
    if (text.includes('ticket') || text.includes('entry') || text.includes('pass')) return 'entry-tickets';

    return 'entry-tickets';
};

const numericPrice = (item) => Number.parseFloat(String(item.priceFrom || '').replace(/[^0-9.]/g, '')) || 0;

const offerBadge = (item, index) => {
    const text = normalizedText(item);

    if (index === 0) return { label: 'Save 15%', tone: '' };
    if (text.includes('desert')) return { label: 'Limited Offer', tone: 'offer-badge--dark' };
    if (text.includes('aquaventure') || text.includes('water')) return { label: 'Weekend Deal', tone: '' };
    if (text.includes('ferrari')) return { label: 'Best Value', tone: 'offer-badge--light' };
    if (text.includes('buggy') || text.includes('quad')) return { label: 'Adventure Deal', tone: 'offer-badge--dark' };
    if (text.includes('yacht')) return { label: 'Best Value', tone: '' };

    return null;
};

const filteredExperiences = computed(() => {
    const filtered = (props.experiences || []).filter((item) => {
        const locationMatch = locationFilter.value === 'all' || activityLocation(item) === locationFilter.value;
        const typeMatch = typeFilter.value === 'all' || activityType(item) === typeFilter.value;

        return locationMatch && typeMatch;
    });

    return [...filtered].sort((a, b) => {
        if (activeSort.value === 'price-low') return numericPrice(a) - numericPrice(b);
        if (activeSort.value === 'price-high') return numericPrice(b) - numericPrice(a);
        return 0;
    });
});

const visibleExperiences = computed(() => filteredExperiences.value.slice(0, visibleLimit.value));
const canLoadMore = computed(() => visibleExperiences.value.length < filteredExperiences.value.length);

const loadMoreExperiences = () => {
    visibleLimit.value += initialVisibleCount;
};

watch([locationFilter, typeFilter, activeSort], () => {
    visibleLimit.value = initialVisibleCount;
});

watch(() => props.activeLocation, (value) => {
    locationFilter.value = value || 'all';
});

watch(() => props.activeType, (value) => {
    typeFilter.value = value || 'all';
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="activities-category-reference">
        <section class="category-hero">
            <div class="container hero-grid">
                <div>
                    <p class="kicker">Tours & Activities</p>
                    <h1 class="hero-title">{{ pageTitle || "Book Dubai's Best Tours, Tickets & Activities" }}</h1>
                    <p class="hero-copy">
                        {{ pageDescription || 'Compare top-rated activities by location, attraction type, price, reviews, and duration, then book the experience that fits your day.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="trust-mini">
            <div class="container trust-mini__grid">
                <div v-for="item in trustItems" :key="item.label" class="trust-mini__item">
                    <strong><span aria-hidden="true">{{ item.icon }}</span>{{ item.label }}</strong>
                </div>
            </div>
        </section>

        <section id="activity-grid" class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Activity planning</p>
                        <h2>Choose by location or activity type</h2>
                        <p>Find the right activity faster, compare trusted options, and book before your preferred time slot sells out.</p>
                    </div>
                </div>

                <div class="navigation-panel">
                    <div class="filter-group">
                        <div class="filter-label">Location</div>
                        <div class="filter-row" aria-label="Location filters">
                            <button
                                v-for="option in locationOptions"
                                :key="option.key"
                                class="filter-chip"
                                :class="{ active: locationFilter === option.key }"
                                type="button"
                                @click="locationFilter = option.key"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>

                    <div class="filter-group">
                        <div class="filter-label">Activity Type</div>
                        <div class="filter-row" aria-label="Activity type filters">
                            <button
                                v-for="option in typeOptions"
                                :key="option.key"
                                class="filter-chip"
                                :class="{ active: typeFilter === option.key }"
                                type="button"
                                @click="typeFilter = option.key"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>

                    <div class="sort-row">
                        <select v-model="activeSort" class="select-field" aria-label="Sort activities">
                            <option value="recommended">Sort by recommended</option>
                            <option value="price-low">Price: low to high</option>
                            <option value="price-high">Price: high to low</option>
                        </select>
                    </div>
                </div>

                <div class="card-grid card-grid-four">
                    <article v-for="(experience, index) in visibleExperiences" :key="`${experience.type || 'experience'}-${experience.slug}`" class="activity-card">
                        <div v-if="experience.heroImageUrl" class="card-media">
                            <img :src="experience.heroImageUrl" :alt="experience.title" />
                            <span
                                v-if="offerBadge(experience, index)"
                                class="offer-badge"
                                :class="offerBadge(experience, index).tone"
                            >
                                {{ offerBadge(experience, index).label }}
                            </span>
                        </div>
                        <div class="card-body">
                            <h3>{{ experience.title }}</h3>
                            <div class="activity-meta-list">
                                <span>{{ experience.duration || 'Flexible timing' }}</span>
                            </div>
                            <p class="price-line"><span>From</span>{{ experience.priceFrom || 'Ask' }}</p>
                            <Link class="card-link" :href="experience.href || `/experiences/${experience.slug}`">Book Now</Link>
                        </div>
                    </article>
                </div>

                <div v-if="!visibleExperiences.length" class="pricing-note">
                    No activities match this filter yet. Choose another location or activity type.
                </div>

                <div v-if="canLoadMore" class="load-more-row">
                    <button class="load-more-button" type="button" @click="loadMoreExperiences">
                        Load More
                    </button>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">First-time visitor guide</p>
                        <h2>Choose faster based on your day plan</h2>
                        <p>Pick the activity that fits your schedule, group, and travel mood.</p>
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

        <section class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Common questions</p>
                        <h2>Tours & activities FAQs</h2>
                        <p>Quick answers to help customers book the right activity with confidence.</p>
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
