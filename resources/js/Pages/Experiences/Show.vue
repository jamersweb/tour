<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    experience: Object,
    inquiryDefaults: Object,
    relatedExperiences: Array,
});

const page = usePage();

const form = useForm({
    experience_slug: props.experience.slug,
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 2,
    interest: props.inquiryDefaults.interest,
    message: props.inquiryDefaults.message,
});

const submit = () => {
    form.post('/inquiries', {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'email', 'phone', 'travel_date', 'guest_count'),
    });
};
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" :image="seo.image" />

    <section class="hero-section experience-hero">
        <div class="container experience-hero-grid">
            <div class="experience-story">
                <p class="eyebrow">{{ experience.category }}</p>
                <h1 class="hero-title">{{ experience.title }}</h1>
                <p class="hero-copy">{{ experience.heroSummary || experience.shortDescription }}</p>

                <div class="tag-row">
                    <span v-if="experience.tag" class="filter-chip active">{{ experience.tag }}</span>
                    <span class="filter-chip active">{{ experience.duration }}</span>
                    <span class="filter-chip active">{{ experience.location }}</span>
                    <span v-if="experience.isPrivate" class="filter-chip active">Private Available</span>
                </div>

                <div class="experience-metrics">
                    <article class="landing-stat">
                        <strong>{{ experience.priceFrom }}</strong>
                        <span>Starting rate</span>
                    </article>
                    <article class="landing-stat">
                        <strong>{{ experience.duration }}</strong>
                        <span>Experience length</span>
                    </article>
                    <article class="landing-stat">
                        <strong>{{ experience.location }}</strong>
                        <span>Primary setting</span>
                    </article>
                </div>
            </div>

            <div class="experience-stage">
                <article class="hero-panel">
                    <div v-if="experience.heroImageUrl" class="page-hero-media experience-hero-media">
                        <img :src="experience.heroImageUrl" :alt="experience.title" />
                    </div>

                    <div class="hero-actions">
                        <a class="button-primary" href="#experience-inquiry">Plan this experience</a>
                        <Link v-if="experience.priceFrom" class="button-secondary" :href="`/checkout/experiences/${experience.slug}`">
                            Pay online
                        </Link>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container detail-grid">
            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Why This Works</p>
                    <h3>{{ experience.shortDescription }}</h3>
                    <p>{{ experience.description }}</p>
                </article>

                <article class="info-card">
                    <p class="card-tag">Experience Highlights</p>
                    <ul class="feature-list">
                        <li v-for="highlight in experience.highlights" :key="highlight">{{ highlight }}</li>
                    </ul>
                </article>

                <article v-if="experience.galleryImageUrls.length" class="info-card">
                    <p class="card-tag">Visual Preview</p>
                    <div class="gallery-grid">
                        <div v-for="image in experience.galleryImageUrls" :key="image" class="gallery-card">
                            <img :src="image" :alt="experience.title" />
                        </div>
                    </div>
                </article>
            </div>

            <div class="detail-stack">
                <article class="info-card experience-service-card">
                    <p class="card-tag">Service Notes</p>

                    <div class="meta-stack">
                        <div>
                            <strong>Pickup</strong>
                            <p>{{ experience.pickupNote || 'Pickup details are confirmed after planning.' }}</p>
                        </div>
                        <div>
                            <strong>Collections</strong>
                            <div class="tag-row">
                                <Link
                                    v-for="collection in experience.collections"
                                    :key="collection.slug"
                                    class="filter-chip"
                                    :href="`/collections/${collection.slug}`"
                                >
                                    {{ collection.name }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="info-card">
                    <p class="card-tag">Included</p>
                    <ul class="feature-list">
                        <li v-for="item in experience.inclusions" :key="item">{{ item }}</li>
                    </ul>
                </article>

                <article class="info-card">
                    <p class="card-tag">Not Included</p>
                    <ul class="feature-list">
                        <li v-for="item in experience.exclusions" :key="item">{{ item }}</li>
                    </ul>
                </article>

                <article id="experience-inquiry" class="info-card inquiry-card">
                    <p class="card-tag">Plan This Experience</p>
                    <h3>Ask for availability, timing, and the best-fit option.</h3>
                    <p class="hero-copy">
                        This request goes straight into the sales pipeline with
                        <strong>{{ experience.title }}</strong> attached to the lead.
                    </p>

                    <div v-if="page.props.flash.success" class="success-banner">
                        {{ page.props.flash.success }}
                    </div>

                    <form class="lead-form" @submit.prevent="submit">
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
                                <span>Travel Date</span>
                                <input v-model="form.travel_date" type="date" />
                                <small v-if="form.errors.travel_date">{{ form.errors.travel_date }}</small>
                            </label>

                            <label class="field">
                                <span>Guests</span>
                                <input v-model="form.guest_count" type="number" min="1" max="100" />
                                <small v-if="form.errors.guest_count">{{ form.errors.guest_count }}</small>
                            </label>

                            <label class="field">
                                <span>Interest</span>
                                <select v-model="form.interest">
                                    <option
                                        v-for="option in page.props.site.interestOptions"
                                        :key="option"
                                        :value="option"
                                    >
                                        {{ option }}
                                    </option>
                                </select>
                                <small v-if="form.errors.interest">{{ form.errors.interest }}</small>
                            </label>
                        </div>

                        <label class="field">
                            <span>Message</span>
                            <textarea v-model="form.message" rows="5"></textarea>
                            <small v-if="form.errors.message">{{ form.errors.message }}</small>
                        </label>

                        <button class="button-primary" type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Sending...' : 'Send Experience Inquiry' }}
                        </button>
                    </form>
                </article>
            </div>
        </div>
    </section>

    <section v-if="relatedExperiences.length" class="section-block section-contrast">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Recommended Next</p>
                <h2>Keep the visitor inside a tighter premium shortlist.</h2>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="item in relatedExperiences" :key="item.slug" class="experience-tile">
                    <div v-if="item.heroImageUrl" class="showcase-media experience-tile-media">
                        <img :src="item.heroImageUrl" :alt="item.title" />
                    </div>
                    <div class="showcase-meta">
                        <span class="card-tag-ghost">{{ item.category }}</span>
                        <span class="card-tag-accent">{{ item.duration }}</span>
                    </div>
                    <h3>{{ item.title }}</h3>
                    <div class="experience-tile-footer">
                        <span>{{ item.category }}</span>
                        <strong>{{ item.priceFrom }}</strong>
                    </div>
                    <Link class="button-primary card-button" :href="`/experiences/${item.slug}`">View experience</Link>
                </article>
            </div>
        </div>
    </section>
</template>
