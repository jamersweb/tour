<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    contact: Object,
    hero: Object,
    visaCategories: Array,
    servicePoints: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="visa-page">
        <section class="about-hero visa-page-hero">
            <div class="container about-hero__grid">
                <div>
                    <p class="about-kicker">{{ hero.eyebrow }}</p>
                    <h1 class="about-title">{{ hero.title }}</h1>
                    <p class="about-copy">{{ hero.description }}</p>

                    <div class="about-actions">
                        <Link class="button-primary" href="/schengen-visa">Schengen Visa</Link>
                        <Link class="button-secondary" href="/contact">Contact Acute Tourism</Link>
                    </div>
                </div>

                <article class="about-card about-card--primary">
                    <p class="about-card__label">Service standard</p>
                    <ul class="about-list about-list--tight">
                        <li v-for="point in servicePoints" :key="point">{{ point }}</li>
                    </ul>
                </article>
            </div>
        </section>

        <section class="section-block visa-page-section">
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Visa Categories</p>
                    <h2>Browse by destination and route.</h2>
                    <p>
                        Start with the destination, then move into the relevant consultation or planning flow based on
                        your residency, timing, and intended trip.
                    </p>
                </div>

                <div class="corporate-services">
                    <article
                        v-for="item in visaCategories"
                        :id="item.id"
                        :key="item.id"
                        class="about-card"
                    >
                        <p class="about-card__label">{{ item.tag }}</p>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.copy }}</p>
                        <Link v-if="item.href" class="button-primary card-button" :href="item.href">{{ item.cta }}</Link>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block visa-page-section visa-page-section--contrast">
            <div class="container about-columns">
                <article class="about-card">
                    <p class="eyebrow">When to contact us</p>
                    <h2>Use the visa pages for structure, then reach out when your route is clearer.</h2>
                    <p>
                        The public pages are meant to reduce ambiguity first. Once your intended destination and travel
                        window are clear, the next step is a direct inquiry with your supporting context.
                    </p>
                </article>

                <article class="about-card">
                    <p class="eyebrow">Company contact</p>
                    <h2>Need clarification before applying?</h2>
                    <p>{{ contact.email }}</p>
                    <p>{{ contact.phone }}</p>
                    <p>{{ contact.address }}</p>
                </article>
            </div>
        </section>
    </div>
</template>
