<script setup>
import { watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    checkout: Object,
});

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 2,
    traveler_contacts: [
        { name: '', email: '', phone: '' },
        { name: '', email: '', phone: '' },
    ],
});

const syncTravelerContacts = (count) => {
    const nextCount = Math.max(1, Number.parseInt(count, 10) || 1);
    const travelers = [...form.traveler_contacts];

    while (travelers.length < nextCount) {
        travelers.push({ name: '', email: '', phone: '' });
    }

    form.traveler_contacts = travelers.slice(0, nextCount);
};

watch(() => form.guest_count, syncTravelerContacts, { immediate: true });

const submit = () => {
    form.post(`/checkout/${props.checkout.type}s/${props.checkout.slug}`);
};
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container detail-grid">
            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">{{ checkout.label }}</p>
                    <h1 class="page-title">{{ checkout.title }}</h1>
                    <p class="page-copy">{{ checkout.summary }}</p>

                    <div v-if="checkout.image" class="page-hero-media">
                        <img :src="checkout.image" :alt="checkout.title" />
                    </div>
                </article>
            </div>

            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Pay With Network Payment Gateway</p>
                    <h3>{{ checkout.amount }}</h3>
                    <p class="hero-copy">
                        This starts a hosted payment session. The payment gateway redirects back
                        here after authorization.
                    </p>

                    <div v-if="page.props.flash.error" class="error-banner">
                        {{ page.props.flash.error }}
                    </div>

                    <form class="lead-form" @submit.prevent="submit">
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

                        <div class="field-group">
                            <div class="field-heading">
                                <span>Traveler Contacts</span>
                                <small>Add contact details for each guest on this booking.</small>
                            </div>

                            <small v-if="form.errors.traveler_contacts">{{ form.errors.traveler_contacts }}</small>

                            <div
                                v-for="(traveler, index) in form.traveler_contacts"
                                :key="`traveler-${index}`"
                                class="traveler-card"
                            >
                                <p class="card-tag">Guest {{ index + 1 }}</p>

                                <label class="field">
                                    <span>Name</span>
                                    <input v-model="traveler.name" type="text" autocomplete="name" />
                                    <small v-if="form.errors[`traveler_contacts.${index}.name`]">
                                        {{ form.errors[`traveler_contacts.${index}.name`] }}
                                    </small>
                                </label>

                                <label class="field">
                                    <span>Email</span>
                                    <input v-model="traveler.email" type="email" autocomplete="email" />
                                    <small v-if="form.errors[`traveler_contacts.${index}.email`]">
                                        {{ form.errors[`traveler_contacts.${index}.email`] }}
                                    </small>
                                </label>

                                <label class="field">
                                    <span>Phone</span>
                                    <input v-model="traveler.phone" type="text" autocomplete="tel" />
                                    <small v-if="form.errors[`traveler_contacts.${index}.phone`]">
                                        {{ form.errors[`traveler_contacts.${index}.phone`] }}
                                    </small>
                                </label>
                            </div>
                        </div>

                        <button class="button-primary" type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Redirecting...' : 'Proceed to Payment' }}
                        </button>
                    </form>
                </article>
            </div>
        </div>
    </section>
</template>
