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
    recommendations: Array,
    serviceFocus: Array,
    testimonials: Array,
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
const collectionCards = computed(() => props.collections.slice(0, 4));
const momentCards = computed(() => props.heroGallery.slice(0, 6));
const collectionPreviewImages = [
    props.heroGallery[0]?.image,
    props.heroGallery[1]?.image,
    props.heroGallery[3]?.image,
    props.heroGallery[4]?.image,
].filter(Boolean);

function collectionImage(collection, index) {
    return collection.heroImageUrl || collectionPreviewImages[index % collectionPreviewImages.length] || props.hero.heroImageUrl;
}

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
                    v-if="hero.videoUrl"
                    ref="videoRef"
                    class="home-hero-video__el"
                    :src="hero.videoUrl"
                    :poster="hero.heroImageUrl || undefined"
                    autoplay
                    muted
                    loop
                    playsinline
                ></video>
                <img
                    v-else
                    class="home-hero-video__el home-hero-video__el--img"
                    :src="hero.heroImageUrl"
                    :alt="hero.title"
                />
            </div>
            <div class="home-hero-video__scrim" aria-hidden="true"></div>
            <div class="home-hero-video__content home-dashboard-hero__content home-hero-motion">
                <p class="home-hero-video__eyebrow">{{ hero.eyebrow }}</p>
                <h1 class="home-hero-video__title">{{ hero.title }}</h1>
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
                        <strong>2.5k+</strong>
                        <span>Dubai</span>
                    </div>
                </div>
                <div class="home-hero-video__actions">
                    <Link class="home-hero-video__cta home-hero-video__cta--solid" :href="hero.primaryCta.href">
                        {{ hero.primaryCta.label }}
                    </Link>
                    <Link class="home-hero-video__cta home-hero-video__cta--ghost" :href="hero.secondaryCta.href">
                        {{ hero.secondaryCta.label }}
                    </Link>
                    <Link
                        v-if="hero.tertiaryCta"
                        class="home-hero-video__cta home-hero-video__cta--ghost"
                        :href="hero.tertiaryCta.href"
                    >
                        {{ hero.tertiaryCta.label }}
                    </Link>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>Dubai's <em>Must-Do</em> Experiences</h2>
                    </div>
                    <Link class="home-dashboard-more" href="/experiences">Explore More</Link>
                </div>
                <div class="home-dashboard-grid home-dashboard-grid--four home-dashboard-grid--featured">
                    <article
                        v-for="experience in mustDoCards"
                        :key="experience.slug"
                        class="home-dashboard-card"
                    >
                        <Link class="home-dashboard-card__media" :href="`/experiences/${experience.slug}`">
                            <img v-if="experience.heroImageUrl" :src="experience.heroImageUrl" :alt="experience.title" />
                            <span class="home-dashboard-badge">{{ experience.tag || experience.category }}</span>
                        </Link>
                        <div class="home-dashboard-card__body">
                            <h3>{{ experience.title }}</h3>
                            <p>{{ experience.summary }}</p>
                            <div class="home-dashboard-card__meta">
                                <span>{{ experience.location || experience.duration }}</span>
                                <strong>{{ experience.priceFrom }}</strong>
                            </div>
                            <Link class="home-dashboard-card__button" :href="`/experiences/${experience.slug}`">Book Now</Link>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>Top-Rated Dubai Experiences</h2>
                    </div>
                    <Link class="home-dashboard-more" href="/experiences">Explore More</Link>
                </div>
                <div class="home-dashboard-grid home-dashboard-grid--four home-dashboard-grid--featured">
                    <article
                        v-for="experience in topRatedCards"
                        :key="experience.slug"
                        class="home-dashboard-card"
                    >
                        <Link class="home-dashboard-card__media" :href="`/experiences/${experience.slug}`">
                            <img v-if="experience.heroImageUrl" :src="experience.heroImageUrl" :alt="experience.title" />
                            <span class="home-dashboard-badge">{{ experience.tag || experience.category }}</span>
                        </Link>
                        <div class="home-dashboard-card__body">
                            <h3>{{ experience.title }}</h3>
                            <p>{{ experience.summary }}</p>
                            <div class="home-dashboard-card__meta">
                                <span>{{ experience.location || experience.duration }}</span>
                                <strong>{{ experience.priceFrom }}</strong>
                            </div>
                            <Link class="home-dashboard-card__button" :href="`/experiences/${experience.slug}`">Book Now</Link>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--light" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading">
                    <div>
                        <h2>Explore Other <em>Emirates</em></h2>
                    </div>
                    <Link class="home-dashboard-more" href="/experiences">Explore More</Link>
                </div>
                <div class="home-dashboard-grid home-dashboard-grid--four">
                    <article
                        v-for="(collection, index) in collectionCards"
                        :key="collection.slug"
                        class="home-dashboard-card home-dashboard-card--compact"
                    >
                        <Link class="home-dashboard-card__media" :href="collection.href || `/collections/${collection.slug}`">
                            <img :src="collectionImage(collection, index)" :alt="collection.name" />
                        </Link>
                        <div class="home-dashboard-card__body">
                            <h3>{{ collection.name }}</h3>
                            <p>{{ collection.summary }}</p>
                            <Link class="home-dashboard-card__button" :href="collection.href || `/collections/${collection.slug}`">Explore More</Link>
                        </div>
                    </article>
                </div>
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
                            <img :src="item.image" :alt="item.title" />
                        </Link>
                    </div>
                    <div class="home-dashboard-feature-video">
                        <img :src="momentCards[5]?.image || momentCards[0]?.image" :alt="momentCards[0]?.title || 'Travel moment'" />
                        <div class="home-dashboard-feature-video__overlay">
                            <strong>{{ momentCards[0]?.title }}</strong>
                            <span>{{ momentCards[0]?.tag }}</span>
                        </div>
                        <Link class="home-dashboard-feature-video__button" href="/experiences">See More</Link>
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
                    <Link class="home-dashboard-more" href="/packages">Explore More</Link>
                </div>
                <div class="home-dashboard-grid home-dashboard-grid--four">
                    <article v-for="item in packages" :key="item.slug" class="home-dashboard-card home-dashboard-card--package">
                        <Link class="home-dashboard-card__media" :href="`/packages/${item.slug}`">
                            <img v-if="item.heroImageUrl" :src="item.heroImageUrl" :alt="item.title" />
                        </Link>
                        <div class="home-dashboard-card__body">
                            <h3>{{ item.title }}</h3>
                            <p>{{ item.summary }}</p>
                            <div class="home-dashboard-card__meta">
                                <span>{{ item.duration }}<span v-if="item.location"> | {{ item.location }}</span></span>
                                <strong>{{ item.priceFrom }}</strong>
                            </div>
                            <Link class="home-dashboard-card__button home-dashboard-card__button--light" :href="`/packages/${item.slug}`">Learn More</Link>
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
                        <img v-if="momentCards[0]" :src="momentCards[0].image" :alt="momentCards[0].title" />
                        <div class="home-dashboard-company__feature-stat">
                            <strong>4000+</strong>
                            <span>Customer Reviews</span>
                        </div>
                    </div>
                    <div class="home-dashboard-company__side">
                        <div class="home-dashboard-company__mini">
                            <img v-if="momentCards[1]" :src="momentCards[1].image" :alt="momentCards[1].title" />
                            <div>
                                <strong>12</strong>
                                <span>years of experience</span>
                            </div>
                        </div>
                        <div class="home-dashboard-company__mini">
                            <img v-if="momentCards[2]" :src="momentCards[2].image" :alt="momentCards[2].title" />
                            <div>
                                <strong>40+</strong>
                                <span>partners and services</span>
                            </div>
                        </div>
                        <div class="home-dashboard-company__copy">
                            <h3>See What They Say <em>About Us</em></h3>
                            <p>{{ testimonials[0]?.quote }}</p>
                            <Link class="home-dashboard-card__button" href="/contact">Read More</Link>
                        </div>
                    </div>
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
                <div class="home-dashboard-grid home-dashboard-grid--three">
                    <article v-for="t in testimonials" :key="t.name" class="home-dashboard-review">
                        <p class="home-dashboard-review__stars">★★★★★</p>
                        <p class="home-dashboard-review__quote">“{{ t.quote }}”</p>
                        <div class="home-dashboard-review__author">
                            <strong>{{ t.name }}</strong>
                            <span>{{ t.tag }}</span>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="home-dashboard-section home-dashboard-section--royal" data-reveal>
            <div class="container home-dashboard-stack">
                <div class="home-dashboard-heading home-dashboard-heading--dark">
                    <div>
                        <h2>Visa services &amp; international travel</h2>
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
                        <Link class="home-dashboard-card__button" :href="item.href">View service</Link>
                    </article>
                </div>
            </div>
        </section>
    </section>
</template>
