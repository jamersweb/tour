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
const selectedNationality = ref('');
const activeFilter = ref('all');
const search = ref('');

const destinationData = {
    'schengen-visa': {
        label: 'Schengen Visa (Europe)',
        dest: 'Multiple Schengen countries - sticker visa via embassy appointment',
        chips: [
            { text: '15-30 working days', amber: true },
            { text: 'Embassy appointment required' },
            { text: 'Insurance mandatory' },
        ],
    },
    'uk-visa': {
        label: 'UK Visitor Visa',
        dest: 'United Kingdom - standard visitor visa online + biometrics',
        chips: [
            { text: '8-15 working days' },
            { text: 'Online application + VFS' },
            { text: 'Strong bank statement needed', amber: true },
        ],
    },
    'usa-visa': {
        label: 'USA B1/B2 Visitor Visa',
        dest: 'United States - interview required at US Embassy Dubai',
        chips: [
            { text: '30-90 days (varies)', amber: true },
            { text: 'DS-160 + interview required' },
            { text: 'Strong travel history helps' },
        ],
    },
    'canada-visa': {
        label: 'Canada Visitor Visa',
        dest: 'Canada - temporary resident visa + biometrics',
        chips: [
            { text: '60-120 working days', amber: true },
            { text: 'Biometrics required' },
            { text: 'Strong funds proof needed' },
        ],
    },
    'japan-visa': {
        label: 'Japan Tourist Visa',
        dest: 'Japan - short-stay tourist visa via embassy',
        chips: [
            { text: '5-10 working days' },
            { text: 'Day-by-day itinerary required' },
            { text: 'Embassy appointment' },
        ],
    },
    'australia-visa': {
        label: 'Australia Visitor Visa',
        dest: 'Australia - subclass 600 visitor visa, online',
        chips: [
            { text: '20-40 working days', amber: true },
            { text: 'Online application' },
            { text: 'Health insurance often required' },
        ],
    },
    'turkey-visa': {
        label: 'Turkey eVisa',
        dest: 'Turkey - fast online eVisa, no appointment needed',
        chips: [
            { text: '24-72 hours' },
            { text: 'Fully online - no embassy visit' },
            { text: 'Eligible for most UAE residents' },
        ],
    },
    'malaysia-visa': {
        label: 'Malaysia eVisa',
        dest: 'Malaysia - online eVisa for UAE residents',
        chips: [
            { text: '3-5 working days' },
            { text: 'Fully online process' },
            { text: 'Simple documents needed' },
        ],
    },
};

