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
                    <p class="about-kicker">Cancellation Policy</p>
                    <h1 class="about-title">Clear cancellation terms for bookings handled by Acute Tourism.</h1>
                    <p class="about-copy">
                        This page explains how cancellation, amendment, no-show, and refund handling generally work across
                        experiences, tours, packages, and other travel-related services arranged through Acute Tourism.
                    </p>

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
                <article v-for="section in policySections" :key="section.title" class="about-card">
                    <p class="about-card__label">Policy section</p>
                    <h2>{{ section.title }}</h2>
                    <p>{{ section.body }}</p>
                </article>
            </div>
        </section>

        <section class="section-block policy-footer-band">
            <div class="container about-columns">
                <article class="about-card">
                    <p class="eyebrow">Need help?</p>
                    <h2>Contact the team with your reference.</h2>
                    <p>
                        For the fastest handling, send the booking reference, lead traveler name, service date,
                        and the product booked when contacting us.
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
