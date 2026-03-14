<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    contact: Object,
    interestOptions: Array,
});

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 2,
    interest: 'General Planning',
    message: '',
});

const submit = () => {
    form.post('/inquiries', {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'email', 'phone', 'travel_date', 'guest_count', 'interest', 'message'),
    });
};
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container">
            <p class="eyebrow">Contact</p>
            <h1 class="page-title">Speak with concierge and get the right shortlist faster.</h1>
            <p class="page-copy">
                Use this page for premium trip planning, package requests, group coordination, and
                availability checks across the Dubai and Abu Dhabi catalog.
            </p>

            <div class="card-grid card-grid-three">
                <article class="info-card">
                    <p class="card-tag">Email</p>
                    <h3>Sales Email</h3>
                    <p>{{ contact.email }}</p>
                </article>
                <article class="info-card">
                    <p class="card-tag">Phone</p>
                    <h3>Direct Line</h3>
                    <p>{{ contact.phone }}</p>
                </article>
                <article class="info-card">
                    <p class="card-tag">Office</p>
                    <h3>Office Address</h3>
                    <p>{{ contact.address }}</p>
                </article>
                <article v-if="contact.whatsappNumber" class="info-card">
                    <p class="card-tag">WhatsApp</p>
                    <h3>Fast Messaging</h3>
                    <p>{{ contact.whatsappNumber }}</p>
                </article>
                <article v-if="contact.whatsappNumber" class="showcase-card recommendation-card">
                    <p class="card-tag">Need a faster route?</p>
                    <h3>Start on WhatsApp if you already know your travel date.</h3>
                    <p>Best for quick coordination on guest counts, package fit, and same-day or next-day planning.</p>
                    <a
                        class="button-primary card-button"
                        :href="`https://wa.me/${contact.whatsappNumber.replace(/[^0-9]/g, '')}`"
                        target="_blank"
                        rel="noreferrer"
                    >
                        Open WhatsApp
                    </a>
                </article>
            </div>

            <div class="form-shell">
                <div v-if="page.props.flash.success" class="success-banner">
                    {{ page.props.flash.success }}
                </div>

                <form class="lead-form" @submit.prevent="submit">
                    <div class="form-grid">
                        <label class="field">
                            <span>Name</span>
                            <input v-model="form.name" type="text" autocomplete="name" />
                            <small v-if="form.errors.name">{{ form.errors.name }}</small>
                        </label>

                        <label class="field">
                            <span>Email</span>
                            <input v-model="form.email" type="email" autocomplete="email" />
                            <small v-if="form.errors.email">{{ form.errors.email }}</small>
                        </label>

                        <label class="field">
                            <span>Phone</span>
                            <input v-model="form.phone" type="text" autocomplete="tel" />
                            <small v-if="form.errors.phone">{{ form.errors.phone }}</small>
                        </label>

                        <label class="field">
                            <span>Travel Date</span>
                            <input v-model="form.travel_date" type="date" />
                            <small v-if="form.errors.travel_date">{{ form.errors.travel_date }}</small>
                        </label>

                        <label class="field">
                            <span>Guests</span>
                            <input v-model="form.guest_count" type="number" min="1" max="100" />
                            <small v-if="form.errors.guest_count">{{ form.errors.guest_count }}</small>
                        </label>

                        <label class="field">
                            <span>Interest</span>
                            <select v-model="form.interest">
                                <option v-for="option in interestOptions" :key="option" :value="option">{{ option }}</option>
                            </select>
                            <small v-if="form.errors.interest">{{ form.errors.interest }}</small>
                        </label>
                    </div>

                    <label class="field">
                        <span>Message</span>
                        <textarea v-model="form.message" rows="5"></textarea>
                        <small v-if="form.errors.message">{{ form.errors.message }}</small>
                    </label>

                    <button class="button-primary" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Sending...' : 'Submit Inquiry' }}
                    </button>
                </form>
            </div>

            <div class="hero-actions">
                <Link class="button-secondary" href="/experiences">Browse experiences</Link>
                <Link class="button-secondary" href="/packages">Browse packages</Link>
            </div>
        </div>
    </section>
</template>