const cardImages = {
    'schengen-visa': 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?auto=format&fit=crop&w=900&q=80',
    'uk-visa': 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=900&q=80',
    'usa-visa': 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?auto=format&fit=crop&w=900&q=80',
    'canada-visa': 'https://images.unsplash.com/photo-1517935706615-2717063c2225?auto=format&fit=crop&w=900&q=80',
    'japan-visa': 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=900&q=80',
    'australia-visa': 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?auto=format&fit=crop&w=900&q=80',
    'turkey-visa': 'https://images.unsplash.com/photo-1527838832700-5059252407fa?auto=format&fit=crop&w=900&q=80',
    'malaysia-visa': 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=900&q=80',
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

const codeMap = {
    'schengen-visa': 'EU',
    'uk-visa': 'UK',
    'usa-visa': 'US',
    'canada-visa': 'CA',
    'japan-visa': 'JP',
    'australia-visa': 'AU',
    'turkey-visa': 'TR',
    'malaysia-visa': 'MY',
    'vietnam-visa': 'VN',
    'brazil-visa': 'BR',
    'south-africa-visa': 'ZA',
    'evisa-assistance': 'EV',
    'tourist-visa-assistance': 'TV',
};

const feeMap = {
    'schengen-visa': 'From AED 350',
    'uk-visa': 'From AED 300',
    'usa-visa': 'From AED 450',
    'canada-visa': 'From AED 400',
    'japan-visa': 'From AED 280',
    'australia-visa': 'From AED 380',
    'turkey-visa': 'From AED 150',
    'malaysia-visa': 'From AED 120',
    'evisa-assistance': 'On request',
};

const processingMap = {
    'schengen-visa': '15-30 working days',
    'uk-visa': '8-15 working days',
    'usa-visa': 'Varies - 30-90 days',
    'canada-visa': '60-120 working days',
    'japan-visa': '5-10 working days',
    'australia-visa': '20-40 working days',
    'turkey-visa': '24-72 hours',
    'malaysia-visa': '3-5 working days',
    'evisa-assistance': 'Varies by country',
};

const badgeMap = {
    'schengen-visa': 'Most requested',
    'uk-visa': 'Popular',
    'usa-visa': 'Interview req.',
    'canada-visa': 'Visitor',
    'japan-visa': 'Tourist',
    'australia-visa': 'Visitor',
    'turkey-visa': 'Fast route',
    'malaysia-visa': 'eVisa',
    'evisa-assistance': 'Online',
};

const visaTypeMap = {
    'schengen-visa': 'Sticker visa',
    'uk-visa': 'Standard visitor visa',
    'usa-visa': 'B1/B2 visitor visa',
    'canada-visa': 'Temporary resident visa',
    'japan-visa': 'Short-stay tourist visa',
    'australia-visa': 'Visitor visa',
    'turkey-visa': 'eVisa',
    'malaysia-visa': 'eVisa / tourist visa',
    'evisa-assistance': 'eVisa / online visa',
};

const visaDocsMap = {
    'schengen-visa': 'Passport, bank statement, insurance, itinerary',
    'uk-visa': 'Passport, bank statement, travel proof',
    'usa-visa': 'Passport, DS-160, travel profile, funds',
    'canada-visa': 'Passport, funds, travel purpose, biometrics',
    'japan-visa': 'Passport, itinerary, bank proof, photo',
    'australia-visa': 'Passport, travel purpose, funds, health',
    'turkey-visa': 'Passport, photo, eligibility check',
    'malaysia-visa': 'Passport, photo, travel details',
    'evisa-assistance': 'Passport, photo, travel details',
};

const visaCopyMap = {
    'schengen-visa': 'For UAE residents planning travel across 27 European countries.',
    'uk-visa': 'Tourist and short-stay visa support for travelers visiting the United Kingdom.',
    'usa-visa': 'Visitor visa guidance focused on application readiness and interview preparation.',
    'canada-visa': 'Visitor visa assistance with document preparation and biometrics guidance.',
    'japan-visa': 'Short-term tourist visa support with itinerary and document preparation.',
    'australia-visa': 'Visitor visa support with travel purpose and document readiness.',
    'turkey-visa': 'Fast online visa assistance based on eligibility and travel plan.',
    'malaysia-visa': 'Tourist and eVisa support for UAE-based travelers.',
    'evisa-assistance': 'Support for online visa applications where document clarity matters.',
};

const visaOrder = [
    'schengen-visa',
    'uk-visa',
    'usa-visa',
    'canada-visa',
    'japan-visa',
    'australia-visa',
    'turkey-visa',
    'malaysia-visa',
    'evisa-assistance',
];

const syntheticVisaCategories = {
    'turkey-visa': {
        id: 'turkey-visa',
        title: 'Turkey eVisa',
        tag: 'eVisa',
        copy: visaCopyMap['turkey-visa'],
    },
};

const visaCode = (item) => codeMap[item.id] || String(item.title || 'Visa').slice(0, 2).toUpperCase();
const visaFee = (item) => feeMap[item.id] || 'Quote on request';
const visaProcessing = (item) => processingMap[item.id] || 'Timeline varies';
const visaBadge = (item) => badgeMap[item.id] || (item.tags.includes('popular') ? 'Most requested' : item.tag);
const visaType = (item) => visaTypeMap[item.id] || item.tag;
const visaDocs = (item) => visaDocsMap[item.id] || 'Passport, funds proof, travel plan';

const visaCards = computed(() => {
    const source = new Map((props.visaCategories || []).map((item) => [item.id, item]));

    return visaOrder.map((id) => {
        const item = source.get(id) || syntheticVisaCategories[id];

        return {
            ...item,
            copy: visaCopyMap[id] || item.copy,
            image: cardImages[id] || 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=900&q=80',
            tags: tagsFor({ ...item, copy: visaCopyMap[id] || item.copy }),
        };
    }).filter(Boolean);
});

const visibleCards = computed(() => {
    const q = search.value.toLowerCase().trim();

    return visaCards.value.filter((item) => {
        const tagMatch = activeFilter.value === 'all' || item.tags.includes(activeFilter.value);
        const searchMatch = !q || `${item.title} ${item.tag} ${item.copy}`.toLowerCase().includes(q);

        return tagMatch && searchMatch;
    });
});

const selectedSummary = computed(() => destinationData[selectedDestination.value]);
const fiveStars = '\u2605\u2605\u2605\u2605\u2605';
const visaRatingValue = '4.8\u2605';

const visaTrustMiniItems = [
    ['\uD83C\uDFDB\uFE0F Licensed UAE company', 'Registered tourism support'],
    ['\u2B50 4.8 / 5 rating', '180+ reviews'],
    ['\u23F1\uFE0F On-time file guidance', 'Prepare before deadlines'],
    ['\uD83D\uDCAC WhatsApp support', 'Daily, 9am-9pm GST'],
];

const visaDocumentCards = [
    ['\uD83D\uDCD8', 'Passport', 'Min. 6 months validity + blank pages'],
    ['\uD83E\uDEAA', 'UAE Residence', 'Residence visa or Emirates ID'],
    ['\uD83D\uDCF8', 'Photo', 'Recent passport-size photo'],
    ['\uD83C\uDFE6', 'Bank Statement', 'Recent financial proof'],
    ['\uD83D\uDCBC', 'Employment Proof', 'Salary certificate, NOC, or trade license'],
    ['\u2708\uFE0F', 'Flight Reservation', 'Travel itinerary proof'],
    ['\uD83C\uDFE8', 'Hotel Booking', 'Accommodation proof or invitation'],
    ['\uD83D\uDEE1\uFE0F', 'Insurance', 'Travel insurance when required'],
];

const trustStats = computed(() => [
    ['500+', 'Visa files assisted'],
    ['4.8★', 'Customer rating'],
    ['10+', 'Visa destinations'],
]);

const trustMiniItems = [
    ['🏛️ Licensed UAE company', 'Registered tourism support'],
    ['⭐ 4.8 / 5 rating', '180+ reviews'],
    ['⏱️ On-time file guidance', 'Prepare before deadlines'],
    ['💬 WhatsApp support', 'Daily, 9am-9pm GST'],
];

const guideSteps = [
    ['01', 'Choose your destination', 'See whether the route is sticker visa, eVisa, or visitor visa before starting.'],
    ['02', 'Check documents', 'Get clarity on passport, bank statement, insurance, itinerary, and other key documents.'],
    ['03', 'Review your file', 'Identify missing items, weak proof, or common rejection risks before submission.'],
    ['04', 'Submit with clarity', 'Move forward with a cleaner file and clear next steps for appointment or online upload.'],
];

const documentCards = [
    ['📘', 'Passport', 'Min. 6 months validity + blank pages'],
    ['🪪', 'UAE Residence', 'Residence visa or Emirates ID'],
    ['📸', 'Photo', 'Recent passport-size photo'],
    ['🏦', 'Bank Statement', 'Recent financial proof'],
    ['💼', 'Employment Proof', 'Salary certificate, NOC, or trade license'],
    ['✈️', 'Flight Reservation', 'Travel itinerary proof'],
    ['🏨', 'Hotel Booking', 'Accommodation proof or invitation'],
    ['🛡️', 'Insurance', 'Travel insurance when required'],
];

const reviewBars = [
    ['Document help', '96%', '4.8'],
    ['Communication', '98%', '4.9'],
    ['Clarity', '94%', '4.7'],
    ['Value', '90%', '4.5'],
];

const reviewCards = [
    ['Schengen Visa', 'Clear checklist and fast WhatsApp response. The team helped me understand exactly what documents were weak in my file before I submitted.', 'Mariam A.'],
    ['UK Visa', 'The UK visa process felt less confusing after the consultation. They told me exactly what the embassy looks for and I got my visa first attempt.', 'Daniel K.'],
    ['Japan Visa', 'They explained the appointment and document process clearly. Got my Japan visa in 6 working days. Very professional service.', 'Priya S.'],
];

const faqItems = [
    ['Can you guarantee visa approval?', 'No. Visa approval is decided exclusively by the embassy, consulate, or immigration authority. Acute Tourism provides document guidance, file preparation, and application support - we cannot and do not guarantee visa approval.'],
    ["What's the difference between your service and applying directly?", 'When applying directly, many customers miss weak documents such as insufficient bank balance presentation, missing NOC wording, or wrong insurance coverage. We review your file before submission and flag issues early.'],
    ['Which visa should I apply for?', 'It depends on your destination, travel purpose, nationality, UAE residence status, employment type, and travel history. Use the checker above as a starting point.'],
    ['Can you help if I already booked my appointment?', 'Yes. We can review documents and help prepare your file depending on the destination and time remaining.'],
    ['How early should I start the visa process?', 'Apply as early as possible, ideally 6-12 weeks before travel for complex visas like Schengen, USA, or Canada. For eVisa routes, 1-2 weeks is usually sufficient.'],
];

const whatsappHref = computed(() => {
    const number = props.contact?.whatsappNumber?.replace(/[^0-9]/g, '');
    const text = encodeURIComponent('Hi, I need help with a visa application.');

    return number ? `https://wa.me/${number}?text=${text}` : null;
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="visa-page visa-services-client visa-category-reference">
        <section class="category-hero">
            <div class="container hero-grid">
                <div>
                    <p class="kicker">Visa Services</p>
                    <h1 class="hero-title">Visa Assistance Made Clear, Simple, and Stress-Free</h1>
                    <p class="hero-copy">
                        We help UAE residents prepare the right documents, understand the correct visa route, and avoid costly mistakes before applying to Schengen, UK, USA, Canada, Japan, Australia, and more.
                    </p>

                    <div class="hero-stats">
                        <article v-for="[value, label] in trustStats" :key="label" class="hero-stat">
                            <strong>{{ label === 'Customer rating' ? visaRatingValue : value }}</strong>
                            <span>{{ label }}</span>
                        </article>
                    </div>

                </div>

                <article class="checker-card">
                    <div class="checker-header">
                        <p class="checker-header-label">Quick eligibility check</p>
                        <h2 class="checker-header-title">Where do you want to go?</h2>
                        <p class="checker-header-sub">Select a destination to see processing time, documents needed, and fee estimate.</p>
                    </div>
                    <div class="checker-body">
                        <label class="checker-field">
                            <span>Your destination</span>
                            <select v-model="selectedDestination" class="checker-select">
                                <option value="">- Select a country -</option>
                                <option value="schengen-visa">Schengen (Europe)</option>
                                <option value="uk-visa">United Kingdom</option>
                                <option value="usa-visa">United States (USA)</option>
                                <option value="canada-visa">Canada</option>
                                <option value="japan-visa">Japan</option>
                                <option value="australia-visa">Australia</option>
                                <option value="turkey-visa">Turkey</option>
                                <option value="malaysia-visa">Malaysia</option>
                            </select>
                        </label>
                        <label class="checker-field">
                            <span>Your nationality</span>
                            <select v-model="selectedNationality" class="checker-select">
                                <option value="">- Select nationality -</option>
                                <option>Indian</option>
                                <option>Pakistani</option>
                                <option>Filipino</option>
                                <option>Egyptian</option>
                                <option>Jordanian</option>
                                <option>Bangladeshi</option>
                                <option>Other</option>
                            </select>
                        </label>
                        <div class="checker-result" :class="{ show: selectedSummary }">
                            <div class="checker-result-label">{{ selectedSummary?.label || 'Schengen Visa' }}</div>
                            <div class="checker-result-dest">{{ selectedSummary?.dest || 'Loading details...' }}</div>
                            <div v-if="selectedSummary" class="checker-result-row">
                                <span v-for="chip in selectedSummary.chips" :key="chip.text" class="result-chip">
                                    <span class="dot" :class="{ amber: chip.amber }"></span>{{ chip.text }}
                                </span>
                            </div>
                        </div>
                        <button class="btn-check" type="button">Check Eligibility & Requirements</button>
                        <a v-if="whatsappHref" class="btn-wa-checker" :href="whatsappHref" target="_blank" rel="noreferrer">
                            Ask on WhatsApp - Fast Response
                        </a>
                        <p class="checker-disclaimer">Visa approval is decided by embassies. Acute Tourism provides guidance and document support only.</p>
                    </div>
                </article>
            </div>
        </section>

        <section class="trust-mini">
            <div class="container trust-mini__grid">
                <article v-for="[title, copy] in visaTrustMiniItems" :key="title" class="trust-mini__item">
                    <strong>{{ title }}</strong>
                    <span>{{ copy }}</span>
                </article>
            </div>
        </section>

        <section id="visa-routes" class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Visa destinations</p>
                        <h2>Choose your destination</h2>
                        <p>
                            Each route shows visa type, typical processing time, service fee, and key documents. Click a destination to see full requirements.
                        </p>
                    </div>
                </div>

                <div class="navigation-panel">
                    <div class="filter-group">
                        <span class="filter-label">Destination Type</span>
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
                    </div>
                    <input v-model="search" class="search-field" type="search" placeholder="Search visa destination e.g. Schengen, UK, USA" />
                </div>

                <div class="card-grid card-grid-three visa-card-grid">
                    <article
                        v-for="item in visibleCards"
                        :id="item.id"
                        :key="item.id"
                        class="visa-card visa-destination-card"
                    >
                        <div class="visa-card__media">
                            <img :src="item.image" :alt="item.title" loading="lazy" />
                            <div class="visa-card__head">
                                <span class="visa-code">{{ visaCode(item) }}</span>
                                <span class="visa-badge" :class="{ hot: item.tags.includes('popular') }">{{ visaBadge(item) }}</span>
                            </div>
                            <div class="processing-badge"><span class="dot amber"></span>{{ visaProcessing(item) }}</div>
                        </div>
                        <div class="visa-card__body visa-destination-card__body">
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.copy }}</p>
                            <div class="visa-meta-list">
                                <span><strong>Type:</strong> {{ visaType(item) }}</span>
                                <span><strong>Docs:</strong> {{ visaDocs(item) }}</span>
                            </div>
                            <div class="fee-line">
                                <span>{{ item.id === 'evisa-assistance' ? 'Service fee' : 'Service fee from' }}</span>
                                <strong>{{ visaFee(item) }}</strong>
                            </div>
                            <div class="card-actions">
                                <Link v-if="item.href" class="card-link" :href="item.href">Check Requirements</Link>
                                <a v-else-if="whatsappHref" class="card-link" :href="whatsappHref" target="_blank" rel="noreferrer">Check Requirements</a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">How it works</p>
                        <h2>Simple steps to prepare your visa</h2>
                        <p>Inspired by fast visa platforms, but delivered with Acute's human support and local UAE guidance.</p>
                    </div>
                </div>
                <div class="guide-grid">
                    <article v-for="[num, title, copy] in guideSteps" :key="title" class="guide-card">
                        <div class="guide-card__num">{{ num }}</div>
                        <h3>{{ title }}</h3>
                        <p>{{ copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Documents</p>
                        <h2>Common documents you may need</h2>
                        <p>Exact requirements depend on destination, nationality, employment status, and visa type.</p>
                    </div>
                </div>
                <div class="doc-grid">
                    <article v-for="[icon, title, copy] in visaDocumentCards" :key="title" class="doc-card">
                        <span class="doc-icon" aria-hidden="true">{{ icon }}</span>
                        <span><strong>{{ title }}</strong>{{ copy }}</span>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="container">
                <div class="disclaimer">
                    <span class="disclaimer-icon">!</span>
                    <p>
                        <strong>Important:</strong> Visa approval is decided solely by the embassy, consulate, or immigration authority of the destination country. Acute Tourism provides document guidance, file preparation, and application support - we do not guarantee visa approval. Processing times shown are estimates and may vary.
                    </p>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Customer Reviews</p>
                        <h2>Visa service reviews</h2>
                    </div>
                </div>
                <div class="review-summary">
                    <div>
                        <div class="review-big">4.8</div>
                        <div class="review-stars">★★★★★</div>
                        <div class="review-count">Based on 180+ reviews</div>
                    </div>
                    <div class="review-bars">
                        <div v-for="[label, width, value] in reviewBars" :key="label" class="review-bar-row">
                            <span>{{ label }}</span>
                            <div class="bar-track"><div class="bar-fill" :style="{ width }"></div></div>
                            <span>{{ value }}</span>
                        </div>
                    </div>
                </div>
                <div class="review-grid">
                    <article v-for="[tag, quote, author] in reviewCards" :key="quote" class="review-card">
                        <div class="review-stars">★★★★★ 5.0</div>
                        <span class="review-tag">{{ tag }}</span>
                        <p>"{{ quote }}"</p>
                        <strong>{{ author }}</strong>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Common questions</p>
                        <h2>Visa service questions</h2>
                        <p>Quick answers to what customers ask most before proceeding.</p>
                    </div>
                </div>
                <div class="faq-list">
                    <details v-for="[question, answer] in faqItems" :key="question" class="faq-item" :open="question === faqItems[0][0]">
                        <summary>{{ question }}</summary>
                        <p>{{ answer }}</p>
                    </details>
                </div>
            </div>
        </section>
    </div>
</template>
