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

const mustDoCards = computed(() => props.mustDoExperiences.slice(0, 4));
const topRatedCards = computed(() => (
    props.topRatedExperiences && props.topRatedExperiences.length > 0
        ? props.topRatedExperiences
        : props.mustDoExperiences
).slice(0, 4));
const serviceCards = computed(() => props.serviceFocus.slice(0, 3));
const visaCards = computed(() => props.recommendations.slice(0, 4));
const visaProcessSteps = computed(() => [
    {
        title: 'Choose route',
        copy: 'Select the destination and visa type that matches the trip.',
        icon: 'document',
    },
    {
        title: 'Review file',
        copy: 'A consultant checks key documents before you proceed.',
        icon: 'check',
    },
    {
        title: 'Proceed clearly',
        copy: 'Know expected timelines, fees, and next steps before applying.',
        icon: 'arrow',
    },
]);
const whyAcuteCards = computed(() => [
    {
        title: 'Local Experts',
        copy: 'Dubai-based guidance.',
        icon: 'pin',
        tone: 'gold',
    },
    {
        title: '2,500+ Customers',
        copy: 'Served across trips and visas.',
        icon: 'customer',
        tone: 'blue',
    },
    {
        title: 'Secure Payment',
        copy: 'Safe confirmation.',
        icon: 'card',
        tone: 'green',
    },
    {
        title: 'Group Friendly',
        copy: 'Solo, family, and groups.',
        icon: 'group',
        tone: 'orange',
    },
]);
const visaFlags = {
    australia: 'au',
    brazil: 'br',
    canada: 'ca',
    japan: 'jp',
    schengen: 'eu',
    'south africa': 'za',
    uk: 'gb',
    usa: 'us',
};
const activeSlides = ref({
    mustDo: 0,
    topRated: 0,
    packages: 0,
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

function visaFlag(item) {
    const title = String(item.title || '').toLowerCase();
    const key = Object.keys(visaFlags).find((name) => title.includes(name));

    return key ? visaFlags[key] : null;
}

function whyAcuteIconPath(icon) {
    return {
        pin: 'M12 21s7-4.8 7-11a7 7 0 0 0-14 0c0 6.2 7 11 7 11Zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z',
        customer: 'M15 11a4 4 0 1 0-8 0M3 21a8 8 0 0 1 16 0M19 8v6M22 11h-6',
        card: 'M3 7h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Zm0 4h18M7 15h4',
        group: 'M16 11a4 4 0 1 0-8 0M4 21a8 8 0 0 1 16 0M19 8h2M3 8h2',
    }[icon] || 'M12 5v14M5 12h14';
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
                <p class="home-hero-video__lead">
                    Search Dubai tours, attraction tickets, holiday packages, and visa assistance from one local travel team.
                </p>
                <div class="home-hero-rating" aria-label="Rated 4.9 out of 5 by more than 2,500 travelers">
                    <span aria-hidden="true">★★★★★</span>
                    <strong>4.9/5 by 2,500+ travelers</strong>
                </div>
                <form class="home-hero-search" action="/experiences" method="get" role="search">
                    <input
                        name="search"
                        type="search"
                        placeholder="Search tours, packages, visas..."
                        aria-label="Search Acute Tourism"
                    />
                    <button type="submit" aria-label="Search">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                    </button>
                </form>
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

        <section class="home-dashboard-section home-dashboard-section--light" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>Our Top <em>Holiday</em> Packages</h2>
                    </div>
                    <Link class="home-dashboard-more" href="/packages">View All Packages</Link>
                </div>
                <div
                    class="home-dashboard-grid home-dashboard-grid--three home-mobile-carousel"
                    @scroll.passive="updateCarousel('packages', $event)"
                >
                    <article v-for="item in packageCategories" :key="item.title" class="home-dashboard-card home-dashboard-card--package home-dashboard-card--package-category" data-carousel-card>
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
                            <div v-if="item.highlights?.length" class="home-package-highlights">
                                <span v-for="highlight in item.highlights" :key="highlight">{{ highlight }}</span>
                            </div>
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.summary }}</p>
                            <strong class="home-package-price">{{ item.priceLine }}</strong>
                            <div class="home-dashboard-card__meta">
                                <span>{{ item.detail }}</span>
                            </div>
                            <Link class="home-dashboard-card__button home-dashboard-card__button--light" :href="item.href">{{ item.cta }}</Link>
                        </div>
                    </article>
                </div>
                <div class="home-carousel-dots" aria-hidden="true">
                    <span
                        v-for="(_, index) in packageCategories"
                        :key="index"
                        :class="{ 'is-active': activeSlides.packages === index }"
                    ></span>
                </div>
                <Link class="home-mobile-view-all" href="/packages">View All Packages</Link>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light home-why-acute-section" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-why-acute-heading">
                    <p>Why Acute</p>
                    <h2>Why Book With Acute Tourism?</h2>
                    <span>Short proof points that matter when customers are deciding who to trust.</span>
                </div>
                <div class="home-why-acute-grid">
                    <article
                        v-for="card in whyAcuteCards"
                        :key="card.title"
                        class="home-why-acute-card"
                    >
                        <span class="home-why-acute-card__icon" :class="`home-why-acute-card__icon--${card.tone}`" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path :d="whyAcuteIconPath(card.icon)"></path>
                            </svg>
                        </span>
                        <div>
                            <h3>{{ card.title }}</h3>
                            <p>{{ card.copy }}</p>
                        </div>
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

        <section class="home-dashboard-section home-dashboard-section--light home-visa-section" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>Visa services &amp; international travel</h2>
                    </div>
                </div>
                <div class="home-visa-disclaimer">
                    <strong>
                        <span aria-hidden="true">▼</span>
                        Important visa note
                    </strong>
                    <p>
                        Acute Tourism provides visa application support and documentation guidance. Visa approval is at the sole discretion of the issuing embassy or consulate. Acute Tourism does not guarantee visa approval. Service fees are charged for assistance, not for visa issuance.
                    </p>
                </div>
                <div class="home-visa-process">
                    <article v-for="step in visaProcessSteps" :key="step.title" class="home-visa-step">
                        <span class="home-visa-step__icon" aria-hidden="true">
                            <svg v-if="step.icon === 'document'" viewBox="0 0 24 24">
                                <path d="M7 3h7l4 4v14H7z"></path>
                                <path d="M14 3v5h5"></path>
                            </svg>
                            <svg v-else-if="step.icon === 'check'" viewBox="0 0 24 24">
                                <path d="M9 11l2 2 4-4"></path>
                                <circle cx="12" cy="12" r="9"></circle>
                            </svg>
                            <svg v-else viewBox="0 0 24 24">
                                <path d="M5 12h14"></path>
                                <path d="m13 6 6 6-6 6"></path>
                            </svg>
                        </span>
                        <span>
                            <strong>{{ step.title }}</strong>
                            <small>{{ step.copy }}</small>
                        </span>
                    </article>
                </div>
                <div class="home-dashboard-grid home-dashboard-grid--four">
                    <article
                        v-for="item in visaCards"
                        :key="item.title"
                        class="home-visa-card"
                    >
                        <div class="home-visa-card__top">
                            <span class="home-visa-card__flag" aria-hidden="true">
                                <img
                                    v-if="visaFlag(item)"
                                    :src="`https://flagcdn.com/w80/${visaFlag(item)}.png`"
                                    :alt="`${item.title} flag`"
                                    loading="lazy"
                                    decoding="async"
                                />
                                <span v-else>Visa</span>
                            </span>
                            <p class="home-visa-card__tag">{{ item.tag }}</p>
                        </div>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.summary }}</p>
                        <Link class="home-dashboard-card__button" :href="item.href">Check Requirements</Link>
                    </article>
                </div>
            </div>
        </section>
    </section>
</template>

