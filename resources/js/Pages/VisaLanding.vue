<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    contact: Object,
    interestOptions: Array,
    hero: Object,
    anchorLinks: { type: Array, default: () => [] },
    visualGallery: { type: Array, default: () => [] },
    serviceCategories: { type: Array, default: () => [] },
    expertBullets: { type: Array, default: () => [] },
    visaRoutes: { type: Array, default: () => [] },
    urgencyCards: { type: Array, default: () => [] },
    eligibleCountries: { type: Array, default: () => [] },
    processSteps: { type: Array, default: () => [] },
    documentGroups: { type: Array, default: () => [] },
    documentHighlights: { type: Array, default: () => [] },
    testimonials: { type: Array, default: () => [] },
    faqItems: { type: Array, default: () => [] },
    contactPoints: { type: Array, default: () => [] },
});

let observer;
const activeFaq = ref(0);

const defaultInterest = computed(() => {
    const visaOption = props.interestOptions?.find((option) => /visa|schengen/i.test(option));
    if (visaOption) {
        return visaOption;
    }
    if (props.interestOptions?.includes('General Planning')) {
        return 'General Planning';
    }
    return props.interestOptions?.[0] ?? 'General Planning';
});

const heroBackdrop = computed(() => props.visualGallery?.[0]?.image ?? '');

const formDefaults = {
    source: 'visa-landing-page',
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 1,
    interest: defaultInterest.value,
    message: 'I need Schengen visa assistance. Please contact me with the required documents, country options, and next steps.',
};

const form = useForm({ ...formDefaults });

const resetLeadForm = () => {
    form.reset('name', 'email', 'phone', 'travel_date', 'guest_count');
    form.message = formDefaults.message;
    form.interest = defaultInterest.value;
};

const submit = () => {
    form.interest = defaultInterest.value;
    form.post('/inquiries', {
        preserveScroll: true,
        onSuccess: () => resetLeadForm(),
    });
};

