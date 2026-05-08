<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    pillars: Array,
});

const page = usePage();

const stats = [
    { value: 'Dubai', label: 'Core operating market' },
    { value: 'Tailored', label: 'Planning built around client fit' },
    { value: 'End-to-end', label: 'Support from inquiry to travel day' },
];

const serviceRows = [
    'Custom Dubai and UAE itinerary planning',
    'Experiences, tours, and holiday package coordination',
    'Airport transfers, stays, and on-ground travel support',
    'Private, family, and group travel arrangement handling',
];

const trustPoints = [
    'A local supplier network that supports faster response and cleaner coordination',
    'Travel products presented with clearer commercial structure than generic marketplaces',
    'Practical destination guidance instead of disconnected listing-style browsing',
];

const reviews = [
    {
        quote: 'The planning felt far more organized than dealing with multiple vendors separately. We got clearer options and faster answers.',
        name: 'Travel client',
        tag: 'Itinerary planning',
    },
    {
        quote: 'The team helped narrow the right activities quickly and coordinated the details without making the process feel messy.',
        name: 'Family booking',
        tag: 'Dubai experiences',
    },
    {
        quote: 'What stood out was the structure. The trip support felt managed from the start instead of just being another enquiry thread.',
        name: 'Private traveler',
        tag: 'On-ground coordination',
    },
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
                    <h1 class="about-title">A Dubai travel company focused on making planning simpler, sharper, and more reliable.</h1>
                    <p class="about-copy">
                        Acute Tourism LLC helps travelers discover Dubai and the UAE through better-structured experiences,
                        tours, and packages. The focus is not just on listing activities, but on guiding clients toward the
                        right fit, coordinating the important details, and reducing friction from first inquiry to travel day.
                    </p>

                    <div class="about-actions">
                        <Link class="button-primary" href="/experiences">Explore experiences</Link>
                        <Link class="button-secondary" href="/contact">Contact Acute Tourism</Link>
                    </div>

                    <div class="about-stats">
                        <article v-for="stat in stats" :key="stat.label" class="about-stat">
                            <strong>{{ stat.value }}</strong>
                            <span>{{ stat.label }}</span>
                        </article>
                    </div>
                </div>

                    <div class="about-hero__panel home-hero-motion">
                    <div class="about-card about-card--primary">
                        <p class="about-card__label">What we do</p>
                        <ul class="about-list about-list--tight">
                            <li v-for="item in serviceRows" :key="item">{{ item }}</li>
                        </ul>
                    </div>

                    <div class="about-card about-card--accent">
                        <p class="about-card__label">Base</p>
                        <h2>Dubai, United Arab Emirates</h2>
                        <p>{{ companyContact.address }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-block about-section" data-reveal>
            <div class="container about-story">
                <div class="about-story__intro">
                    <p class="eyebrow">Our approach</p>
                    <h2>We present travel as a managed service, not just a collection of disconnected bookings.</h2>
                </div>

                <div class="about-story__cards">
                    <article v-for="pillar in pillars" :key="pillar" class="about-card">
                        <p class="about-card__label">Brand pillar</p>
                        <p>{{ pillar }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block about-section about-section--contrast" data-reveal>
            <div class="container about-columns">
                <article class="about-card">
                    <p class="eyebrow">Why clients use us</p>
                    <h2>Better coordination, clearer planning, less wasted back-and-forth.</h2>
                    <ul class="about-list">
                        <li v-for="item in trustPoints" :key="item">{{ item }}</li>
                    </ul>
                </article>

                <article class="about-card">
                    <p class="eyebrow">Who we serve</p>
                    <h2>Travelers, families, and groups who want a more organized Dubai planning process.</h2>
                    <p>
                        Acute Tourism works across leisure requests, private outings, holiday packages, and group-led
                        travel needs. The goal is to give clients a single planning thread instead of pushing them through
                        fragmented supplier decisions on their own.
                    </p>
                </article>
            </div>
        </section>

        <section class="section-block about-section" data-reveal>
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Reviews</p>
                    <h2>What clients value most.</h2>
                </div>

                <div class="card-grid card-grid-three">
                    <article v-for="review in reviews" :key="review.quote" class="testimonial-card">
                        <p class="testimonial-card__quote">“{{ review.quote }}”</p>
                        <p class="testimonial-card__name">{{ review.name }}</p>
                        <p class="testimonial-card__tag">{{ review.tag }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block about-section" data-reveal>
            <div class="container about-contact">
                <div class="about-contact__copy">
                    <p class="eyebrow">Contact</p>
                    <h2>Plan with a Dubai-based team that understands the product on the ground.</h2>
                    <p>
                        If you need itinerary support, product clarification, or help narrowing the right option,
                        contact Acute Tourism directly or continue into the experiences, tours, and packages catalog.
                    </p>
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
