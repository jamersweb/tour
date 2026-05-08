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
                    <p class="about-kicker">Terms and Conditions</p>
                    <h1 class="about-title">The operating terms that apply to use of the Acute Tourism website and booking flow.</h1>
                    <p class="about-copy">
                        These terms explain how website use, pricing, booking requests, third-party supplier handling,
                        customer responsibilities, and commercial limitations are treated across the Acute Tourism platform.
                    </p>

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
                <article v-for="section in termsSections" :key="section.title" class="about-card">
                    <p class="about-card__label">Terms section</p>
                    <h2>{{ section.title }}</h2>
                    <p>{{ section.body }}</p>
                </article>
            </div>
        </section>

        <section class="section-block policy-footer-band">
            <div class="container about-columns">
                <article class="about-card">
                    <p class="eyebrow">Before booking</p>
                    <h2>Review both the commercial terms and the cancellation terms.</h2>
                    <p>
                        This page works together with the cancellation policy and any product-specific conditions shown
                        on the related experience, tour, package, or service page.
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
