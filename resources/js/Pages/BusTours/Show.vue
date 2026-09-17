<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    listing: Object,
});

const page = usePage();
const listing = computed(() => props.listing || {});
const galleryImages = computed(() => {
    const images = [
        ...(listing.value.galleryImageUrls || []),
        listing.value.detailImageUrl,
        listing.value.cardImageUrl,
    ].filter(Boolean);

    return [...new Set(images)];
});
const form = useForm({
    source: 'bus-tour-listing-page',
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 2,
    interest: 'Panoramic Bus',
    message: '',
});

function submit() {
    const message = [
        `Preferred route: ${listing.value.title || 'Panoramic Bus'}`,
        `Guests: ${form.guest_count || 'Not provided'}`,
        `Message: ${form.message || 'No extra request provided.'}`,
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
            onSuccess: () => form.reset('name', 'email', 'phone', 'travel_date', 'guest_count', 'message'),
        });
}
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" :image="seo.image" />

    <main class="acute-bus-page">
        <section
            class="acute-hero"
            :style="listing.detailImageUrl ? { backgroundImage: `linear-gradient(180deg, rgba(6, 26, 99, 0.48), rgba(6, 26, 99, 0.9)), url('${listing.detailImageUrl}')` } : {}"
        >
            <div class="acute-hero-inner">
                <div class="acute-eyebrow">{{ listing.day || 'Panoramic Bus' }}</div>
                <h1>{{ listing.title }}</h1>
                <p>{{ listing.copy }}</p>
                <div class="acute-hero-actions">
                    <a class="acute-btn gold" href="#enquiry">Request Availability</a>
                    <Link class="acute-btn light" href="/luxury-bus-tour-dubai">All Bus Tours</Link>
                </div>
                <div class="acute-hero-facts">
                    <div class="acute-hero-fact">
                        <strong>{{ listing.price }}</strong>
                        <span>Starting price</span>
                    </div>
                    <div v-if="listing.label" class="acute-hero-fact">
                        <strong>{{ listing.label }}</strong>
                        <span>Experience type</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="acute-section">
            <div class="acute-container acute-private-grid">
                <div>
                    <div class="acute-eyebrow">Tour intro</div>
                    <h2 class="acute-heading">{{ listing.title }}</h2>
                    <p class="acute-copy">{{ listing.copy || listing.bestFor }}</p>
                    <p v-if="listing.bestFor" class="acute-copy acute-copy--small"><strong>Best for:</strong> {{ listing.bestFor }}</p>
                    <div class="acute-tags">
                        <span v-for="tag in listing.tags || []" :key="tag">{{ tag }}</span>
                    </div>
                </div>
                <div
                    v-if="listing.detailImageUrl || listing.cardImageUrl"
                    class="acute-private-image acute-listing-intro-image"
                    :style="{ backgroundImage: `linear-gradient(to top, rgba(6, 26, 99, 0.28), transparent 48%), url('${listing.detailImageUrl || listing.cardImageUrl}')` }"
                ></div>
            </div>
        </section>

        <section class="acute-section alt">
            <div class="acute-container">
                <div class="acute-section-head acute-section-head--compact">
                    <div class="acute-eyebrow">Tour details</div>
                    <h2 class="acute-heading">What to expect on this route</h2>
                    <p v-if="listing.tourDetails || listing.bestFor" class="acute-copy">{{ listing.tourDetails || listing.bestFor }}</p>
                </div>
                <div class="acute-info-cols">
                    <div class="acute-list">
                        <h4>Route highlights</h4>
                        <ul>
                            <li v-for="item in listing.highlights || []" :key="item">{{ item }}</li>
                        </ul>
                    </div>
                    <div class="acute-list">
                        <h4>Included</h4>
                        <ul>
                            <li v-for="item in listing.included || []" :key="item">{{ item }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="galleryImages.length" class="acute-section acute-media-showcase">
            <div class="acute-container">
                <div class="acute-section-head">
                    <div class="acute-eyebrow">Gallery</div>
                    <h2 class="acute-heading">Experience images</h2>
                </div>
                <div class="acute-gallery-carousel__track acute-listing-gallery-carousel">
                    <div
                        v-for="image in galleryImages"
                        :key="image"
                        class="acute-gallery-carousel__item"
                    >
                        <img :src="image" :alt="listing.title" loading="lazy" decoding="async" />
                    </div>
                </div>
            </div>
        </section>

        <section id="enquiry" class="acute-section acute-lead">
            <div class="acute-container acute-lead-grid">
                <div class="acute-lead-copy">
                    <div class="acute-eyebrow">Check availability</div>
                    <h2 class="acute-heading">Request this panoramic bus tour</h2>
                    <p class="acute-copy">Share your preferred date and group details. The Acute Tourism team will confirm availability, pick-up details and final timing before payment.</p>
                </div>
                <form class="acute-lead-form" @submit.prevent="submit">
                    <div v-if="page.props.flash.success" class="success-banner acute-full-field">{{ page.props.flash.success }}</div>
                    <div class="acute-form-grid">
                        <label>Full Name<input v-model="form.name" placeholder="Enter your name" required type="text" autocomplete="name" /></label>
                        <label>WhatsApp Number<input v-model="form.phone" placeholder="+971 5X XXX XXXX" required type="tel" autocomplete="tel" /></label>
                        <label>Guests<input v-model="form.guest_count" type="number" min="1" required /></label>
                        <label>Preferred Date<input v-model="form.travel_date" type="date" /></label>
                    </div>
                    <label class="acute-full-field">Additional Request<textarea v-model="form.message" placeholder="Hotel location, private group request, or any special occasion." rows="4"></textarea></label>
                    <button class="acute-btn gold acute-submit" type="submit" :disabled="form.processing">{{ form.processing ? 'Sending...' : 'Send Enquiry' }}</button>
                </form>
            </div>
        </section>
    </main>
</template>
