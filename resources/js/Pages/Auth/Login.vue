<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
});

const form = useForm({
    email: '',
    password: '',
});

const submit = () => form.post('/login');
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">Account</p>
            <h1 class="page-title">Sign in to your dashboard.</h1>

            <div class="form-shell">
                <form class="lead-form" @submit.prevent="submit">
                    <label class="field">
                        <span>Email</span>
                        <input v-model="form.email" type="email" autocomplete="email" />
                        <small v-if="form.errors.email">{{ form.errors.email }}</small>
                    </label>

                    <label class="field">
                        <span>Password</span>
                        <input v-model="form.password" type="password" autocomplete="current-password" />
                        <small v-if="form.errors.password">{{ form.errors.password }}</small>
                    </label>

                    <button class="button-primary" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Signing in...' : 'Sign in' }}
                    </button>
                </form>

                <p class="meta-copy auth-switch">
                    <Link class="card-link" href="/forgot-password">Forgot password?</Link>
                </p>

                <p class="meta-copy auth-switch">
                    Need an account?
                    <Link class="card-link" href="/register">Create one</Link>
                </p>
            </div>
        </div>
    </section>
</template>
