<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    contact: Object,
    hero: Object,
    visaCategories: Array,
    servicePoints: Array,
});

const selectedDestination = ref('');
const activeFilter = ref('all');
const search = ref('');

const destinationData = {
    'schengen-visa': {
        label: 'Schengen Visa',
        summary: 'Sticker visa via embassy appointment, usually requiring insurance, itinerary, funds proof, and appointment planning.',
    },
    'uk-visa': {
        label: 'UK Visitor Visa',
        summary: 'Online application and biometrics route with strong emphasis on bank statements, travel proof, and profile consistency.',
    },
    'usa-visa': {
        label: 'USA B1/B2 Visitor Visa',
        summary: 'DS-160 and interview-led visitor visa route where travel history, purpose, and interview readiness matter.',
    },
    'canada-visa': {
        label: 'Canada Visitor Visa',
        summary: 'Temporary resident visa route with biometrics guidance, financial proof review, and travel-purpose framing.',
    },
    'japan-visa': {
        label: 'Japan Tourist Visa',
        summary: 'Short-stay tourist visa preparation with itinerary, photo, bank proof, and document consistency checks.',
    },
    'australia-visa': {
        label: 'Australia Visitor Visa',
        summary: 'Visitor visa support with travel-purpose documentation, funds proof, and realistic processing expectations.',
    },
    'malaysia-visa': {
        label: 'Malaysia Visa',
        summary: 'Tourist and eVisa support for eligible UAE-based travelers with a lighter online-document route.',
    },
    'evisa-assistance': {
        label: 'E-visa Assistance',
        summary: 'Online visa support where accurate data entry, image requirements, and eligibility checks are the main risks.',
    },
};

const cardImages = {
    'schengen-visa': 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?auto=format&fit=crop&w=900&q=80',
    'uk-visa': 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=900&q=80',
    'usa-visa': 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?auto=format&fit=crop&w=900&q=80',
    'canada-visa': 'https://images.unsplash.com/photo-1503614472-8c93d56cd55d?auto=format&fit=crop&w=900&q=80',
    'japan-visa': 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=900&q=80',
    'australia-visa': 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?auto=format&fit=crop&w=900&q=80',
    'malaysia-visa': 'https://images.unsplash.com/photo-1515444744559-5f3205c7e4c7?auto=format&fit=crop&w=900&q=80',
    'evisa-assistance': 'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=900&q=80',
};

const filterOptions = [
    { key: 'all', label: 'All' },
    { key: 'europe', label: 'Europe' },
    { key: 'north-america', label: 'North America' },
    { key: 'asia', label: 'Asia' },
    { key: 'evisa', label: 'eVisa' },
    { key: 'popular', label: 'Most Requested' },
];

const tagsFor = (item) => {
    const id = item.id || '';
    const text = `${item.title} ${item.tag} ${item.copy}`.toLowerCase();
    const tags = ['all'];

    if (/schengen|uk|europe/.test(text)) tags.push('europe');
    if (/usa|canada|america/.test(text)) tags.push('north-america');
    if (/japan|malaysia|vietnam|asia/.test(text)) tags.push('asia');
    if (/e-visa|evisa|online/.test(text) || id.includes('evisa')) tags.push('evisa');
    if (/schengen|uk|usa|canada|japan/.test(text)) tags.push('popular');

    return tags;
};

const visaCards = computed(() => (props.visaCategories || []).map((item) => ({
    ...item,
    image: cardImages[item.id] || 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=900&q=80',
    tags: tagsFor(item),
})));

const visibleCards = computed(() => {
    const q = search.value.toLowerCase().trim();

    return visaCards.value.filter((item) => {
        const tagMatch = activeFilter.value === 'all' || item.tags.includes(activeFilter.value);
        const searchMatch = !q || `${item.title} ${item.tag} ${item.copy}`.toLowerCase().includes(q);

        return tagMatch && searchMatch;
    });
});

const selectedSummary = computed(() => destinationData[selectedDestination.value]);

