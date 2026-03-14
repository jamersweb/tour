<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
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
        <div class="container narrow">
            <p class="eyebrow">Contact</p>
            <h1 class="page-title">Concierge-first contact layer.</h1>
            <p class="page-copy">
                This page will become the primary inquiry and fast-response entry point before full
                checkout goes live.
            </p>

            <div class="card-grid card-grid-three">
                <article class="info-card">
                    <p class="card-tag">Email</p>
                    <h3>{{ contact.email }}</h3>
                </article>
                <article class="info-card">
                    <p class="card-tag">Phone</p>
                    <h3>{{ contact.phone }}</h3>
                </article>
                <article class="info-card">
                    <p class="card-tag">Office</p>
                    <h3>{{ contact.address }}</h3>
                </article>
                <article v-if="contact.whatsappNumber" class="info-card">
                    <p class="card-tag">WhatsApp</p>
                    <h3>{{ contact.whatsappNumber }}</h3>
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
        </div>
    </section>
</template>
