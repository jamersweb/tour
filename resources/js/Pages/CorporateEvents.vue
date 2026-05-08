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

const eventTypes = [
    'Executive hosting and client entertainment',
    'Incentive travel and reward programs',
    'Leadership off-sites and curated group itineraries',
    'Private desert, yacht, and venue-led brand experiences',
];

const reviews = [
    {
        quote: 'The proposal came back with clearer scope and the coordination felt commercial rather than informal.',
        name: 'Corporate client',
        tag: 'Proposal handling',
    },
    {
        quote: 'It was easier to manage the group because one team handled the moving parts instead of us chasing several providers.',
        name: 'Group organizer',
        tag: 'Group coordination',
    },
    {
        quote: 'The value was in execution. Timings, transport, and hosting felt better structured from the start.',
        name: 'Executive travel planner',
        tag: 'On-ground delivery',
    },
];
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="corporate-page">
        <section class="about-hero corporate-hero">
            <div class="container about-hero__grid">
                <div class="about-hero__content home-hero-motion">
                    <p class="about-kicker">Corporate Events</p>
                    <h1 class="about-title">Executive hosting and group planning built around commercial clarity.</h1>
                    <p class="about-copy">
                        Corporate travel should not be treated like consumer leisure browsing. Acute Tourism packages
                        group-led Dubai experiences with clearer scope, faster coordination, and stronger on-ground execution.
                    </p>

                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Request a corporate proposal</Link>
                        <Link class="button-secondary" href="/contact">Contact Acute Tourism</Link>
                    </div>
                </div>

                <div class="about-card about-card--primary home-hero-motion">
                    <p class="about-card__label">Typical use cases</p>
                    <ul class="about-list about-list--tight">
                        <li v-for="item in eventTypes" :key="item">{{ item }}</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section-block corporate-section" data-reveal>
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Service Lines</p>
                    <h2>Products designed for corporate requirements, not generic tours.</h2>
                </div>

                <div class="corporate-services">
                    <article v-for="service in services" :key="service" class="about-card">
                        <p class="about-card__label">Corporate service</p>
                        <h3>{{ service }}</h3>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block corporate-section corporate-section--contrast" data-reveal>
            <div class="container about-columns">
                <article class="about-card">
                    <p class="eyebrow">What matters</p>
                    <h2>Speed, reliability, and a single coordination thread.</h2>
                    <p>
                        Group bookings usually fail when vendors, transport, timing, and hosting are fragmented.
                        The goal here is to simplify those dependencies and keep commercial communication clear.
                    </p>
                </article>

                <article class="about-card">
                    <p class="eyebrow">Company contact</p>
                    <h2>Dubai-based planning with local supplier access.</h2>
                    <p>{{ page.props.site.contact.address }}</p>
                    <p>{{ page.props.site.contact.phone }}</p>
                </article>
            </div>
        </section>

        <section class="section-block corporate-section" data-reveal>
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Reviews</p>
                    <h2>What corporate clients respond to.</h2>
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
    </div>
</template>
