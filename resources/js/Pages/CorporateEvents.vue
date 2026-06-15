<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    services: Array,
});

const page = usePage();

const promises = [
    'Human consultation before pricing or proposal recommendations.',
    'Prompt response through WhatsApp, phone, or email.',
    'Clear event scope, inclusions, timing, and responsibilities.',
    'Coordination for transport, activities, guest flow, and suppliers.',
];

const stats = [
    { value: 'Human-first', label: 'Consultation before proposal' },
    { value: '2 hrs', label: 'Typical WhatsApp response time' },
    { value: 'Dubai-based', label: 'Local supplier and operations access' },
    { value: 'Service-led', label: 'Clear coordination standard' },
];

const serviceCards = [
    { label: 'Desert events', title: 'Executive desert hosting', copy: 'Premium desert safaris, private camp-style setups, dinner programs, and hosted group experiences.' },
    { label: 'Yacht events', title: 'Private yacht hosting', copy: 'Corporate yacht charters for client entertainment, celebrations, networking, and smaller executive groups.' },
    { label: 'Incentives', title: 'Reward and incentive trips', copy: 'Curated Dubai itineraries for teams, top performers, leadership groups, and visiting business guests.' },
    { label: 'Ground handling', title: 'Transport and guest movement', copy: 'Private transfers, bus coordination, pickup flow, timing, and supplier movement planning.' },
];

const process = [
    { step: '1', title: 'Consult', copy: 'We clarify the goal, date, group size, guest profile, preferred service level, and key requirements.' },
    { step: '2', title: 'Recommend', copy: 'We suggest suitable options instead of sending generic activities that may not fit the brief.' },
    { step: '3', title: 'Propose', copy: 'You receive a clear proposal with scope, inclusions, timing, price direction, and next steps.' },
    { step: '4', title: 'Coordinate', copy: 'Once approved, we coordinate supplier timing, transport, guest movement, and event-day communication.' },
];

const guarantees = [
    ['Prompt response guarantee', 'Your enquiry will be acknowledged quickly, with next steps handled by a real person.'],
    ['Clear scope guarantee', 'We explain inclusions, exclusions, supplier-dependent items, timing, and confirmation requirements.'],
    ['Coordination guarantee', 'Once confirmed, our team coordinates the agreed services, suppliers, and event flow.'],
    ['Honest expectation guarantee', 'We do not promise outcomes outside our control, such as weather, traffic, venue rules, or supplier restrictions.'],
];

const supportTypes = [
    { label: 'Executive hosting', title: 'Client entertainment and VIP hosting', copy: 'Private experiences, hosted dinners, yacht outings, desert programs, and premium Dubai moments for important guests.' },
    { label: 'Team experiences', title: 'Company outings and team rewards', copy: 'Curated experiences for staff appreciation, team bonding, sales rewards, leadership retreats, and annual celebrations.' },
    { label: 'Travel and events', title: 'Inbound corporate groups', copy: 'Itinerary planning, transfers, tours, private group arrangements, and Dubai experience support for visiting teams or delegates.' },
];