const whatsappHref = computed(() => {
    const number = props.contact?.whatsappNumber?.replace(/[^0-9]/g, '');
    const text = encodeURIComponent('Hi, I need help with a visa application.');

    return number ? `https://wa.me/${number}?text=${text}` : null;
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="visa-page visa-services-client">
        <section class="about-hero visa-page-hero visa-services-hero">
            <div class="container about-hero__grid">
                <div>
                    <p class="about-kicker">{{ hero.eyebrow }}</p>
                    <h1 class="about-title">Visa Assistance Made Clear, Simple, and Stress-Free</h1>
                    <p class="about-copy">
                        Choose your destination, check the likely document route, and speak with Acute Tourism before
                        you invest time in the wrong visa process.
                    </p>

                    <div class="about-actions">
                        <Link class="button-primary" href="/schengen-visa">Schengen Visa</Link>
                        <a v-if="whatsappHref" class="button-secondary" :href="whatsappHref" target="_blank" rel="noreferrer">Ask on WhatsApp</a>
                    </div>
                </div>

                <article class="about-card about-card--primary visa-checker-card">
                    <p class="about-card__label">Visa checker</p>
                    <h2>Where do you want to go?</h2>
                    <label class="field">
                        <span>Destination</span>
                        <select v-model="selectedDestination">
                            <option value="">Select destination</option>
                            <option v-for="item in visaCards" :key="item.id" :value="item.id">{{ item.title }}</option>
                        </select>
                    </label>
                    <div v-if="selectedSummary" class="visa-checker-result">
                        <strong>{{ selectedSummary.label }}</strong>
                        <p>{{ selectedSummary.summary }}</p>
                    </div>
                    <a v-if="whatsappHref" class="button-primary card-button" :href="whatsappHref" target="_blank" rel="noreferrer">
                        Check eligibility
                    </a>
                </article>
            </div>
        </section>

        <section class="client-trust-strip">
            <div class="container client-trust-strip__grid">
                <div><strong>Document guidance</strong><span>Know what to prepare</span></div>
                <div><strong>Timeline clarity</strong><span>Set expectations early</span></div>
                <div><strong>UAE resident focus</strong><span>Practical local context</span></div>
                <div><strong>Human support</strong><span>Speak to a consultant</span></div>
            </div>
        </section>

        <section class="section-block visa-page-section">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Visa destinations</p>
                    <h2>Choose your destination</h2>
                    <p>
                        Each route shows visa type, typical support focus, and key documents. Click a destination to
                        open the dedicated page when available or ask the team for exact requirements.
                    </p>
                </div>

                <div class="listing-controls visa-listing-controls">
                    <div class="filter-row" aria-label="Visa filters">
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
                    <input v-model="search" class="search-field" type="search" placeholder="Search visa destination e.g. Schengen, UK, USA" />
                </div>

                <div class="visa-card-grid">
                    <article
                        v-for="item in visibleCards"
                        :id="item.id"
                        :key="item.id"
                        class="visa-destination-card"
                    >
                        <div class="visa-destination-card__media">
                            <img :src="item.image" :alt="item.title" loading="lazy" />
                            <span>{{ item.tag }}</span>
                        </div>
                        <div class="visa-destination-card__body">
                            <p class="about-card__label">{{ item.tags.includes('popular') ? 'Most requested' : 'Visa route' }}</p>
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.copy }}</p>
                            <div class="visa-meta-list">
                                <span><strong>Type:</strong> {{ item.tag }}</span>
                                <span><strong>Docs:</strong> Passport, funds proof, travel plan</span>
                            </div>
                            <Link v-if="item.href" class="button-primary card-button" :href="item.href">{{ item.cta }}</Link>
                            <a v-else-if="whatsappHref" class="button-primary card-button" :href="whatsappHref" target="_blank" rel="noreferrer">Check Requirements</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block visa-page-section visa-page-section--contrast">
            <div class="container about-columns">
                <article class="about-card">
                    <p class="eyebrow">How it works</p>
                    <h2>Simple steps to prepare your visa</h2>
                    <ul class="about-list">
                        <li>Choose your intended destination and travel window.</li>
                        <li>Share nationality, UAE residency status, and travel purpose.</li>
                        <li>Review the checklist with a consultant before submission.</li>
                        <li>Proceed with document preparation, appointment, or online application support.</li>
                    </ul>
                </article>

                <article class="about-card">
                    <p class="eyebrow">Important</p>
                    <h2>Visa approval is always decided by the destination authority.</h2>
                    <p>
                        Acute Tourism provides document guidance, file preparation, and application support. Processing
                        times are estimates and may vary by destination, season, and applicant profile.
                    </p>
                </article>
            </div>
        </section>
    </div>
</template>
