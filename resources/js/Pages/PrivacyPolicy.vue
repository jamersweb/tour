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
                    <p class="about-kicker">Privacy Policy</p>
                    <h1 class="about-title">How Acute Tourism collects, uses, and protects customer information.</h1>
                    <p class="about-copy">
                        This page explains how personal information may be collected through inquiries, bookings,
                        payments, and website usage, and how that information is handled as part of Acute Tourism operations.
                    </p>

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
                <article v-for="section in privacySections" :key="section.title" class="about-card">
                    <p class="about-card__label">Privacy section</p>
                    <h2>{{ section.title }}</h2>
                    <p>{{ section.body }}</p>
                </article>
            </div>
        </section>

        <section class="section-block policy-footer-band">
            <div class="container about-columns">
                <article class="about-card">
                    <p class="eyebrow">Questions?</p>
                    <h2>Contact the company directly for clarification.</h2>
                    <p>
                        If you need more context on how your inquiry, booking, or transaction data is handled,
                        contact Acute Tourism with the relevant reference or request details.
                    </p>
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
