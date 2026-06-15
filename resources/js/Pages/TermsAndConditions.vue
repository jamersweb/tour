<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    termsSections: Array,
});

const page = usePage();
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="policy-page">
        <section class="about-hero policy-hero">
            <div class="container about-hero__grid">
                <div class="about-hero__content">
                    <p class="about-kicker">Acute Tourism Policy</p>
                    <h1 class="about-title">Terms and Conditions</h1>
                    <p class="about-copy">
                        These Terms & Conditions explain the general rules for using the Acute Tourism website, making enquiries,
                        confirming bookings, paying for services, requesting changes, and using travel-related services arranged by Acute Tourism.
                    </p>
                    <div class="faq-meta policy-meta">
                        <span class="filter-chip active">Acute Tourism LLC</span>
                        <span class="filter-chip">Last updated: 13 June 2026</span>
                    </div>

                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Contact Acute Tourism</Link>
                        <Link class="button-secondary" href="/cancellation-policy">Cancellation Policy</Link>
                    </div>
                </div>

                <div class="about-card about-card--primary">
                    <p class="about-card__label">Important</p>
                    <ul class="about-list about-list--tight">
                        <li>Availability is not final until confirmed</li>
                        <li>Supplier terms may affect the booking outcome</li>
                        <li>Customer-submitted details must be accurate</li>
                        <li>Policy updates may be made as operations evolve</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section-block policy-sections">
            <div class="container policy-sections__stack">
                <article v-for="section in termsSections" :key="section.title" class="about-card policy-section-card">
                    <p class="about-card__label">Terms section</p>
                    <h2>{{ section.title }}</h2>
                    <ul v-if="section.items" class="about-list">
                        <li v-for="item in section.items" :key="item">{{ item }}</li>
                    </ul>
                    <p v-else>{{ section.body }}</p>
                </article>
            </div>
        </section>

        <section class="section-block policy-footer-band">
            <div class="container about-columns">
                <article class="about-card">
                    <p class="eyebrow">Before booking</p>
                    <h2>Need help?</h2>
                    <p>For faster handling, contact Acute Tourism with the relevant booking reference, service date, or request details.</p>
                </article>

                <article class="about-card">
                    <p class="eyebrow">Company contact</p>
                    <h2>Acute Tourism</h2>
                    <p>{{ page.props.site.contact.email }}</p>
                    <p>{{ page.props.site.contact.phone }}</p>
                    <p>{{ page.props.site.contact.address }}</p>
                </article>
            </div>
        </section>
    </div>
</template>
