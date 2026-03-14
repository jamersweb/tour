<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    account: Object,
    feedbackOptions: Array,
});

const page = usePage();

const feedbackForm = useForm({
    category: props.feedbackOptions[0] ?? 'general',
    rating: 5,
    subject: '',
    message: '',
});

const submitFeedback = () => {
    feedbackForm.post('/account/feedback', {
        preserveScroll: true,
        onSuccess: () => feedbackForm.reset('subject', 'message'),
    });
};

const logoutForm = useForm({});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">My Dashboard</p>
                <h1 class="page-title">{{ account.name }}</h1>
                <p class="page-copy">{{ account.email }}</p>
            </div>

            <div class="card-grid card-grid-three">
                <article class="info-card">
                    <p class="card-tag">Orders</p>
                    <h3>{{ account.stats.orders }}</h3>
                    <p>{{ account.stats.paidOrders }} paid</p>
                </article>
                <article class="info-card">
                    <p class="card-tag">Inquiries</p>
                    <h3>{{ account.stats.inquiries }}</h3>
                    <p>Tracked across contact and experience pages</p>
                </article>
                <article class="info-card">
                    <p class="card-tag">Feedback</p>
                    <h3>{{ account.stats.feedback }}</h3>
                    <p>Submitted from your account area</p>
                </article>
            </div>

            <div class="hero-actions">
                <Link class="button-primary" href="/account/profile">Edit profile</Link>
                <button class="button-secondary" @click="logoutForm.post('/logout')">Log out</button>
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container detail-grid">
            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Recent Orders</p>
                    <div v-if="account.orders.length" class="account-list">
                        <div v-for="order in account.orders" :key="order.reference" class="account-row">
                            <strong>{{ order.itemTitle || 'Checkout Item' }}</strong>
                            <p>{{ order.amount }} | {{ order.status }} | {{ order.createdAt }}</p>
                            <p><Link :href="`/account/orders/${order.id}`">Open receipt</Link></p>
                        </div>
                    </div>
                    <p v-else class="page-copy">No orders yet.</p>
                </article>

                <article class="info-card">
                    <p class="card-tag">Recent Inquiries</p>
                    <div v-if="account.inquiries.length" class="account-list">
                        <div v-for="inquiry in account.inquiries" :key="`${inquiry.createdAt}-${inquiry.interest}`" class="account-row">
                            <strong>{{ inquiry.experienceTitle || inquiry.interest }}</strong>
                            <p>{{ inquiry.status }} | {{ inquiry.createdAt }}</p>
                        </div>
                    </div>
                    <p v-else class="page-copy">No inquiries yet.</p>
                </article>
            </div>

            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Share Feedback</p>

                    <div v-if="page.props.flash.success" class="success-banner">
                        {{ page.props.flash.success }}
                    </div>

                    <form class="lead-form" @submit.prevent="submitFeedback">
                        <label class="field">
                            <span>Category</span>
                            <select v-model="feedbackForm.category">
                                <option v-for="option in feedbackOptions" :key="option" :value="option">{{ option }}</option>
                            </select>
                            <small v-if="feedbackForm.errors.category">{{ feedbackForm.errors.category }}</small>
                        </label>

                        <label class="field">
                            <span>Rating</span>
                            <input v-model="feedbackForm.rating" type="number" min="1" max="5" />
                            <small v-if="feedbackForm.errors.rating">{{ feedbackForm.errors.rating }}</small>
                        </label>

                        <label class="field">
                            <span>Subject</span>
                            <input v-model="feedbackForm.subject" type="text" />
                            <small v-if="feedbackForm.errors.subject">{{ feedbackForm.errors.subject }}</small>
                        </label>

                        <label class="field">
                            <span>Message</span>
                            <textarea v-model="feedbackForm.message" rows="5"></textarea>
                            <small v-if="feedbackForm.errors.message">{{ feedbackForm.errors.message }}</small>
                        </label>

                        <button class="button-primary" type="submit" :disabled="feedbackForm.processing">
                            {{ feedbackForm.processing ? 'Sending...' : 'Submit feedback' }}
                        </button>
                    </form>
                </article>

                <article class="info-card">
                    <p class="card-tag">My Feedback</p>
                    <div v-if="account.feedback.length" class="account-list">
                        <div v-for="item in account.feedback" :key="`${item.subject}-${item.createdAt}`" class="account-row">
                            <strong>{{ item.subject }}</strong>
                            <p>{{ item.category }} | {{ item.status }} | {{ item.createdAt }}</p>
                        </div>
                    </div>
                    <p v-else class="page-copy">No feedback submitted yet.</p>
                </article>
            </div>
        </div>
    </section>
</template>
