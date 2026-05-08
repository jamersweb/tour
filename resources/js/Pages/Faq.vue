<script setup>
const planningFaqs = [
    {
        question: 'How far in advance should I book?',
        answer: 'For the best availability, book as early as possible, especially for private experiences, group requests, or popular travel dates. Last-minute requests can still work, but options may narrow depending on timing and supplier capacity.',
    },
    {
        question: 'Can I cancel and get a refund?',
        answer: 'Cancellation terms depend on the product, supplier, and how close the request is to the travel date. Where a product supports flexible cancellation, that policy will be shown clearly before payment or in the final booking details.',
    },
    {
        question: 'What happens after I complete a booking?',
        answer: 'After booking, the team confirms the operational details such as timing, meeting point, pickup, guest information, and any product-specific instructions. You should keep your confirmation reference available for follow-up.',
    },
    {
        question: 'Do I need to provide my phone number during booking?',
        answer: 'Yes. A reachable contact number makes coordination easier for pickup timing, schedule confirmation, and urgent operational updates on the day of service.',
    },
    {
        question: 'Are pickup and drop-off always included?',
        answer: 'Not always. Some products include transport, some offer it as an option, and others require a direct meeting point. The exact inclusion depends on the product you choose.',
    },
    {
        question: 'Can one experience run in more than one language?',
        answer: 'Some shared activities may operate with multilingual guidance when needed. Private bookings are usually the best option when you want a more controlled language or pacing preference.',
    },
];

import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    featuredFaqs: Array,
    faqGroups: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="faq-page">
        <section class="faq-hero">
            <div class="container faq-hero__grid">
                <div class="home-hero-motion">
                    <p class="about-kicker">FAQ</p>
                    <h1 class="about-title">Questions around bookings, planning, payments, and service coordination.</h1>
                    <p class="about-copy">
                        Use this page to understand how Acute Tourism handles inquiries, payment, planning flow, and
                        service expectations across experiences, tours, packages, and visa-related requests.
                    </p>
                </div>

                <div class="about-card about-card--primary home-hero-motion">
                    <p class="about-card__label">What this covers</p>
                    <ul class="about-list about-list--tight">
                        <li>Booking and payment questions</li>
                        <li>Planning, timing, and availability</li>
                        <li>What happens after you submit an inquiry</li>
                        <li>How support works across products</li>
                    </ul>
                </div>
            </div>
        </section>

        <section v-if="featuredFaqs.length" class="section-block faq-featured" data-reveal>
            <div class="container">
                <div class="section-heading">
                    <p class="eyebrow">Top Questions</p>
                    <h2>Start with the answers clients ask for most often.</h2>
                </div>

                <div class="faq-featured__grid">
                    <article v-for="faq in featuredFaqs" :key="faq.question" class="about-card">
                        <p class="about-card__label">{{ faq.category }}</p>
                        <h3>{{ faq.question }}</h3>
                        <p>{{ faq.answer }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block faq-groups" data-reveal>
            <div class="container faq-groups__stack">
                <section class="faq-group-panel">
                    <div class="faq-group-panel__head">
                        <p class="about-kicker">Planning and Booking</p>
                    </div>

                    <div class="faq-group-panel__items">
                        <article v-for="faq in planningFaqs" :key="faq.question" class="faq-item faq-item--refined">
                            <h3>{{ faq.question }}</h3>
                            <p>{{ faq.answer }}</p>
                        </article>
                    </div>
                </section>

                <section v-for="group in faqGroups" :key="group.category" class="faq-group-panel">
                    <div class="faq-group-panel__head">
                        <p class="about-kicker">{{ group.category }}</p>
                    </div>

                    <div class="faq-group-panel__items">
                        <article v-for="faq in group.items" :key="faq.question" class="faq-item faq-item--refined">
                            <h3>{{ faq.question }}</h3>
                            <p>{{ faq.answer }}</p>
                        </article>
                    </div>
                </section>
            </div>
        </section>
    </div>
</template>
