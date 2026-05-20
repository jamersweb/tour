<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const page = usePage();

const props = defineProps({
    seo: Object,
    hero: Object,
    homeSections: Object,
    collections: Array,
    featuredExperiences: Array,
    mustDoExperiences: Array,
    topRatedExperiences: Array,
    heroGallery: Array,
    packages: Array,
    packageCategories: Array,
    recommendations: Array,
    serviceFocus: Array,
    testimonials: Array,
    trustProof: Object,
});

const videoRef = ref(null);
const mobileMediaRibbon = ref(
    typeof window !== 'undefined' && window.matchMedia('(max-width: 768px)').matches,
);
const reduceMotion = ref(
    typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches,
);

const heroAvatars = computed(() => props.heroGallery.slice(0, 4));
const mustDoCards = computed(() => props.mustDoExperiences.slice(0, 4));
const topRatedCards = computed(() => (
    props.topRatedExperiences && props.topRatedExperiences.length > 0
        ? props.topRatedExperiences
        : props.mustDoExperiences
).slice(0, 4));
const momentCards = computed(() => props.heroGallery.slice(0, 6));
const serviceCards = computed(() => props.serviceFocus.slice(0, 3));
const activeSlides = ref({
    mustDo: 0,
    topRated: 0,
    testimonials: 0,
});

function serviceFocusWhatsappUrl(item) {
    const raw = page.props.site?.contact?.whatsappNumber;
    const number = raw ? String(raw).replace(/[^0-9]/g, '') : '';
    if (!number) {
        return null;
    }
    const text = encodeURIComponent(
        `Hi Acute Tourism, I'd like to book or enquire about ${item.title}.`,
    );

    return `https://wa.me/${number}?text=${text}`;
}

function updateCarousel(name, event) {
    const el = event.currentTarget;
    const card = el.querySelector('[data-carousel-card]');
    if (!card) {
        return;
    }

    const styles = window.getComputedStyle(el);
    const gap = Number.parseFloat(styles.columnGap || styles.gap || '0') || 0;
    const step = card.getBoundingClientRect().width + gap;
    const index = step > 0 ? Math.round(el.scrollLeft / step) : 0;

    activeSlides.value = {
        ...activeSlides.value,
        [name]: index,
    };
}

let detachMediaListeners = () => {};

onMounted(() => {
    const mqMobile = window.matchMedia('(max-width: 768px)');
    const mqReduce = window.matchMedia('(prefers-reduced-motion: reduce)');

    const syncMobile = () => {
        mobileMediaRibbon.value = mqMobile.matches;
    };

    const syncReduce = () => {
        reduceMotion.value = mqReduce.matches;
        if (mqReduce.matches) {
            const el = videoRef.value;
            el?.pause?.();
        }
    };

    syncMobile();
    syncReduce();

    if (mqReduce.matches) {
        const el = videoRef.value;
        el?.pause?.();
    }

    mqMobile.addEventListener('change', syncMobile);
    mqReduce.addEventListener('change', syncReduce);

    detachMediaListeners = () => {
        mqMobile.removeEventListener('change', syncMobile);
        mqReduce.removeEventListener('change', syncReduce);
    };
});

