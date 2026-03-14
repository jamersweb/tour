<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.post('/register');
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">Account</p>
            <h1 class="page-title">Create your Acute Tourism account.</h1>

            <div class="form-shell">
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
                        <span>Password</span>
                        <input v-model="form.password" type="password" autocomplete="new-password" />
                        <small v-if="form.errors.password">{{ form.errors.password }}</small>
                    </label>

                    <label class="field">
                        <span>Confirm Password</span>
                        <input v-model="form.password_confirmation" type="password" autocomplete="new-password" />
                    </label>

                    <button class="button-primary" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create account' }}
                    </button>
                </form>

                <p class="meta-copy auth-switch">
                    Already have an account?
                    <Link class="card-link" href="/login">Sign in</Link>
                </p>
            </div>
        </div>
    </section>
</template>
