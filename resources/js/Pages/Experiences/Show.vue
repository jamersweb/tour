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

    <section class="hero-section">
        <div class="container hero-grid">
            <div>
                <p class="eyebrow">{{ experience.category }}</p>
                <h1 class="hero-title">{{ experience.title }}</h1>
                <p class="hero-copy">{{ experience.heroSummary || experience.shortDescription }}</p>

                <div class="tag-row">
                    <span v-if="experience.tag" class="filter-chip active">{{ experience.tag }}</span>
                    <span class="filter-chip active">{{ experience.duration }}</span>
                    <span class="filter-chip active">{{ experience.location }}</span>
                    <span v-if="experience.isPrivate" class="filter-chip active">Private Available</span>
                </div>
            </div>

            <div class="hero-panel">
                <div v-if="experience.heroImageUrl" class="panel-media">
                    <img :src="experience.heroImageUrl" :alt="experience.title" />
                </div>
                <p class="panel-label">From</p>
                <h2 class="detail-price">{{ experience.priceFrom }}</h2>
                <p class="hero-copy">Production checkout will use Network Payment Gateway after the inquiry-led phase.</p>

                <div class="hero-actions">
                    <a class="button-primary" href="#experience-inquiry">Plan this experience</a>
                    <Link v-if="experience.priceFrom" class="button-secondary" :href="`/checkout/experiences/${experience.slug}`">
                        Pay online
                    </Link>
                    <Link class="button-secondary" href="/experiences">Back to experiences</Link>
                </div>
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container detail-grid">
            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Overview</p>
                    <h3>{{ experience.shortDescription }}</h3>
                    <p>{{ experience.description }}</p>
                </article>

                <article class="info-card">
                    <p class="card-tag">Highlights</p>
                    <ul class="feature-list">
                        <li v-for="highlight in experience.highlights" :key="highlight">{{ highlight }}</li>
                    </ul>
                </article>

                <article v-if="experience.galleryImageUrls.length" class="info-card">
                    <p class="card-tag">Gallery</p>
                    <div class="gallery-grid">
                        <div v-for="image in experience.galleryImageUrls" :key="image" class="gallery-card">
                            <img :src="image" :alt="experience.title" />
                        </div>
                    </div>
                </article>
            </div>

            <div class="detail-stack">
                <article class="info-card">
                    <p class="card-tag">Service Notes</p>
                    <div class="meta-stack">
                        <div>
                            <strong>Pickup</strong>
                            <p>{{ experience.pickupNote || 'Pickup details will be confirmed during planning.' }}</p>
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
                    <p class="card-tag">Inclusions</p>
                    <ul class="feature-list">
                        <li v-for="item in experience.inclusions" :key="item">{{ item }}</li>
                    </ul>
                </article>

                <article class="info-card">
                    <p class="card-tag">Exclusions</p>
                    <ul class="feature-list">
                        <li v-for="item in experience.exclusions" :key="item">{{ item }}</li>
                    </ul>
                </article>

                <article id="experience-inquiry" class="info-card">
                    <p class="card-tag">Plan This Experience</p>
                    <h3>Send a lead with the experience already attached.</h3>
                    <p class="hero-copy">
                        This inquiry goes into the admin pipeline with
                        <strong>{{ experience.title }}</strong> linked as the requested product.
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
                <p class="eyebrow">Related Experiences</p>
                <h2>Keep visitors inside the curated journey.</h2>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="item in relatedExperiences" :key="item.slug" class="info-card">
                    <div v-if="item.heroImageUrl" class="card-media">
                        <img :src="item.heroImageUrl" :alt="item.title" />
                    </div>
                    <p class="card-tag">{{ item.category }}</p>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.duration }}</p>
                    <p class="price-line">{{ item.priceFrom }}</p>
                    <Link class="card-link" :href="`/experiences/${item.slug}`">View experience</Link>
                </article>
            </div>
        </div>
    </section>
</template>
