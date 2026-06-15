<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    pillars: Array,
});

const page = usePage();

const stats = [
    { value: '12+', label: 'Years of Dubai travel experience' },
    { value: '2,500+', label: 'Travelers assisted across trips and visas' },
    { value: 'Licensed', label: 'Dubai operator, license details available on request' },
    { value: '2 hrs', label: 'Typical WhatsApp response time' },
];

const serviceRows = [
    'Real support before you book, not only automated product listings.',
    'Clear guidance across tours, packages, transfers, activities, and visa documents.',
    'Dubai-based coordination with office presence and direct WhatsApp assistance.',
    'Secure payment support and confirmation handled by the Acute Tourism team.',
];

const services = [
    {
        label: 'Dubai Tours & Experiences',
        title: 'Curated activities with clearer choice.',
        copy: 'Desert safaris, yacht experiences, city tours, attractions, cultural activities, private tours, family experiences, and group arrangements.',
    },
    {
        label: 'Holiday Packages',
        title: 'Complete travel planning with one point of contact.',
        copy: 'Dubai, UAE, and international holiday packages arranged around hotels, transfers, activities, travel dates, budget, hotel category, and guest preferences.',
    },
    {
        label: 'International Visa Assistance',
        title: 'Document guidance with realistic expectations.',
        copy: 'Support for Schengen, UK, USA, Canada, Japan, Australia, and other destinations. Approval is always decided by the embassy, consulate, or immigration authority.',
    },
    {
        label: 'Panoramic Bus & Corporate Events',
        title: 'Premium group planning with real coordination.',
        copy: 'Support for Panoramic Bus experiences, corporate groups, private requests, event-style arrangements, and guest movement in Dubai.',
    },
];

const decisionPoints = [
    'We clarify what is suitable for your travel purpose before recommending a product.',
    'We explain what is included, what depends on availability, and what happens next.',
    'We keep communication direct through WhatsApp, email, and phone support.',
    'We avoid overpromising on visa approvals, supplier availability, or fixed outcomes outside our control.',
];

const workflow = [
    {
        title: '1. We understand the request',
        copy: 'We confirm the travel date, destination, number of guests, preferred service level, hotel category, budget range, or visa destination before recommending a solution.',
    },
    {
        title: '2. We explain the right fit',
        copy: 'We guide customers toward the most relevant option instead of overwhelming them with unrelated tours, generic packages, or unclear visa instructions.',
    },
    {
        title: '3. We coordinate the details',
        copy: 'Once the customer is ready to proceed, we support confirmation, payment guidance, supplier coordination, document review, and travel-day communication where applicable.',
    },
];

const trustCards = [
    { label: 'Office presence', title: 'Dubai-based company', copy: 'Office presence in Palm Jumeirah, Dubai, with direct contact by phone, email, and WhatsApp.' },
    { label: 'Licensed operator', title: 'License details available', copy: 'Verification details can be shared with customers who need company confirmation before proceeding.' },
    { label: 'Secure payments', title: 'Confirmed booking support', copy: 'Approved payment channels with booking confirmation follow-up from the Acute Tourism team.' },
    { label: 'Visa honesty', title: 'No false approval promises', copy: 'Embassies decide visa outcomes. Acute Tourism guides the process and sets realistic expectations.' },
];

const teamCards = [
    { label: 'Travel consultants', title: 'Tours & package guidance', copy: 'Support with selecting the right tour, package, hotel category, and itinerary pace.' },
    { label: 'Visa support', title: 'Document guidance', copy: 'Checklist support, document readiness, application steps, and realistic expectation setting.' },
    { label: 'Operations', title: 'Booking coordination', copy: 'Availability checks, booking confirmations, transfer timing, and supplier follow-up.' },
    { label: 'Management oversight', title: 'Service control', copy: 'Internal oversight helps keep communication and customer handling aligned with our standard.' },
];

const aboutFaqs = [
    ['Is Acute Tourism a travel planning agency in Dubai?', 'Yes. Acute Tourism LLC is a Dubai-based travel planning agency helping customers with Dubai tours, holiday packages, and international visa assistance through one coordinated team.'],
    ['What makes Acute Tourism different from a normal booking website?', 'A normal booking website usually lists products and prices. Acute Tourism focuses on planning support, product suitability, clear communication, and coordination before and after confirmation.'],
    ['Can I contact a real person before booking?', 'Yes. Customers can contact Acute Tourism by WhatsApp, phone, or email before booking. The team can help clarify availability, package details, visa support steps, and next actions.'],
    ['Can Acute Tourism guarantee visa approval?', 'No. Visa approval is decided only by the embassy, consulate, or immigration authority. Acute Tourism provides document guidance, application preparation support, and file readiness review, but does not guarantee approval or processing time.'],
];

