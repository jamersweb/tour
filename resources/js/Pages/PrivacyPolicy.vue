<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    privacySections: Array,
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
                    <h1 class="about-title">Privacy Policy</h1>
                    <p class="about-copy">
                        This Privacy Policy explains how Acute Tourism may collect, use, share, protect, and retain customer information
                        when providing tours, holiday packages, visa assistance, corporate events, and related travel services.
                    </p>
                    <div class="faq-meta policy-meta">
                        <span class="filter-chip active">Acute Tourism LLC</span>
                        <span class="filter-chip">Last updated: 13 June 2026</span>
                    </div>

                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Contact Acute Tourism</Link>
                        <Link class="button-secondary" href="/terms-and-conditions">Terms & Conditions</Link>
                    </div>
                </div>

                <div class="about-card about-card--primary">
                    <p class="about-card__label">At a glance</p>
                    <ul class="about-list about-list--tight">
                        <li>Inquiry and booking details may be stored for service handling</li>
                        <li>Relevant information may be shared with suppliers when needed</li>
                        <li>Payment and booking data may be retained for operational records</li>
                        <li>Policy updates may happen as systems and requirements evolve</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section-block policy-sections">
            <div class="container policy-sections__stack">
                <article v-for="section in privacySections" :key="section.title" class="about-card policy-section-card">
                    <p class="about-card__label">Privacy section</p>
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
                    <p class="eyebrow">Questions?</p>
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