onUnmounted(() => detachMediaListeners());
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="home-dashboard">
        <section class="home-hero-video home-dashboard-hero" aria-label="Welcome">
            <div class="home-hero-video__media">
                <video
                    v-if="hero.videoUrl && !mobileMediaRibbon && !reduceMotion"
                    ref="videoRef"
                    class="home-hero-video__el"
                    :src="hero.videoUrl"
                    :poster="hero.heroImageUrl || undefined"
                    preload="metadata"
                    autoplay
                    muted
                    loop
                    playsinline
                ></video>
                <img
                    v-else-if="hero.heroImageUrl"
                    class="home-hero-video__el home-hero-video__el--img"
                    :src="hero.heroImageUrl"
                    :alt="hero.title"
                    width="2000"
                    height="1325"
                    fetchpriority="high"
                    decoding="async"
                />
            </div>
            <div class="home-hero-video__scrim" aria-hidden="true"></div>
            <div class="home-hero-video__content home-dashboard-hero__content home-hero-motion">
                <p class="home-hero-video__eyebrow">{{ hero.eyebrow }}</p>
                <h1 class="home-hero-video__title">
                    <span class="home-title-desktop">{{ hero.title }}</span>
                    <span class="home-title-mobile">{{ hero.mobileTitle || hero.title }}</span>
                </h1>
                <p class="home-hero-video__lead">{{ hero.description }}</p>
                <div class="home-hero-video__proof">
                    <div class="home-hero-video__avatars" aria-hidden="true">
                        <span
                            v-for="item in heroAvatars"
                            :key="item.slug"
                            class="home-hero-video__avatar"
                        >
                            <img :src="item.image" :alt="item.title" />
                        </span>
                    </div>
                    <div class="home-hero-video__proof-copy">
                        <strong>2,500+</strong>
                        <span>travelers</span>
                    </div>
                </div>
                <p class="home-hero-video__trust">{{ hero.trustLine }}</p>
                <div class="home-hero-video__actions">
                    <Link class="home-hero-video__cta home-hero-video__cta--solid" :href="hero.primaryCta.href">
                        {{ hero.primaryCta.label }}
                    </Link>
                    <div class="home-hero-video__secondary-actions">
                        <Link class="home-hero-video__secondary-link" :href="hero.secondaryCta.href">
                            {{ hero.secondaryCta.label }}
                        </Link>
                        <Link
                            v-if="hero.tertiaryCta"
                            class="home-hero-video__secondary-link"
                            :href="hero.tertiaryCta.href"
                        >
                            {{ hero.tertiaryCta.label }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-trust-strip" aria-label="Acute Tourism proof">
            <div class="container home-trust-strip__inner">
                <span>2,500+ happy travelers</span>
                <span>Serving Dubai travelers since 2013</span>
                <span>Licensed operator</span>
                <span>WhatsApp replies within 2 hours</span>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light home-routing-section" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading home-dashboard-heading--center">
                    <div>
                        <h2>Choose your travel path</h2>
                    </div>
                </div>
                <div class="home-routing-grid">
                    <article
                        v-for="item in serviceCards"
                        :key="item.title"
                        class="home-routing-card"
                    >
                        <p class="home-routing-card__tag">{{ item.tag }}</p>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.copy }}</p>
                        <div class="home-routing-card__actions">
                            <Link class="home-dashboard-card__button" :href="item.href">{{ item.cta }}</Link>
                            <a
                                v-if="serviceFocusWhatsappUrl(item)"
                                class="home-routing-card__whatsapp"
                                :href="serviceFocusWhatsappUrl(item)"
                                target="_blank"
                                rel="noreferrer"
                            >
                                Chat on WhatsApp
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>Featured Dubai Tours &amp; Experiences</h2>
                    </div>
                    <Link class="home-dashboard-more" href="/experiences">View All Tours</Link>
                </div>
                <div
                    class="home-dashboard-grid home-dashboard-grid--four home-dashboard-grid--featured home-mobile-carousel"
                    @scroll.passive="updateCarousel('mustDo', $event)"
                >
                    <article
                        v-for="experience in mustDoCards"
                        :key="experience.slug"
                        class="home-dashboard-card"
                        data-carousel-card
                    >
                        <Link class="home-dashboard-card__media" :href="`/experiences/${experience.slug}`">
                            <img
                                v-if="experience.heroImageUrl"
                                :src="experience.heroImageUrl"
                                :alt="experience.title"
                                width="640"
                                height="530"
                                loading="lazy"
                                decoding="async"
                            />
                            <span class="home-dashboard-badge">{{ experience.tag || experience.category }}</span>
                        </Link>
                        <div class="home-dashboard-card__body">
                            <h3>{{ experience.title }}</h3>
                            <p>{{ experience.summary }}</p>
                            <div class="home-dashboard-card__meta">
                                <span>{{ experience.location || experience.duration }}</span>
                                <strong>{{ experience.priceFrom }}</strong>
                            </div>
                            <Link class="home-dashboard-card__button" :href="`/experiences/${experience.slug}`">View Tour</Link>
                        </div>
                    </article>
                </div>
                <div class="home-carousel-dots" aria-hidden="true">
                    <span
                        v-for="(_, index) in mustDoCards"
                        :key="index"
                        :class="{ 'is-active': activeSlides.mustDo === index }"
                    ></span>
                </div>
                <Link class="home-mobile-view-all" href="/experiences">View All Tours</Link>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>Top-Rated Dubai Experiences</h2>
                    </div>
                    <Link class="home-dashboard-more" href="/experiences">View All Tours</Link>
                </div>
                <div
                    class="home-dashboard-grid home-dashboard-grid--four home-dashboard-grid--featured home-mobile-carousel"
                    @scroll.passive="updateCarousel('topRated', $event)"
                >
                    <article
                        v-for="experience in topRatedCards"
                        :key="experience.slug"
                        class="home-dashboard-card"
                        data-carousel-card
                    >
                        <Link class="home-dashboard-card__media" :href="`/experiences/${experience.slug}`">
                            <img
                                v-if="experience.heroImageUrl"
                                :src="experience.heroImageUrl"
                                :alt="experience.title"
                                width="640"
                                height="530"
                                loading="lazy"
                                decoding="async"
                            />
                            <span class="home-dashboard-badge">{{ experience.tag || experience.category }}</span>
                        </Link>
                        <div class="home-dashboard-card__body">
                            <h3>{{ experience.title }}</h3>
                            <p>{{ experience.summary }}</p>
                            <div class="home-dashboard-card__meta">
                                <span>{{ experience.location || experience.duration }}</span>
                                <strong>{{ experience.priceFrom }}</strong>
                            </div>
                            <Link class="home-dashboard-card__button" :href="`/experiences/${experience.slug}`">Check Availability</Link>
                        </div>
                    </article>
                </div>
                <div class="home-carousel-dots" aria-hidden="true">
                    <span
                        v-for="(_, index) in topRatedCards"
                        :key="index"
                        :class="{ 'is-active': activeSlides.topRated === index }"
                    ></span>
                </div>
                <Link class="home-mobile-view-all" href="/experiences">View All Tours</Link>
            </div>
        </section>

        <section v-if="momentCards.length" class="home-dashboard-section home-dashboard-section--light" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>Curated <em>Travel</em> Moments</h2>
                    </div>
                </div>
                <div class="home-dashboard-moments">
                    <div class="home-dashboard-mosaic">
                        <Link
                            v-for="item in momentCards.slice(0, 5)"
                            :key="item.slug"
                            class="home-dashboard-mosaic__item"
                            :href="`/experiences/${item.slug}`"
                        >
                            <img :src="item.image" :alt="item.title" width="520" height="520" loading="lazy" decoding="async" />
                        </Link>
                    </div>
                    <div class="home-dashboard-feature-video">
                        <img
                            :src="momentCards[5]?.image || momentCards[0]?.image"
                            :alt="momentCards[0]?.title || 'Travel moment'"
                            width="900"
                            height="760"
                            loading="lazy"
                            decoding="async"
                        />
                        <div class="home-dashboard-feature-video__overlay">
                            <strong>{{ momentCards[0]?.title }}</strong>
                            <span>{{ momentCards[0]?.tag }}</span>
                        </div>
                        <Link class="home-dashboard-feature-video__button" href="/experiences">View All Tours</Link>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>Our Top <em>Holiday</em> Packages</h2>
                    </div>
                    <Link class="home-dashboard-more" href="/packages">View All Packages</Link>
                </div>
                <div class="home-dashboard-grid home-dashboard-grid--three">
                    <article v-for="item in packageCategories" :key="item.title" class="home-dashboard-card home-dashboard-card--package home-dashboard-card--package-category">
                        <Link class="home-dashboard-card__media" :href="item.href">
                            <img
                                v-if="item.image"
                                :src="item.image"
                                :alt="item.title"
                                width="640"
                                height="530"
                                loading="lazy"
                                decoding="async"
                            />
                        </Link>
                        <div class="home-dashboard-card__body">
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.summary }}</p>
                            <div class="home-dashboard-card__meta">
                                <span>{{ item.detail }}</span>
                                <strong>{{ item.priceLine }}</strong>
                            </div>
                            <Link class="home-dashboard-card__button home-dashboard-card__button--light" :href="item.href">{{ item.cta }}</Link>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>We are the Best <em>Company</em> for your Visit</h2>
                        <p class="home-dashboard-subcopy">{{ homeSections.companyCopy }}</p>
                    </div>
                </div>
                <div class="home-dashboard-company">
                    <div class="home-dashboard-company__feature">
                        <img
                            v-if="momentCards[0]"
                            :src="momentCards[0].image"
                            :alt="momentCards[0].title"
                            width="760"
                            height="620"
                            loading="lazy"
                            decoding="async"
                        />
                        <div class="home-dashboard-company__feature-stat">
                            <strong>4000+</strong>
                            <span>Customer Reviews</span>
                        </div>
                    </div>
                    <div class="home-dashboard-company__side">
                        <div class="home-dashboard-company__mini">
                            <img
                                v-if="momentCards[1]"
                                :src="momentCards[1].image"
                                :alt="momentCards[1].title"
                                width="420"
                                height="240"
                                loading="lazy"
                                decoding="async"
                            />
                            <div>
                                <strong>12</strong>
                                <span>years of experience</span>
                            </div>
                        </div>
                        <div class="home-dashboard-company__mini">
                            <img
                                v-if="momentCards[2]"
                                :src="momentCards[2].image"
                                :alt="momentCards[2].title"
                                width="420"
                                height="240"
                                loading="lazy"
                                decoding="async"
                            />
                            <div>
                                <strong>40+</strong>
                                <span>partners and services</span>
                            </div>
                        </div>
                        <div class="home-dashboard-company__copy">
                            <h3>See What They Say <em>About Us</em></h3>
                            <p>{{ testimonials[0]?.quote }}</p>
                            <Link class="home-dashboard-card__button" href="/contact">Talk to Acute Tourism</Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light home-proof-section" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading home-dashboard-heading--center">
                    <div>
                        <h2>Proof before you book</h2>
                    </div>
                </div>
                <div class="home-proof-grid">
                    <article class="home-proof-card home-proof-card--review">
                        <p class="home-dashboard-service-card__tag">{{ trustProof.reviewSource || 'Reviews' }}</p>
                        <h3>Verified traveler feedback</h3>
                        <p>Read public reviews before you enquire, then confirm the exact service details with the Acute team.</p>
                        <a
                            v-if="trustProof.reviewsUrl"
                            class="home-dashboard-card__button"
                            :href="trustProof.reviewsUrl"
                            target="_blank"
                            rel="noreferrer"
                        >
                            Read All {{ trustProof.reviewSource }} Reviews
                        </a>
                    </article>
                    <article class="home-proof-card">
                        <p class="home-dashboard-service-card__tag">License</p>
                        <h3>{{ trustProof.licenseText }}</h3>
                        <p>Ask the team to verify license details before payment if you need the certificate or registration reference for your records.</p>
                    </article>
                    <article class="home-proof-card">
                        <p class="home-dashboard-service-card__tag">Payment</p>
                        <h3>Secure booking support</h3>
                        <p>{{ trustProof.paymentText }}</p>
                    </article>
                    <article class="home-proof-card home-proof-card--wide">
                        <p class="home-dashboard-service-card__tag">Operating proof</p>
                        <h3>One team coordinates the trip details.</h3>
                        <ul>
                            <li v-for="item in trustProof.partnerProof" :key="item">{{ item }}</li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light home-dashboard-section--reviews" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>What our travelers say</h2>
                    </div>
                </div>
                <div
                    class="home-dashboard-grid home-dashboard-grid--three home-mobile-carousel"
                    @scroll.passive="updateCarousel('testimonials', $event)"
                >
                    <article v-for="t in testimonials" :key="t.name" class="home-dashboard-review" data-carousel-card>
                        <p class="home-dashboard-review__stars">5/5 rating</p>
                        <p class="home-dashboard-review__quote">"{{ t.quote }}"</p>
                        <div class="home-dashboard-review__author">
                            <strong>{{ t.name }}</strong>
                            <span>{{ t.tag }}</span>
                        </div>
                    </article>
                </div>
                <div class="home-carousel-dots" aria-hidden="true">
                    <span
                        v-for="(_, index) in testimonials"
                        :key="index"
                        :class="{ 'is-active': activeSlides.testimonials === index }"
                    ></span>
                </div>
                <div v-if="trustProof.reviewsUrl" class="home-review-link-row">
                    <a :href="trustProof.reviewsUrl" target="_blank" rel="noreferrer">
                        Read All {{ trustProof.reviewSource }} Reviews
                    </a>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--royal" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading home-dashboard-heading--dark">
                    <div>
                        <h2>Visa services &amp; international travel</h2>
                        <p class="home-visa-disclaimer">
                            Acute Tourism provides visa application support and documentation guidance. Visa approval is at the sole discretion of the issuing embassy or consulate. Acute Tourism does not guarantee visa approval. Service fees are charged for assistance, not for visa issuance.
                        </p>
                    </div>
                </div>
                <div class="home-dashboard-grid home-dashboard-grid--three">
                    <article
                        v-for="item in recommendations"
                        :key="item.title"
                        class="home-dashboard-service-card"
                    >
                        <p class="home-dashboard-service-card__tag">{{ item.tag }}</p>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.summary }}</p>
                        <Link class="home-dashboard-card__button" :href="item.href">Check Requirements</Link>
                    </article>
                </div>
            </div>
        </section>
    </section>
</template>
