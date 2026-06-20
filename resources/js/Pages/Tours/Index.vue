<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    tours: Array,
});

const activeCategory = ref('all');
const activeLocation = ref('all');
const activeSort = ref('recommended');

const normalizeKey = (value) => String(value || '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');

const uniqueOptions = (field, allLabel) => {
    const options = new Map();

    (props.tours || []).forEach((tour) => {
        const value = tour[field];
        const key = normalizeKey(value);

        if (key && !options.has(key)) {
            options.set(key, { key, label: value });
        }
    });

    return [{ key: 'all', label: allLabel }, ...options.values()];
};

const categoryOptions = computed(() => uniqueOptions('category', 'All Tours'));
const locationOptions = computed(() => uniqueOptions('location', 'All Locations'));
const numericPrice = (item) => Number.parseFloat(String(item.priceFrom || '').replace(/[^0-9.]/g, '')) || 0;

const visibleTours = computed(() => {
    const filtered = (props.tours || []).filter((item) => {
        const categoryMatch = activeCategory.value === 'all' || normalizeKey(item.category) === activeCategory.value;
        const locationMatch = activeLocation.value === 'all' || normalizeKey(item.location) === activeLocation.value;

        return categoryMatch && locationMatch;
    });

    return [...filtered].sort((a, b) => {
        if (activeSort.value === 'price-low') return numericPrice(a) - numericPrice(b);
        if (activeSort.value === 'price-high') return numericPrice(b) - numericPrice(a);
        return 0;
    });
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="listing-page">
        <section class="section-block listing-section">
            <div class="container">
                <div class="navigation-panel">
                    <div v-if="categoryOptions.length > 2" class="filter-group">
                        <div class="filter-label">Category</div>
                        <div class="filter-row" aria-label="Tour category filters">
                            <button
                                v-for="option in categoryOptions"
                                :key="option.key"
                                class="filter-chip"
                                :class="{ active: activeCategory === option.key }"
                                type="button"
                                @click="activeCategory = option.key"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>

                    <div v-if="locationOptions.length > 2" class="filter-group">
                        <div class="filter-label">Location</div>
                        <div class="filter-row" aria-label="Tour location filters">
                            <button
                                v-for="option in locationOptions"
                                :key="option.key"
                                class="filter-chip"
                                :class="{ active: activeLocation === option.key }"
                                type="button"
                                @click="activeLocation = option.key"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>

                    <div class="sort-row sort-row--simple">
                        <select v-model="activeSort" class="select-field" aria-label="Sort tours">
                            <option value="recommended">Sort by recommended</option>
                            <option value="price-low">Price: low to high</option>
                            <option value="price-high">Price: high to low</option>
                        </select>
                    </div>
                </div>

                <div class="card-grid card-grid-three">
                    <article v-for="item in visibleTours" :key="item.slug" class="info-card package-card">
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

                <div v-if="!visibleTours.length" class="pricing-note">
                    No tours match this filter yet. Choose another category or location.
                </div>
            </div>
        </section>
    </div>
</template>