onMounted(() => {
    const nodes = document.querySelectorAll('.visa-v2-reveal');
    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -6% 0px' },
    );
    nodes.forEach((node) => observer.observe(node));
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="visa-v2">
        <!-- Hero: imagery + overlay (inspired by schengen.acutetourism.org) -->
        <section
            class="visa-v2-hero"
            :style="heroBackdrop ? { '--visa-v2-hero-bg': `url(${heroBackdrop})` } : {}"
        >
            <div class="visa-v2-hero__gradient" aria-hidden="true"></div>
            <div class="container visa-v2-hero__content">
                <p class="eyebrow visa-v2-hero__eyebrow">{{ hero.eyebrow }}</p>
                <h1 class="hero-title visa-v2-hero__title">{{ hero.title }}</h1>
                <p class="hero-copy visa-v2-hero__lead">{{ hero.description }}</p>

                <ul class="visa-v2-hero__bullets">
                    <li v-for="item in hero.highlights" :key="item">{{ item }}</li>
                </ul>

                <div class="visa-v2-hero__cta">
                    <a class="button-primary visa-cta-primary" href="#visa-form">Start your request</a>
                    <Link class="button-secondary visa-cta-secondary" href="/visa-services">All visa services</Link>
                </div>

                <div class="visa-v2-stats">
                    <article v-for="stat in hero.stats" :key="stat.label" class="visa-v2-stat">
                        <strong>{{ stat.value }}</strong>
                        <span>{{ stat.label }}</span>
                    </article>
                </div>
            </div>
        </section>

        <nav v-if="anchorLinks.length" class="visa-v2-subnav" aria-label="On this page">
            <div class="container visa-v2-subnav__inner">
                <a v-for="link in anchorLinks" :key="link.href" class="visa-v2-subnav__link" :href="link.href">
                    {{ link.label }}
                </a>
            </div>
        </nav>

        <section v-if="visualGallery.length > 1" class="visa-v2-media-ribbon">
            <div class="container">
                <div class="visa-v2-media-ribbon__grid">
                    <article
                        v-for="(item, index) in visualGallery.slice(1)"
                        :key="item.image + index"
                        class="visa-v2-media-ribbon__card"
                    >
                        <img :src="item.image" :alt="item.title" loading="lazy" />
                        <span>{{ item.title }}</span>
                    </article>
                </div>
            </div>
        </section>

        <!-- What we offer -->
        <section id="offer" class="section-block visa-v2-section visa-v2-reveal">
            <div class="container">
                <header class="visa-v2-section__head">
                    <p class="eyebrow">What we offer</p>
                    <h2>Service categories</h2>
                    <p class="visa-v2-section__sub">
                        Planning, visas, and local support organized so clients are not forced through fragmented vendor decisions.
                    </p>
                </header>
                <div class="visa-v2-offer-grid">
                    <article v-for="cat in serviceCategories" :key="cat.title" class="visa-v2-offer-card">
                        <p class="card-tag">{{ cat.eyebrow }}</p>
                        <h3>{{ cat.title }}</h3>
                        <p>{{ cat.copy }}</p>
                        <Link class="button-secondary card-button" :href="cat.href">{{ cat.cta }}</Link>
                    </article>
                </div>
            </div>
        </section>

        <!-- Expert + routes -->
        <section id="routes" class="section-block section-contrast visa-v2-section visa-v2-reveal">
            <div class="container">
                <header class="visa-v2-section__head">
                    <p class="eyebrow">Expert assistance</p>
                    <h2>Visa services</h2>
                    <p class="visa-v2-section__sub">
                        Professional visa help with document review, application support, and practical travel guidance.
                    </p>
                </header>
                <ul class="visa-v2-expert-bullets">
                    <li v-for="b in expertBullets" :key="b">{{ b }}</li>
                </ul>
                <div class="visa-v2-route-scroll">
                    <a
                        v-for="route in visaRoutes"
                        :key="route.code + route.title"
                        class="visa-v2-route-card"
                        :class="{ 'visa-v2-route-card--featured': route.featured }"
                        :href="route.href"
                    >
                        <span class="visa-v2-route-card__code">{{ route.code }}</span>
                        <span class="visa-v2-route-card__title">{{ route.title }}</span>
                        <span class="visa-v2-route-card__sub">{{ route.subtitle }}</span>
                        <span class="visa-v2-route-card__cta">View route</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Appointment windows -->
        <section class="section-block visa-v2-section visa-v2-reveal">
            <div class="container">
                <header class="visa-v2-section__head">
                    <p class="eyebrow">Planning timelines</p>
                    <h2>Appointment windows</h2>
                    <p class="visa-v2-section__sub">Choose the pace that matches how ready your file is.</p>
                </header>
                <div class="visa-v2-urgency-grid">
                    <article v-for="card in urgencyCards" :key="card.title" class="visa-v2-urgency-card">
                        <div class="visa-v2-urgency-card__media">
                            <img :src="card.image" :alt="card.title" loading="lazy" />
                        </div>
                        <p class="card-tag">{{ card.eyebrow }}</p>
                        <h3>{{ card.title }}</h3>
                        <p>{{ card.copy }}</p>
                        <span class="card-tag-accent">{{ card.badge }}</span>
                        <a class="button-primary card-button" href="#visa-form">Start request</a>
                    </article>
                </div>
            </div>
        </section>

        <!-- Journey -->
        <section id="process" class="section-block section-contrast visa-v2-section visa-v2-reveal">
            <div class="container">
                <header class="visa-v2-section__head">
                    <p class="eyebrow">How it works</p>
                    <h2>Your journey with us</h2>
                </header>
                <div class="visa-v2-process">
                    <article v-for="(step, index) in processSteps" :key="step.title" class="visa-v2-process-step">
                        <span class="visa-v2-process-step__num">{{ String(index + 1).padStart(2, '0') }}</span>
                        <h3>{{ step.title }}</h3>
                        <p>{{ step.copy }}</p>
                    </article>
                </div>
                <div class="visa-v2-inline-cta">
                    <a class="button-primary visa-cta-primary" href="#visa-form">Start your request</a>
                    <Link class="button-secondary visa-cta-secondary" href="/contact">Contact Acute Tourism</Link>
                </div>
            </div>
        </section>

        <!-- Countries -->
        <section id="countries" class="section-block visa-v2-section visa-v2-reveal">
            <div class="container">
                <header class="visa-v2-section__head">
                    <p class="eyebrow">Coverage</p>
                    <h2>Schengen area countries</h2>
                    <p class="visa-v2-section__sub">Broad coverage with one clear next step: talk to us about your main destination.</p>
                </header>
                <div class="visa-v2-country-cloud">
                    <span v-for="country in eligibleCountries" :key="country" class="visa-v2-country-pill">{{ country }}</span>
                </div>
            </div>
        </section>

        <!-- Documents -->
        <section id="documents" class="section-block section-contrast visa-v2-section visa-v2-reveal">
            <div class="container">
                <header class="visa-v2-section__head">
                    <p class="eyebrow">Checklist</p>
                    <h2>Documents & requirements</h2>
                </header>
                <div class="visa-v2-doc-highlights">
                    <article v-for="item in documentHighlights" :key="item.title" class="info-card visa-v2-doc-chip">
                        <p class="card-tag">{{ item.title }}</p>
                        <p>{{ item.copy }}</p>
                    </article>
                </div>
                <div class="visa-v2-doc-groups">
                    <article v-for="group in documentGroups" :key="group.title" class="info-card visa-v2-doc-group">
                        <p class="card-tag">{{ group.title }}</p>
                        <ul class="feature-list">
                            <li v-for="item in group.items" :key="item">{{ item }}</li>
                        </ul>
                    </article>
                </div>
                <div class="visa-v2-inline-cta">
                    <a class="button-primary visa-cta-primary" href="#visa-form">Review my file</a>
                    <Link class="button-secondary visa-cta-secondary" href="/contact">Contact Acute Tourism</Link>
                </div>
            </div>
        </section>

        <!-- Stories -->
        <section class="section-block visa-v2-section visa-v2-reveal">
            <div class="container">
                <header class="visa-v2-section__head">
                    <p class="eyebrow">Client stories</p>
                    <h2>What travelers say</h2>
                </header>
                <div class="visa-v2-testimonials">
                    <blockquote v-for="t in testimonials" :key="t.name" class="visa-v2-quote">
                        <p>“{{ t.quote }}”</p>
                        <footer>
                            <strong>{{ t.name }}</strong>
                            <span v-if="t.role" class="visa-v2-quote__role">{{ t.role }}</span>
                            <span class="visa-v2-quote__badge">Verified review</span>
                        </footer>
                    </blockquote>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="section-block section-contrast visa-v2-section visa-v2-reveal">
            <div class="container">
                <header class="visa-v2-section__head">
                    <p class="eyebrow">FAQ</p>
                    <h2>Frequently asked questions</h2>
                </header>
                <div class="faq-stack visa-faq-stack">
                    <article v-for="(item, index) in faqItems" :key="item.question" class="faq-item visa-faq-item">
                        <button class="visa-faq-trigger" type="button" @click="activeFaq = activeFaq === index ? -1 : index">
                            <h3>{{ item.question }}</h3>
                            <span class="visa-faq-icon" :class="{ 'is-open': activeFaq === index }">+</span>
                        </button>
                        <div v-show="activeFaq === index" class="visa-faq-answer">
                            <p>{{ item.answer }}</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- Contact -->
        <section id="contact-us" class="section-block visa-v2-section visa-v2-reveal visa-v2-contact">
            <div class="container">
                <div class="visa-v2-contact__grid">
                    <div class="visa-v2-contact__copy">
                        <header class="visa-v2-section__head">
                            <p class="eyebrow">Start your journey</p>
                            <h2>Ready to travel smarter?</h2>
                            <p class="visa-v2-section__sub">
                                Contact Acute Tourism for direct visa and travel support. Visit the office or use the inquiry form to start with clearer next steps.
                            </p>
                        </header>
                        <div class="visa-v2-contact-points">
                            <article v-for="item in contactPoints" :key="item.label" class="visa-v2-contact-point">
                                <p class="card-tag">{{ item.label }}</p>
                                <p>{{ item.value }}</p>
                            </article>
                        </div>
                        <div class="visa-v2-inline-cta">
                            <Link class="button-primary visa-cta-primary" href="/contact">Contact Acute Tourism</Link>
                            <Link class="button-secondary visa-cta-secondary" href="/visa-services">All visa services</Link>
                        </div>
                    </div>

                    <article class="hero-panel visa-v2-form-card">
                        <p class="panel-label">Contact form</p>
                        <h2>Send your Schengen request</h2>
                        <p class="meta-copy">We reply with next steps and a document checklist.</p>

                        <form id="visa-form" class="lead-form" @submit.prevent="submit">
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
                                    <span>Expected travel date</span>
                                    <input v-model="form.travel_date" type="date" />
                                    <small v-if="form.errors.travel_date">{{ form.errors.travel_date }}</small>
                                </label>
                            </div>
                            <label class="field">
                                <span>Message</span>
                                <textarea v-model="form.message" rows="4"></textarea>
                                <small v-if="form.errors.message">{{ form.errors.message }}</small>
                            </label>
                            <button class="button-primary visa-submit visa-cta-primary" type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Sending...' : 'Submit request' }}
                            </button>
                        </form>
                    </article>
                </div>
            </div>
        </section>
    </div>
</template>