const companyContact = computed(() => page.props.site?.contact ?? {});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="about-page">
        <section class="about-hero">
            <div class="container about-hero__grid">
                <div class="about-hero__content home-hero-motion">
                    <p class="about-kicker">About Acute Tourism</p>
                    <h1 class="about-title">About Acute Tourism</h1>
                    <p class="about-copy">
                        Acute Tourism LLC is a Dubai-based travel planning agency helping customers with Dubai tours,
                        holiday packages, international visa assistance, corporate events, and Panoramic Bus experiences.
                    </p>

                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Speak with our team</Link>
                        <Link class="button-secondary" href="/dubai-holiday-packages">View holiday packages</Link>
                        <Link class="button-secondary" href="/tourist-visa-assistance-uae-residents">Get visa assistance</Link>
                    </div>
                </div>

                <div class="about-hero__panel home-hero-motion">
                    <div class="about-card about-card--primary">
                        <p class="about-card__label">Why customers choose us</p>
                        <h2>One Dubai team for the important travel decisions.</h2>
                        <ul class="about-list about-list--tight">
                            <li v-for="item in serviceRows" :key="item">{{ item }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="trust-strip-section" data-reveal>
            <div class="container about-stats">
                <article v-for="stat in stats" :key="stat.label" class="about-stat">
                    <strong>{{ stat.value }}</strong>
                    <span>{{ stat.label }}</span>
                </article>
            </div>
        </section>

        <section class="section-block about-section" data-reveal>
            <div class="container about-columns">
                <div class="about-story__intro">
                    <p class="eyebrow">Company credibility</p>
                    <h2>Who we are, and why that matters before you book.</h2>
                    <p class="about-copy">
                        High-intent customers usually want to know one thing before they pay, share documents, or plan a full holiday:
                        can this company be trusted to handle the details properly?
                    </p>
                </div>
                <article class="about-card">
                    <p class="about-card__label">Dubai-based agency</p>
                    <h2>A travel planning agency, not a faceless booking page.</h2>
                    <p>Acute Tourism LLC helps travelers move from uncertainty to a clear plan. Our work covers curated Dubai tours, holiday package coordination, and international visa assistance for travelers who need practical guidance, fast communication, and organized next steps.</p>
                </article>
            </div>
        </section>

        <section class="section-block about-section about-section--contrast" data-reveal>
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">What we help with</p>
                        <h2>Four core services, one planning standard.</h2>
                        <p>Whether you are booking one activity, planning a complete trip, arranging visa support, or organizing a group experience, the goal is the same: make the process easier to understand, confirm, and trust.</p>
                    </div>
                </div>
                <div class="content-card-grid content-card-grid--four">
                    <article v-for="service in services" :key="service.label" class="about-card">
                        <p class="about-card__label">{{ service.label }}</p>
                        <h3>{{ service.title }}</h3>
                        <p>{{ service.copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block about-section" data-reveal>
            <div class="container about-columns">
                <article>
                    <p class="eyebrow">What makes us different</p>
                    <h2>We help customers make the right travel decision before they commit.</h2>
                    <p class="about-copy">Many travel websites show options, prices, and generic descriptions. Acute Tourism adds the human layer that high-intent customers often need: explanation, suitability, timing, document clarity, availability checking, and follow-up.</p>
                    <ul class="about-list">
                        <li v-for="item in decisionPoints" :key="item">{{ item }}</li>
                    </ul>
                </article>
                <article class="about-card about-card--media">
                    <p class="about-card__label">Company video</p>
                    <h2>Company introduction video</h2>
                    <p>A short introduction video can sit here to show the Acute Tourism team, our Dubai-based service approach, and how customers are supported before booking.</p>
                </article>
            </div>
        </section>

        <section class="section-block about-section about-section--navy" data-reveal>
            <div class="container">
                <div class="section-heading section-heading--dark">
                    <div>
                        <p class="eyebrow">How we work</p>
                        <h2>Designed to reduce doubt before booking.</h2>
                        <p>Every serious enquiry should leave the customer with a clearer understanding of the service, the next step, and what information is required.</p>
                    </div>
                </div>
                <div class="content-card-grid content-card-grid--three">
                    <article v-for="item in workflow" :key="item.title" class="about-card about-card--on-dark">
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block about-section" data-reveal>
            <div class="container about-columns">
                <div>
                    <p class="eyebrow">Trust and transparency</p>
                    <h2>What high-intent customers should know upfront.</h2>
                    <p class="about-copy">Trust is not only about looking premium. It is about being clear on what is included, what is not guaranteed, who handles the request, and how the customer can reach the company.</p>
                </div>
                <div class="content-card-grid content-card-grid--two">
                    <article v-for="item in trustCards" :key="item.title" class="about-card">
                        <p class="about-card__label">{{ item.label }}</p>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block about-section about-section--contrast" data-reveal>
            <div class="container about-columns">
                <div>
                    <p class="eyebrow">Who handles your request</p>
                    <h2>A coordinated team, not a scattered booking process.</h2>
                    <p class="about-copy">Customers should not have to chase multiple suppliers or repeat the same details across different channels. Your request is handled through the relevant Acute Tourism team depending on the service needed.</p>
                </div>
                <div class="content-card-grid content-card-grid--two">
                    <article v-for="item in teamCards" :key="item.title" class="about-card">
                        <p class="about-card__label">{{ item.label }}</p>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block about-section" data-reveal>
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Questions visitors may have</p>
                        <h2>Clear answers before you enquire.</h2>
                    </div>
                </div>
                <div class="content-faq-list">
                    <details v-for="(faq, index) in aboutFaqs" :key="faq[0]" :open="index === 0" class="content-faq-item">
                        <summary>{{ faq[0] }}</summary>
                        <p>{{ faq[1] }}</p>
                    </details>
                </div>
            </div>
        </section>

        <section class="section-block about-section" data-reveal>
            <div class="container about-contact">
                <div class="about-contact__copy">
                    <p class="eyebrow">Contact Acute Tourism</p>
                    <h2>Speak with a Dubai-based travel planning team.</h2>
                    <p>Contact Acute Tourism if you are ready to book, need help choosing the right option, want a holiday package quote, or need guidance on international visa assistance.</p>
                </div>

                <div class="about-contact__grid">
                    <article class="about-card">
                        <p class="about-card__label">Company email</p>
                        <a :href="`mailto:${companyContact.email}`">{{ companyContact.email }}</a>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Company phone</p>
                        <a :href="`tel:${String(companyContact.phone || '').replace(/[^\d+]/g, '')}`">{{ companyContact.phone }}</a>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Office address</p>
                        <p>{{ companyContact.address }}</p>
                    </article>
                </div>
            </div>
        </section>
    </div>
</template>
