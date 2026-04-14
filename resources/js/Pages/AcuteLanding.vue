<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';

defineOptions({ layout: null });

const props = defineProps({
    seo: Object,
    contact: Object,
    hero: Object,
    serviceCategories: Array,
    featuredPackages: Array,
    visaCategories: Array,
    processSteps: Array,
    testimonials: Array,
    faqItems: Array,
});

const page = usePage();
const activeFaq = ref(0);

const whatsappHref = computed(() => {
    const number = props.contact?.whatsappNumber?.replace(/[^0-9]/g, '');
    return number ? `https://wa.me/${number}` : '#contact';
});

const phoneHref = computed(() => {
    const number = props.contact?.phone?.replace(/[^0-9+]/g, '');
    return number ? `tel:${number}` : '#contact';
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="acute-landing-page">
        <header class="acute-landing-nav">
            <div class="container acute-landing-nav-row">
                <a class="acute-landing-brand" href="#top" aria-label="Home">
                    <img
                        v-if="page.props.site.logoUrl"
                        class="acute-landing-logo"
                        :src="page.props.site.logoUrl"
                        alt=""
                    />
                </a>

                <nav class="acute-landing-links">
                    <a href="#services">Services</a>
                    <a href="#packages">Packages</a>
                    <a href="#visa">Visa Services</a>
                    <a href="#about">About</a>
                    <a href="#contact">Contact</a>
                </nav>

                <a class="acute-landing-gold-button" :href="whatsappHref" target="_blank" rel="noreferrer">
                    Book Consultation
                </a>
            </div>
        </header>

        <main id="top">
            <section
                class="acute-landing-hero"
                :style="{ '--acute-hero-image': `url(${hero.backgroundImage})` }"
            >
                <div class="container acute-landing-hero-grid">
                    <div class="acute-landing-copy">
                        <p class="acute-landing-eyebrow">{{ hero.eyebrow }}</p>
                        <h1>{{ hero.title }}</h1>
                        <p class="acute-landing-intro">{{ hero.description }}</p>

                        <div class="acute-landing-actions">
                            <a class="acute-landing-gold-button acute-landing-gold-button-wide" href="#packages">
                                Explore Packages
                            </a>
                            <a class="acute-landing-ghost-button" href="#visa">Visa Services</a>
                        </div>

                        <div class="acute-landing-stats">
                            <article v-for="stat in hero.stats" :key="stat.label">
                                <strong>{{ stat.value }}</strong>
                                <span>{{ stat.label }}</span>
                            </article>
                        </div>
                    </div>

                    <aside class="acute-landing-card">
                        <p class="acute-landing-card-label">Quick Contact</p>
                        <h2>Talk to Acute without extra steps.</h2>
                        <p>
                            Use the real Acute contact numbers directly from this page for travel planning, visa guidance,
                            and premium booking support.
                        </p>

                        <div class="acute-landing-contact-stack">
                            <a class="acute-landing-contact-pill" :href="phoneHref">
                                <span>Call</span>
                                <strong>{{ contact.phone }}</strong>
                                <span v-if="contact.phoneSecondary" class="acute-landing-contact-sub">{{ contact.phoneSecondary }}</span>
                            </a>
                            <a class="acute-landing-contact-pill" :href="whatsappHref" target="_blank" rel="noreferrer">
                                <span>WhatsApp</span>
                                <strong>{{ contact.whatsappNumber }}</strong>
                            </a>
                            <a class="acute-landing-contact-pill" :href="`mailto:${contact.email}`">
                                <span>Email</span>
                                <strong>{{ contact.email }}</strong>
                            </a>
                        </div>

                        <div class="acute-landing-card-actions">
                            <a class="acute-landing-gold-button acute-landing-gold-button-wide" :href="whatsappHref" target="_blank" rel="noreferrer">
                                WhatsApp Acute
                            </a>
                            <a class="acute-landing-ghost-button" :href="phoneHref">Call Now</a>
                        </div>
                    </aside>
                </div>
            </section>

            <section id="services" class="acute-landing-section">
                <div class="container">
                    <div class="acute-landing-heading">
                        <p class="acute-landing-eyebrow">What We Offer</p>
                        <h2>Service categories built in the same visual direction as the reference page.</h2>
                    </div>

                    <div class="acute-landing-service-grid">
                        <article v-for="item in serviceCategories" :key="item.title" class="acute-landing-service-card">
                            <div class="acute-landing-media">
                                <img :src="item.image" :alt="item.title" loading="lazy" />
                            </div>
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.copy }}</p>
                            <Link class="acute-landing-text-link" :href="item.href">Learn More</Link>
                        </article>
                    </div>
                </div>
            </section>

            <section id="packages" class="acute-landing-section acute-landing-section-soft">
                <div class="container">
                    <div class="acute-landing-heading">
                        <p class="acute-landing-eyebrow">Curated For You</p>
                        <h2>Featured packages with the same premium card treatment.</h2>
                    </div>

                    <div class="acute-landing-package-grid">
                        <article v-for="item in featuredPackages" :key="item.title" class="acute-landing-package-card">
                            <div class="acute-landing-media acute-landing-media-tall">
                                <img v-if="item.heroImageUrl" :src="item.heroImageUrl" :alt="item.title" loading="lazy" />
                                <div v-else class="acute-landing-fallback-media"></div>
                                <span class="acute-landing-price-badge">{{ item.priceFrom || 'Custom Quote' }}</span>
                            </div>
                            <div class="acute-landing-package-body">
                                <h3>{{ item.title }}</h3>
                                <p>{{ item.duration }}</p>
                                <ul class="acute-landing-badge-list">
                                    <li v-for="highlight in item.highlights" :key="highlight">{{ highlight }}</li>
                                </ul>
                                <Link class="acute-landing-gold-button acute-landing-gold-button-wide" :href="item.href">
                                    Book Now
                                </Link>
                            </div>
                        </article>
                    </div>

                    <div class="acute-landing-package-gallery">
                        <article v-for="item in featuredPackages" :key="`${item.title}-gallery`" class="acute-landing-package-gallery-card">
                            <h3>{{ item.title }} Gallery</h3>
                            <div class="acute-landing-package-gallery-grid">
                                <img
                                    v-for="image in (item.galleryImageUrls || [])"
                                    :key="`${item.title}-${image}`"
                                    :src="image"
                                    :alt="`${item.title} gallery image`"
                                    loading="lazy"
                                />
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="visa" class="acute-landing-section">
                <div class="container">
                    <div class="acute-landing-heading">
                        <p class="acute-landing-eyebrow">Expert Assistance</p>
                        <h2>Visa service cards styled like the source page, but branded for Acute.</h2>
                    </div>

                    <div class="acute-landing-visa-grid">
                        <Link v-for="item in visaCategories" :key="item.title" class="acute-landing-visa-card" :href="item.href">
                            <span class="acute-landing-visa-flag">{{ item.emoji }}</span>
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.copy }}</p>
                            <strong>Apply</strong>
                        </Link>
                    </div>
                </div>
            </section>

            <section id="about" class="acute-landing-section acute-landing-section-soft">
                <div class="container">
                    <div class="acute-landing-heading">
                        <p class="acute-landing-eyebrow">How It Works</p>
                        <h2>Your journey with Acute stays simple on one page.</h2>
                    </div>

                    <div class="acute-landing-process-grid">
                        <article v-for="item in processSteps" :key="item.step" class="acute-landing-process-card">
                            <div class="acute-landing-step">{{ item.step }}</div>
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.copy }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="acute-landing-section">
                <div class="container">
                    <div class="acute-landing-heading">
                        <p class="acute-landing-eyebrow">Client Stories</p>
                        <h2>Proof blocks adapted for the Acute version.</h2>
                    </div>

                    <div class="acute-landing-testimonial-grid">
                        <article v-for="item in testimonials" :key="item.name" class="acute-landing-testimonial-card">
                            <p class="acute-landing-quote">{{ item.quote }}</p>
                            <div>
                                <strong>{{ item.name }}</strong>
                                <span>{{ item.service }}</span>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="acute-landing-section acute-landing-section-soft">
                <div class="container">
                    <div class="acute-landing-heading">
                        <p class="acute-landing-eyebrow">FAQ</p>
                        <h2>Short answers for high-intent landing page traffic.</h2>
                    </div>

                    <div class="acute-landing-faq-list">
                        <article v-for="(item, index) in faqItems" :key="item.question" class="acute-landing-faq-item">
                            <button type="button" class="acute-landing-faq-trigger" @click="activeFaq = activeFaq === index ? -1 : index">
                                <span>{{ item.question }}</span>
                                <strong>{{ activeFaq === index ? '−' : '+' }}</strong>
                            </button>
                            <p v-show="activeFaq === index">{{ item.answer }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="contact" class="acute-landing-section">
                <div class="container">
                    <div class="acute-landing-cta-band">
                        <div>
                            <p class="acute-landing-eyebrow">Start Your Journey</p>
                            <h2>Ready to use the Acute logo, real numbers, and the approved landing-page style?</h2>
                            <p>
                                Call {{ contact.phone }} or message {{ contact.whatsappNumber }} to turn this landing page into a direct-response entry point.
                            </p>
                        </div>

                        <div class="acute-landing-actions">
                            <a class="acute-landing-gold-button acute-landing-gold-button-wide" :href="whatsappHref" target="_blank" rel="noreferrer">
                                WhatsApp Us
                            </a>
                            <a class="acute-landing-ghost-button" :href="phoneHref">Book a Call</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>
