<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    profile: Object,
});

const page = usePage();

const form = useForm({
    name: props.profile.name,
    email: props.profile.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.patch('/account/profile');

const updatePassword = () => passwordForm.patch('/account/password', {
    onSuccess: () => passwordForm.reset(),
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">My Profile</p>
            <h1 class="page-title">Update your account details.</h1>

            <div class="form-shell">
                <div v-if="page.props.flash.success" class="success-banner">
                    {{ page.props.flash.success }}
                </div>

                <form class="lead-form" @submit.prevent="submit">
                    <label class="field">
                        <span>Name</span>
                        <input v-model="form.name" type="text" />
                        <small v-if="form.errors.name">{{ form.errors.name }}</small>
                    </label>

                    <label class="field">
                        <span>Email</span>
                        <input v-model="form.email" type="email" />
                        <small v-if="form.errors.email">{{ form.errors.email }}</small>
                    </label>

                    <button class="button-primary" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save profile' }}
                    </button>
                </form>
            </div>

            <div class="form-shell">
                <p class="card-tag">Security</p>

                <form class="lead-form" @submit.prevent="updatePassword">
                    <label class="field">
                        <span>Current Password</span>
                        <input v-model="passwordForm.current_password" type="password" autocomplete="current-password" />
                        <small v-if="passwordForm.errors.current_password">{{ passwordForm.errors.current_password }}</small>
                    </label>

                    <label class="field">
                        <span>New Password</span>
                        <input v-model="passwordForm.password" type="password" autocomplete="new-password" />
                        <small v-if="passwordForm.errors.password">{{ passwordForm.errors.password }}</small>
                    </label>

                    <label class="field">
                        <span>Confirm New Password</span>
                        <input v-model="passwordForm.password_confirmation" type="password" autocomplete="new-password" />
                    </label>

                    <button class="button-primary" type="submit" :disabled="passwordForm.processing">
                        {{ passwordForm.processing ? 'Updating...' : 'Change password' }}
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
