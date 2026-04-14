<script setup>
import { computed } from 'vue';
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

const whatsappUrl = computed(() => {
    const number = props.contact?.whatsappNumber?.replace(/[^0-9]/g, '');
    if (!number) {
        return null;
    }
    const text = encodeURIComponent('Hi Acute Tourism, I would like to book a visa consultation.');

    return `https://wa.me/${number}?text=${text}`;
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="hero-section visa-services-hero">
        <div class="container visa-services-hero-grid">
            <div class="landing-copy">
                <p class="eyebrow">{{ hero.eyebrow }}</p>
                <h1 class="hero-title">{{ hero.title }}</h1>
                <p class="hero-copy landing-copy-text">{{ hero.description }}</p>

                <div class="hero-actions">
                    <a
                        v-if="whatsappUrl"
                        class="button-primary"
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noreferrer"
                    >
                        Book now — WhatsApp
                    </a>
                    <Link class="button-secondary" href="/schengen-visa">Schengen Visa</Link>
                    <Link class="button-secondary" href="/contact">Contact Us</Link>
                </div>
            </div>

            <article class="hero-panel visa-service-standard-panel">
                <p class="panel-label">Service Standard</p>
                <ul class="feature-list">
                    <li v-for="point in servicePoints" :key="point">{{ point }}</li>
                </ul>
                <a
                    v-if="whatsappUrl"
                    class="button-primary visa-service-standard-whatsapp"
                    :href="whatsappUrl"
                    target="_blank"
                    rel="noreferrer"
                    aria-label="Book visa consultation on WhatsApp"
                >
                    Book now — WhatsApp
                </a>
            </article>
        </div>
    </section>

    <section class="section-block section-contrast">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Visa Categories</p>
                <h2>Browse by destination, then get tailored guidance.</h2>
                <p>
                    Schengen, UK, USA, Japan, Canada, Australia, Malaysia, Vietnam, and more—see what we cover here,
                    then use WhatsApp or the Schengen page to go deeper when you are ready.
                </p>
            </div>

            <div class="card-grid card-grid-three">
                <article
                    v-for="item in visaCategories"
                    :id="item.id"
                    :key="item.id"
                    class="info-card visa-category-card"
                >
                    <p class="card-tag">{{ item.tag }}</p>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.copy }}</p>
                    <Link v-if="item.href" class="button-primary card-button" :href="item.href">{{ item.cta }}</Link>
                </article>
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container concierge-band visa-cta-band">
            <div>
                <p class="eyebrow">Need A Faster Route?</p>
                <h2>WhatsApp for quick questions; dedicated pages when you are ready to apply.</h2>
                <p class="page-copy">
                    Message us for timing and eligibility, or open the Schengen landing page for the full checklist
                    and consultation flow.
                </p>
            </div>

            <div class="hero-actions">
                <a v-if="whatsappUrl" class="button-primary" :href="whatsappUrl" target="_blank" rel="noreferrer">Open WhatsApp</a>
                <Link class="button-secondary" href="/schengen-visa">Schengen Landing Page</Link>
            </div>
        </div>
    </section>
</template>
