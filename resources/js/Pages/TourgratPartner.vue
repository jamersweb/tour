<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
});

const page = usePage();

const steps = [
    ['1', 'Send a referral', 'Share a traveler, group, company, or guest who may need tours, tickets, visas, or travel arrangements.'],
    ['2', 'Acute follows up', 'Our team checks the request, assists the customer, and manages the booking or consultation process.'],
    ['3', 'Track your reward', 'When a referral becomes an eligible confirmed booking, your reward can be tracked inside Tourgrat.'],
];

const audiences = [
    ['Hospitality contacts', 'Refer guests who need tours, transfers, tickets, or special arrangements.'],
    ['Community connectors', 'Share travel requests from friends, families, groups, and local communities.'],
    ['Tour and transport people', 'Refer customers who ask for activities, packages, or extra travel services.'],
    ['Content creators', 'Share travel opportunities with your audience and track qualified referrals.'],
];

const benefits = [
    ['Stronger earning potential', 'Earn rewards on eligible confirmed bookings, with selected referrals reaching as high as 15%.'],
    ['Easy reward tracking', 'See referral status, completed referrals, pending payouts, and reward activity inside the app.'],
    ['Acute handles customers', 'Our team manages consultation, booking support, confirmation, and service delivery.'],
    ['Refer many services', 'Tours, attraction tickets, holiday packages, visa assistance, and group travel requests.'],
];

const faqs = [
    ['Do I need to sell the tour myself?', 'No. Your role is to refer a potential customer. Acute Tourism handles consultation, booking, and service arrangements.'],
    ['When do I earn a reward?', 'Rewards apply when a referral meets the program terms and becomes an eligible confirmed booking.'],
    ['Can anyone join?', 'Tourgrat is designed for people who can refer genuine travel opportunities, subject to review and approval.'],
    ['What can I refer?', 'You can refer tours, tickets, holiday packages, visa assistance requests, group travel, and corporate travel opportunities.'],
    ['Is this employment with Acute Tourism?', 'No. Tourgrat is not employment, not a part-time job, and not a salary-based role. It is an optional referral rewards program.'],
    ['How much can I earn per referral?', 'Rewards vary by product, booking value, and program terms. Selected eligible confirmed referrals can earn rewards as high as 15%.'],
];

const form = useForm({
    source: 'tourgrat-partner-page',
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 1,
    interest: 'Tourgrat Referrer Application',
    referrer_type: 'Individual referrer',
    referral_source: '',
    message: '',
});

