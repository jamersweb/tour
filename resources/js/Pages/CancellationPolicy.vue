<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    policySections: Array,
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
                    <h1 class="about-title">Cancellation Policy</h1>
                    <p class="about-copy">
                        This page explains how cancellations, amendments, no-shows, and refunds are handled for tours, entry tickets,
                        holiday packages, and other travel services arranged through Acute Tourism.
                    </p>
                    <div class="faq-meta policy-meta">
                        <span class="filter-chip active">Acute Tourism LLC</span>
                        <span class="filter-chip">Last updated: 13 June 2026</span>
                    </div>

                    <div class="about-actions">
                        <Link class="button-primary" href="/contact">Contact Acute Tourism</Link>
                        <Link class="button-secondary" href="/faq">View FAQ</Link>
                    </div>
                </div>

                <div class="about-card about-card--primary">
                    <p class="about-card__label">Before you cancel</p>
                    <ul class="about-list about-list--tight">
                        <li>Have your booking reference ready</li>
                        <li>Check the service date and supplier terms</li>
                        <li>Note that package components may carry separate rules</li>
                        <li>Wait for written confirmation before assuming a cancellation is complete</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section-block policy-sections">
            <div class="container policy-sections__stack">
                <article v-for="section in policySections" :key="section.title" class="about-card policy-section-card">
                    <p class="about-card__label">Policy section</p>
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
                    <p class="eyebrow">Need help?</p>
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
