<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
});

const page = usePage();

const form = useForm({
    email: '',
});

const submit = () => form.post('/forgot-password');
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">Password Reset</p>
            <h1 class="page-title">Request a reset link.</h1>

            <div class="form-shell">
                <div v-if="page.props.flash.success" class="success-banner">
                    {{ page.props.flash.success }}
                </div>

                <form class="lead-form" @submit.prevent="submit">
                    <label class="field">
                        <span>Email</span>
                        <input v-model="form.email" type="email" autocomplete="email" />
                        <small v-if="form.errors.email">{{ form.errors.email }}</small>
                    </label>

                    <button class="button-primary" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Sending...' : 'Send reset link' }}
                    </button>
                </form>

                <p class="auth-switch">
                    <Link href="/login">Back to login</Link>
                </p>
            </div>
        </div>
    </section>
</template>
