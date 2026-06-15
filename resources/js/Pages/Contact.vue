<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
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
    interest: props.interestOptions?.[0] || 'Tours & Tickets',
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

    <div class="contact-page">
        <section class="about-hero contact-hero">
            <div class="container about-hero__grid">
                <div class="about-hero__content">
                    <p class="about-kicker">Contact Acute Tourism</p>
                    <h1 class="about-title">Contact Acute Tourism</h1>
                    <p class="about-copy">
                        Contact us for a new travel enquiry or support with an existing booking. Our team can assist
                        with tours, holiday packages, visa assistance, Panoramic Bus, corporate events, and general travel planning support.
                    </p>
                </div>

                <div class="contact-cards">
                    <article class="about-card">
                        <p class="about-card__label">Contact details</p>
                        <h2>Reach us directly</h2>
                        <p>For faster support, include your booking reference if you are an existing customer, or your travel date and service needed if you are making a new enquiry.</p>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Company email</p>
                        <a :href="`mailto:${contact.email}`">{{ contact.email }}</a>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Company phone</p>
                        <a :href="`tel:${String(contact.phone || '').replace(/[^\d+]/g, '')}`">{{ contact.phone }}</a>
                        <p v-if="contact.phoneSecondary">{{ contact.phoneSecondary }}</p>
                    </article>
                    <article class="about-card">
                        <p class="about-card__label">Office address</p>
                        <p>{{ contact.address }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-block contact-section">
            <div class="container contact-layout">
                <div class="about-card about-card--primary">
                    <p class="eyebrow">Enquiry form</p>
                    <h2>Send your enquiry or support request.</h2>
                    <p>
                        Share your trip timing, guest count, booking reference if available, and the type of support you want.
                        This is the clearest route for tours, packages, visa assistance, Panoramic Bus, corporate requests, and existing booking support.
                    </p>
                </div>

                <div class="form-shell contact-form-shell">
                    <div v-if="page.props.flash.success" class="success-banner">
                        {{ page.props.flash.success }}
                    </div>

                    <form class="lead-form" @submit.prevent="submit">
                        <div class="form-grid">
                            <label class="field">
                                <span>Full Name</span>
                                <input v-model="form.name" type="text" autocomplete="name" />
                                <small v-if="form.errors.name">{{ form.errors.name }}</small>
                            </label>

                            <label class="field">
                                <span>Email Address</span>
                                <input v-model="form.email" type="email" autocomplete="email" />
                                <small v-if="form.errors.email">{{ form.errors.email }}</small>
                            </label>

                            <label class="field">
                                <span>Phone Number</span>
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
                                <span>Service Needed</span>
                                <select v-model="form.interest">
                                    <option v-for="option in interestOptions" :key="option" :value="option">{{ option }}</option>
                                </select>
                                <small v-if="form.errors.interest">{{ form.errors.interest }}</small>
                            </label>
                        </div>

                        <label class="field">
                            <span>Message</span>
                            <textarea v-model="form.message" rows="5" placeholder="Tell us what you need help with. Existing customers can include their booking reference here."></textarea>
                            <small v-if="form.errors.message">{{ form.errors.message }}</small>
                        </label>

                        <button class="button-primary" type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Sending...' : 'Send enquiry' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="container">
                <div class="about-actions contact-actions">
                    <Link class="button-secondary" href="/dubai-tours-and-tickets">Tours & Tickets</Link>
                    <Link class="button-secondary" href="/dubai-holiday-packages">Holiday Packages</Link>
                    <Link class="button-secondary" href="/tourist-visa-assistance-uae-residents">Visa Services</Link>
                    <Link class="button-secondary" href="/corporate-travel-event-planning-dubai">Corporate Events</Link>
                </div>
            </div>
        </section>
    </div>
</template>