const faqs = [
    ['Do you provide corporate event planning in Dubai?', 'Yes. Acute Tourism supports corporate event planning in Dubai, including executive hosting, incentive travel, private group experiences, yacht events, desert programs, transport coordination, and curated Dubai itineraries.'],
    ['Can we speak to a real person before receiving a proposal?', 'Yes. The process is consultation-led. A team member will clarify your event goal, group size, date, service expectations, and important requirements before recommending suitable options.'],
    ['What does the best service guarantee mean?', 'It means Acute Tourism commits to clear communication, transparent scope, honest expectation setting, and coordinated service handling. It does not mean guaranteed weather, traffic, venue access, supplier availability, or third-party outcomes outside the company control.'],
    ['What information should we provide for a proposal?', 'Provide the preferred date, group size, guest profile, preferred experience type, budget range if available, transport needs, food or venue requirements, and any privacy, branding, or timing expectations.'],
];
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="corporate-page">
        <section class="about-hero corporate-hero">
            <div class="container about-hero__grid">
                <div class="about-hero__content home-hero-motion">
                    <p class="about-kicker">Corporate events in Dubai</p>
                    <h1 class="about-title">Corporate Travel and Event Planning Dubai</h1>
                    <p class="about-copy">
                        Acute Tourism helps companies, executives, HR teams, travel managers, and event organizers plan Dubai corporate
                        experiences with prompt response, clear scope, human support, and reliable event-day coordination.
                    </p>

                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Request a corporate proposal</Link>
                        <a class="button-secondary" href="https://wa.me/971585161554?text=Hi%20Acute%20Tourism%2C%20I%20would%20like%20to%20plan%20a%20corporate%20event%20in%20Dubai." target="_blank" rel="noreferrer">Speak to a consultant</a>
                    </div>
                </div>

                <div class="about-card about-card--primary home-hero-motion">
                    <p class="about-card__label">Service promise</p>
                    <h2>Premium service, without slow back-and-forth.</h2>
                    <ul class="about-list about-list--tight">
                        <li v-for="item in promises" :key="item">{{ item }}</li>
                    </ul>
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

        <section class="section-block corporate-section" data-reveal>
            <div class="container about-columns">
                <div>
                    <p class="eyebrow">Human-first consultation</p>
                    <h2>Corporate requests need more than a product list.</h2>
                    <p class="about-copy">Before sending a proposal, Acute Tourism reviews your event goal, guest profile, group size, budget direction, preferred service level, transport needs, timing, and any risks that can affect delivery.</p>
                    <ul class="about-list">
                        <li>Clear recommendation based on your event purpose.</li>
                        <li>Transparent proposal with key inclusions and exclusions stated.</li>
                        <li>Dedicated follow-up so your team is not left chasing suppliers.</li>
                    </ul>
                </div>
                <div class="content-image-panel content-image-panel--corporate" role="img" aria-label="Corporate event planning consultation"></div>
            </div>
        </section>

        <section class="section-block corporate-section corporate-section--contrast" data-reveal>
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Corporate service lines</p>
                        <h2>Premium group experiences designed for business needs.</h2>
                        <p>From client entertainment to team rewards, Acute Tourism shapes the request into a practical event plan with clear service coordination.</p>
                    </div>
                </div>

                <div class="content-card-grid content-card-grid--four">
                    <article v-for="service in serviceCards" :key="service.title" class="about-card">
                        <p class="about-card__label">{{ service.label }}</p>
                        <h3>{{ service.title }}</h3>
                        <p>{{ service.copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block corporate-section about-section--navy" data-reveal>
            <div class="container">
                <div class="section-heading section-heading--dark">
                    <div>
                        <p class="eyebrow">How we work</p>
                        <h2>A clear process from enquiry to event day.</h2>
                        <p>We keep the process structured so your team knows what is included, what needs confirmation, and who is coordinating the request.</p>
                    </div>
                </div>
                <div class="content-card-grid content-card-grid--four">
                    <article v-for="item in process" :key="item.step" class="about-card about-card--on-dark process-card">
                        <span>{{ item.step }}</span>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block corporate-section" data-reveal>
            <div class="container guarantee-box">
                <div>
                    <p class="eyebrow">Best service guarantee</p>
                    <h2>We guarantee the service standard we control.</h2>
                    <p>Our guarantee focuses on communication, clarity, coordination, and honest expectation setting.</p>
                </div>
                <div class="content-card-grid content-card-grid--two">
                    <article v-for="item in guarantees" :key="item[0]" class="about-card about-card--on-dark">
                        <h3>{{ item[0] }}</h3>
                        <p>{{ item[1] }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block corporate-section corporate-section--contrast" data-reveal>
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Best for</p>
                        <h2>Corporate requests we can support.</h2>
                        <p>Suitable for companies that want premium event coordination without scattered vendor communication.</p>
                    </div>
                </div>
                <div class="content-card-grid content-card-grid--three">
                    <article v-for="item in supportTypes" :key="item.title" class="about-card">
                        <p class="about-card__label">{{ item.label }}</p>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="proposal" class="section-block corporate-section" data-reveal>
            <div class="container proposal-card">
                <div>
                    <p class="eyebrow">Request a proposal</p>
                    <h2>Tell us what you want to achieve, and we will recommend the right format.</h2>
                    <p>For a faster response, share the event date, group size, guest profile, preferred experience type, budget range if available, and whether you need transport, food, venue support, or full coordination.</p>
                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Send corporate request</Link>
                        <a class="button-secondary" href="https://wa.me/971585161554?text=Hi%20Acute%20Tourism%2C%20I%20would%20like%20to%20request%20a%20corporate%20event%20proposal." target="_blank" rel="noreferrer">Request on WhatsApp</a>
                    </div>
                </div>
                <div class="contact-cards">
                    <article class="about-card">
                        <p class="about-card__label">Email</p>
                        <a :href="`mailto:${page.props.site.contact.email}`">{{ page.props.site.contact.email }}</a>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Phone</p>
                        <a :href="`tel:${String(page.props.site.contact.phone || '').replace(/[^\d+]/g, '')}`">{{ page.props.site.contact.phone }}</a>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Office</p>
                        <p>{{ page.props.site.contact.address }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block corporate-section corporate-section--contrast" data-reveal>
            <div class="container">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Corporate event FAQs</p>
                        <h2>Clear answers before your team proceeds.</h2>
                    </div>
                </div>
                <div class="content-faq-list">
                    <details v-for="(faq, index) in faqs" :key="faq[0]" :open="index === 0" class="content-faq-item">
                        <summary>{{ faq[0] }}</summary>
                        <p>{{ faq[1] }}</p>
                    </details>
                </div>
            </div>
        </section>
    </div>
</template>
