<script setup>
import { useForm } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    reset: Object,
});

const form = useForm({
    token: props.reset.token,
    email: props.reset.email,
    password: '',
    password_confirmation: '',
});

const submit = () => form.post('/reset-password');
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">Password Reset</p>
            <h1 class="page-title">Choose a new password.</h1>

            <div class="form-shell">
                <form class="lead-form" @submit.prevent="submit">
                    <label class="field">
                        <span>Email</span>
                        <input v-model="form.email" type="email" autocomplete="email" />
                        <small v-if="form.errors.email">{{ form.errors.email }}</small>
                    </label>

                    <label class="field">
                        <span>New Password</span>
                        <input v-model="form.password" type="password" autocomplete="new-password" />
                        <small v-if="form.errors.password">{{ form.errors.password }}</small>
                    </label>

                    <label class="field">
                        <span>Confirm Password</span>
                        <input v-model="form.password_confirmation" type="password" autocomplete="new-password" />
                    </label>

                    <button class="button-primary" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Reset password' }}
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