function submit() {
    const message = [
        `Referrer type: ${form.referrer_type}`,
        `Referral source: ${form.referral_source || 'Not provided'}`,
        `Message: ${form.message || 'No extra message provided.'}`,
    ].join('\n');

    form
        .transform((data) => ({
            source: data.source,
            name: data.name,
            email: data.email,
            phone: data.phone,
            travel_date: data.travel_date,
            guest_count: data.guest_count,
            interest: data.interest,
            message,
        }))
        .post('/inquiries', {
            preserveScroll: true,
            onSuccess: () => form.reset('name', 'email', 'phone', 'referrer_type', 'referral_source', 'message'),
        });
}
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="tourgrat-page">
        <section class="tourgrat-hero">
            <div class="container tourgrat-hero__grid">
                <div>
                    <p class="tourgrat-kicker">Tourgrat by Acute Tourism</p>
                    <h1>Earn With Tourgrat</h1>
                    <p>
                        Tourgrat is a simple referral platform for anyone who can connect travelers to Acute Tourism tours,
                        tickets, packages, visa assistance, or group requests and earn rewards as high as 15% per eligible confirmed referral.
                    </p>
                    <ul class="tourgrat-points">
                        <li>Earn up to 15% per referral</li>
                        <li>No travel business needed</li>
                        <li>Acute handles the booking</li>
                        <li>Track referrals and payouts in the app</li>
                    </ul>
                    <div class="tourgrat-actions">
                        <a class="tourgrat-btn tourgrat-btn--gold" href="#tourgrat-apply">Become a Referrer</a>
                        <a class="tourgrat-btn tourgrat-btn--light" href="#tourgrat-how">How it works</a>
                    </div>
                </div>

                <aside class="tourgrat-app" aria-label="Tourgrat app preview">
                    <div class="tourgrat-app__screen">
                        <div class="tourgrat-app__top">
                            <div>T</div>
                            <span><strong>Tourgrat</strong><small>Referrer Dashboard</small></span>
                        </div>
                        <div class="tourgrat-app__body">
                            <div class="tourgrat-app__title"><small>Control center</small><strong>Dashboard</strong></div>
                            <div class="tourgrat-stats">
                                <span><small>Rewards</small><strong>AED 2,480</strong></span>
                                <span><small>Completed</small><strong>13</strong></span>
                                <span><small>Pending</small><strong>AED 640</strong></span>
                                <span><small>Active</small><strong>4</strong></span>
                            </div>
                            <div class="tourgrat-app__action">
                                <strong>Today's action</strong>
                                <span>3 payouts awaiting approval</span>
                            </div>
                            <div class="tourgrat-referrals">
                                <strong>Recent referrals</strong>
                                <span><em>Evening Desert Safari</em><b>AED 180</b></span>
                                <span><em>Yacht Charter</em><b>AED 420</b></span>
                                <span><em>Holiday Package</em><b>Pending</b></span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section id="tourgrat-how" class="tourgrat-section">
            <div class="container">
                <div class="tourgrat-heading">
                    <p class="tourgrat-eyebrow">Simple process</p>
                    <h2>How Tourgrat works</h2>
                    <p>You focus on referring the right traveler. Acute Tourism handles the travel service, customer support, and booking process.</p>
                </div>
                <div class="tourgrat-steps">
                    <article v-for="[number, title, copy] in steps" :key="title">
                        <span>{{ number }}</span>
                        <h3>{{ title }}</h3>
                        <p>{{ copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="tourgrat-section tourgrat-section--soft">
            <div class="container tourgrat-split">
                <div class="tourgrat-panel">
                    <p class="tourgrat-eyebrow">Who can join?</p>
                    <h2>Everyone with travel connections.</h2>
                    <p>
                        You do not need to be a professional travel agent. Tourgrat is useful for anyone who naturally meets
                        people planning trips, activities, visas, or Dubai experiences.
                    </p>
                    <div class="tourgrat-pills">
                        <span>Drivers</span><span>Hotel staff</span><span>Tour guides</span><span>Creators</span>
                        <span>Students</span><span>Community members</span><span>Friends and family connectors</span>
                    </div>
                </div>
                <div class="tourgrat-audience">
                    <article v-for="[title, copy] in audiences" :key="title">
                        <strong>{{ title }}</strong>
                        <span>{{ copy }}</span>
                    </article>
                </div>
            </div>
        </section>

        <section class="tourgrat-section">
            <div class="container">
                <div class="tourgrat-heading">
                    <p class="tourgrat-eyebrow">Why it is worth your time</p>
                    <h2>Clear, trackable, and low effort.</h2>
                    <p>Tourgrat is built for simple referrals, not complex travel selling.</p>
                </div>
                <div class="tourgrat-commission">
                    <article><span>Commission highlight</span><strong>Up to 15%</strong><p>Selected eligible referrals can earn rewards as high as 15% of the confirmed booking value.</p></article>
                    <article><span>Typical model</span><strong>Trackable rewards</strong><p>Tourgrat shows reward status, pending payouts, and completed earnings.</p></article>
                    <article><span>Why it matters</span><strong>Acute closes</strong><p>You do not need to manage quotes, fulfillment, or travel operations.</p></article>
                </div>
                <div class="tourgrat-benefits">
                    <article v-for="[title, copy] in benefits" :key="title">
                        <h3>{{ title }}</h3>
                        <p>{{ copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="tourgrat-section tourgrat-download">
            <div class="container tourgrat-download__box">
                <div>
                    <h2>Download Tourgrat and start referring.</h2>
                    <p>Use the app to submit referrals, check status, and track rewards from eligible confirmed bookings.</p>
                </div>
                <div class="tourgrat-store-buttons">
                    <a href="#tourgrat-apply">Apply to join</a>
                </div>
            </div>
        </section>

        <section id="tourgrat-apply" class="tourgrat-section tourgrat-section--soft">
            <div class="container tourgrat-form-grid">
                <div>
                    <p class="tourgrat-eyebrow">Join Tourgrat</p>
                    <h2>Apply to become a referrer</h2>
                    <p>Tell us who you are and how you usually meet or connect with travelers. Our team will review your details and guide you on the next steps.</p>
                    <ul class="tourgrat-checks">
                        <li>No travel agency license needed to express interest.</li>
                        <li>Open to individuals, communities, and partners.</li>
                        <li>Best for people who can refer real travel opportunities.</li>
                    </ul>
                </div>
                <div class="tourgrat-form-card">
                    <div v-if="page.props.flash.success" class="success-banner">{{ page.props.flash.success }}</div>
                    <form class="tourgrat-form" @submit.prevent="submit">
                        <label><span>Full name</span><input v-model="form.name" type="text" autocomplete="name" /></label>
                        <label><span>Phone / WhatsApp</span><input v-model="form.phone" type="tel" autocomplete="tel" /></label>
                        <label><span>Email</span><input v-model="form.email" type="email" autocomplete="email" /></label>
                        <label><span>I am a</span><select v-model="form.referrer_type"><option>Individual referrer</option><option>Driver / transport partner</option><option>Hotel / hospitality contact</option><option>Tour guide</option><option>Content creator</option><option>Community / group contact</option><option>Travel partner</option><option>Other</option></select></label>
                        <label class="tourgrat-form__full"><span>Where do your referrals usually come from?</span><input v-model="form.referral_source" type="text" placeholder="Friends, hotel guests, social media, communities, customers..." /></label>
                        <label class="tourgrat-form__full"><span>Message</span><textarea v-model="form.message" rows="5" placeholder="Tell us how you plan to refer travelers."></textarea></label>
                        <button class="tourgrat-btn tourgrat-btn--gold tourgrat-form__full" type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Sending...' : 'Submit Application' }}
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="tourgrat-section">
            <div class="container tourgrat-clarity">
                <p class="tourgrat-eyebrow">Important clarification</p>
                <h2>Tourgrat is a referral program, not employment.</h2>
                <p>This clarification helps interested referrers understand the opportunity and the terms before joining.</p>
                <div>
                    <article><strong>Not a job</strong><span>Acute Tourism is not hiring referrers as employees, part-time staff, or freelancers.</span></article>
                    <article><strong>No fixed salary</strong><span>Rewards are not salary or guaranteed income. They depend on eligible confirmed referrals.</span></article>
                    <article><strong>Optional program</strong><span>There are no fixed working hours, work obligations, or employment relationship.</span></article>
                </div>
            </div>
        </section>

        <section class="tourgrat-section tourgrat-section--soft">
            <div class="container">
                <div class="tourgrat-heading">
                    <p class="tourgrat-eyebrow">Questions</p>
                    <h2>Frequently asked questions</h2>
                </div>
                <div class="tourgrat-faq">
                    <details v-for="[question, answer] in faqs" :key="question">
                        <summary>{{ question }}</summary>
                        <p>{{ answer }}</p>
                    </details>
                </div>
            </div>
        </section>
    </div>
</template>
