<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    pageContent: Object,
});

const page = usePage();
const content = computed(() => props.pageContent || {});

const routes = [
    {
        key: 'dubai',
        title: 'Dubai Panoramic Bus + Food Tasting',
        day: 'Dubai route',
        price: 'AED 499 per person',
        panelPrice: 'AED 499 / person',
        label: 'City, food and sundowner',
        copy: 'A city-focused luxury bus tour with hotel pick-up, Museum of the Future photo stop, Al Seef, DIFC, food tasting, lunch, and a sundowner at The Palm.',
        bestFor: 'Best for visitors, residents hosting guests, couples and small groups who want a relaxed city route with selected Dubai highlights and food-focused moments.',
        tags: ['Food tasting', 'Lunch', 'Guide'],
        highlights: ['Hotel pick-up from selected Dubai areas', 'Museum of the Future photo stop', 'Al Seef photo stop', 'DIFC photo stop', 'Sundowner at The Palm'],
        included: ['Hotel pick-up and drop-off', 'Professional guide', 'Food tasting', 'Lunch', 'Water and soft drinks'],
    },
    {
        key: 'alain',
        title: 'Al Ain Panoramic Bus + Al Ain Zoo',
        day: 'Al Ain route',
        price: 'AED 499 per person',
        panelPrice: 'AED 499 / person',
        label: 'Wildlife and heritage',
        copy: 'A family-friendly journey with Al Ain Zoo admission, Jebel Hafeet, Al Jahili Fort, Hili Archaeological Park, lunch, and guide.',
        bestFor: 'Best for families, residents and guests who want a comfortable wildlife and heritage day outside Dubai with attraction access already included.',
        tags: ['Zoo ticket', 'Family-friendly', 'Lunch'],
        highlights: ['National Museum photo stop', 'Hili Archaeological Park photo stop', 'Jebel Hafeet', 'Al Jahili Fort', 'Al Ain Zoo visit'],
        included: ['Hotel pick-up and drop-off', 'Al Ain Zoo admission ticket', 'Professional guide', 'Lunch', 'Water and soft drinks'],
    },
    {
        key: 'fujairah',
        title: 'Fujairah Panoramic Bus',
        day: 'Fujairah route',
        price: 'AED 699 per person',
        panelPrice: 'AED 699 / person',
        label: 'Coastal and marine experience',
        copy: 'A coastal route with Friday Market, Al Hayl Castle, Khorfakkan Waterfall, oyster farm visit, beach access, and marine activities.',
        bestFor: 'Best for guests looking for the most distinctive route: a coastal journey with heritage stops, Khorfakkan scenery, lunch by the beach and marine-focused experiences.',
        tags: ['Oyster farm', 'Beach access', 'Marine experience'],
        highlights: ['Hotel pick-up from selected Dubai areas', 'Friday Market shopping and photo stop', 'Al Hayl Castle', 'Khorfakkan Waterfall', 'Return transfer to your hotel'],
        included: ['Hotel pick-up and drop-off', 'Lunch at Heart Beach, Khorfakkan', 'Professional guide', 'Swimming with turtles', 'Oyster farm visit', 'Free beach access'],
    },
    {
        key: 'abudhabi',
        title: 'Abu Dhabi Panoramic Bus + Ferrari World',
        day: 'Abu Dhabi route',
        price: 'AED 945 per person',
        panelPrice: 'AED 945 / person',
        label: 'Grand Mosque and Ferrari World',
        copy: 'A focused Abu Dhabi day including Sheikh Zayed Grand Mosque, Ferrari World Theme Park admission, lunch, guide, and refreshments.',
        bestFor: 'Best for guests who want a focused Abu Dhabi day combining one major cultural landmark with Ferrari World Theme Park access.',
        tags: ['Ferrari World', 'Mosque visit', 'Lunch'],
        highlights: ['Sheikh Zayed Grand Mosque', 'Ferrari World Theme Park'],
        included: ['Hotel pick-up and drop-off', 'Professional guide', 'Lunch', 'Water and soft drinks', 'Admission ticket to Ferrari World Theme Park'],
    },
];

const editableRoutes = computed(() => (
    content.value.routesSection?.items?.length ? content.value.routesSection.items : routes
));

const galleryFolders = [
    {
        key: 'panoramic',
        title: 'Luxury panoramic bus',
        copy: 'Exterior and route visuals from the experience.',
        cover: 'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=1300&q=85',
        images: [
            'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=900&q=85',
        ],
    },
    {
        key: 'bus',
        title: 'Bus exterior',
        copy: 'Luxury coach exterior and arrival setup.',
        cover: 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=900&q=85',
        images: [
            'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1300&q=85',
        ],
    },
    {
        key: 'interior',
        title: 'Bus interior',
        copy: 'Seating, comfort, space and onboard setup.',
        cover: 'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=900&q=85',
        images: [
            'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1527631746610-bca00a040d60?auto=format&fit=crop&w=1300&q=85',
        ],
    },
    {
        key: 'hosted',
        title: 'Hosted welcome',
        copy: 'Guest welcome, guide briefing and departure flow.',
        cover: 'https://images.unsplash.com/photo-1527631746610-bca00a040d60?auto=format&fit=crop&w=900&q=85',
        images: [
            'https://images.unsplash.com/photo-1527631746610-bca00a040d60?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1507537297725-24a1c029d3ca?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=1300&q=85',
        ],
    },
    {
        key: 'dubai',
        title: 'Dubai city route',
        copy: 'City views, photo stops and sundowner atmosphere.',
        cover: 'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1200&q=85',
        images: [
            'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1582672060674-bc2bd808a8b5?auto=format&fit=crop&w=1300&q=85',
        ],
    },
    {
        key: 'fujairah',
        title: 'Fujairah coastal route',
        copy: 'Coastal, beach and marine moments.',
        cover: 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=85',
        images: [
            'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=1300&q=85',
        ],
    },
    {
        key: 'alain',
        title: 'Al Ain family route',
        copy: 'Wildlife, heritage and mountain scenery.',
        cover: 'https://images.unsplash.com/photo-1551969014-7d2c4cddf0b6?auto=format&fit=crop&w=1200&q=85',
        images: [
            'https://images.unsplash.com/photo-1551969014-7d2c4cddf0b6?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1300&q=85',
        ],
    },
    {
        key: 'abudhabi',
        title: 'Abu Dhabi route',
        copy: 'Grand Mosque and Ferrari World highlights.',
        cover: 'https://images.unsplash.com/photo-1512632578888-169bbbc64f33?auto=format&fit=crop&w=1200&q=85',
        images: [
            'https://images.unsplash.com/photo-1512632578888-169bbbc64f33?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=1300&q=85',
        ],
    },
    {
        key: 'food',
        title: 'Lunch and tastings',
        copy: 'Food tasting, lunch and refreshment moments.',
        cover: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=85',
        images: [
            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1543353071-873f17a7a088?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1300&q=85',
        ],
    },
    {
        key: 'group',
        title: 'Private groups',
        copy: 'Family, corporate and group travel moments.',
        cover: 'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=900&q=85',
        images: [
            'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1507537297725-24a1c029d3ca?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1300&q=85',
        ],
    },
    {
        key: 'guest',
        title: 'Guest moments',
        copy: 'Hosted service, route highlights and guest moments.',
        cover: 'https://images.unsplash.com/photo-1507537297725-24a1c029d3ca?auto=format&fit=crop&w=900&q=85',
        images: [
            'https://images.unsplash.com/photo-1507537297725-24a1c029d3ca?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1527631746610-bca00a040d60?auto=format&fit=crop&w=1300&q=85',
            'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1300&q=85',
        ],
    },
];

const galleryTrackRef = ref(null);
const editableGalleryFolders = computed(() => (
    content.value.gallery?.items?.length ? content.value.gallery.items : galleryFolders
));
const busGalleryMedia = computed(() => editableGalleryFolders.value.flatMap((folder) => [
    ...(folder.images || []).map((url, index) => ({
        type: 'image',
        url,
        title: folder.title,
        copy: folder.copy,
        key: `${folder.key}-image-${index}`,
    })),
    ...(folder.videos || []).map((url, index) => ({
        type: 'video',
        url,
        title: folder.title,
        copy: folder.copy,
        key: `${folder.key}-video-${index}`,
    })),
]));
const activeMediaIndex = ref(null);
const showStickyCta = ref(false);
let stickyCtaTimer = null;
const activeMedia = computed(() => (
    activeMediaIndex.value === null ? null : busGalleryMedia.value[activeMediaIndex.value] ?? null
));

function galleryCoverStyle(url) {
    return {
        backgroundImage: `linear-gradient(to top, rgba(6, 26, 99, 0.82), transparent 58%), url('${url}')`,
    };
}

function imageBackgroundStyle(url) {
    if (!url) {
        return {};
    }

    return {
        backgroundImage: `linear-gradient(to top, rgba(6, 26, 99, 0.88), rgba(6, 26, 99, 0.18) 58%, rgba(6, 26, 99, 0.08)), url('${url}')`,
    };
}

function plainBackgroundStyle(url) {
    if (!url) {
        return {};
    }

    return {
        backgroundImage: `linear-gradient(to top, rgba(6, 26, 99, 0.46), transparent 48%), url('${url}')`,
    };
}

function choiceMediaStyle(url) {
    if (!url) {
        return {};
    }

    return {
        backgroundImage: `linear-gradient(135deg, rgba(6, 26, 99, 0.82), rgba(11, 44, 143, 0.46)), url('${url}')`,
    };
}

function openMedia(index) {
    activeMediaIndex.value = index;
}

function closeMedia() {
    activeMediaIndex.value = null;
}

function showMedia(index) {
    if (! busGalleryMedia.value.length) {
        return;
    }

    const total = busGalleryMedia.value.length;
    activeMediaIndex.value = ((index % total) + total) % total;
}

function scrollGallery(direction) {
    const track = galleryTrackRef.value;

    if (! track) {
        return;
    }

    track.scrollBy({
        left: direction * Math.max(280, track.clientWidth * 0.78),
        behavior: 'smooth',
    });
}

const audiences = [
    ['Premium travellers', 'For tourists who want a comfortable UAE day tour with hotel pick-up, guide support and route planning already handled.'],
    ['Residents hosting guests', 'For UAE residents who want to impress family or friends with a polished, easy-to-book experience.'],
    ['Families and small groups', 'For guests who prefer a more comfortable alternative to arranging cars, tickets, lunch and timings separately.'],
    ['Private occasions', 'For birthdays, corporate outings, school groups, social clubs and travel agencies that want a ready-made group journey.'],
];

const editableAudiences = computed(() => (
    content.value.audience?.items?.length
        ? content.value.audience.items
        : audiences.map(([title, copy]) => ({ title, copy }))
));

const faqs = [
    ['Are hotel pick-up and drop-off included?', 'Yes. Hotel pick-up and drop-off are included across the four panoramic bus tour options. Exact pick-up time and coverage will be confirmed by the Acute Tourism team after your enquiry.'],
    ['Are lunch and drinks included?', 'Yes. Lunch, water and soft drinks are included across the routes. The Dubai route also includes food tasting and a sundowner at The Palm.'],
    ['Can the bus be booked privately?', 'Yes. The panoramic bus can be requested for families, friends, corporate groups, school groups, travel agencies and special occasions. Private requests are recommended when you need more control over date, route and group arrangements.'],
    ['Are marine activities on the Fujairah tour guaranteed?', 'Marine activities such as turtle swimming, shark sessions, beach access and oyster farm visits are reconfirmed before booking because they may depend on supplier operations, weather, sea conditions and safety requirements.'],
    ['What should guests bring?', 'Comfortable clothing is recommended. For mosque visits, modest clothing is required. For Fujairah, guests may bring swimwear, a towel, sunscreen and sunglasses. A valid ID may be required for selected attractions.'],
    ['Is photography or videography available?', 'Yes. Professional photography and videography are optional add-ons and can be requested during enquiry.'],
    ['How do I confirm my booking?', 'Submit the enquiry form or contact the team on WhatsApp. Acute Tourism will confirm availability, hotel pick-up timing, final route flow, attraction access and payment details before your booking is confirmed.'],
    ['How do I choose the right route?', 'Choose Dubai for food and city highlights, Al Ain for family and wildlife, Fujairah for coastal and marine experiences, and Abu Dhabi for Grand Mosque and Ferrari World.'],
];

const editableFaqs = computed(() => (
    content.value.faq?.items?.length
        ? content.value.faq.items
        : faqs.map(([question, answer]) => ({ question, answer }))
));

const form = useForm({
    source: 'bus-tour-page',
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 2,
    interest: 'Panoramic Bus',
    route: '',
    message: '',
});

function submit() {
    const message = [
        `Preferred route: ${form.route || 'Not selected'}`,
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
            onSuccess: () => form.reset('name', 'email', 'phone', 'travel_date', 'guest_count', 'route', 'message'),
        });
}

function updateStickyCta() {
    const hero = document.querySelector('.acute-hero');
    const footer = document.querySelector('.site-footer');
    const heroBottom = hero?.getBoundingClientRect().bottom ?? 0;
    const footerTop = footer?.getBoundingClientRect().top ?? Number.POSITIVE_INFINITY;

    showStickyCta.value = heroBottom < 0 && footerTop > window.innerHeight - 120;
}

onMounted(() => {
    updateStickyCta();
    window.addEventListener('scroll', updateStickyCta, { passive: true });
    window.addEventListener('resize', updateStickyCta);
    stickyCtaTimer = window.setInterval(updateStickyCta, 250);
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateStickyCta);
    window.removeEventListener('resize', updateStickyCta);
    if (stickyCtaTimer) {
        window.clearInterval(stickyCtaTimer);
    }
});

</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <main class="acute-bus-page">
        <section id="top" class="acute-hero">
            <div class="acute-hero-inner">
                <div class="acute-eyebrow">{{ content.hero?.eyebrow || 'Panoramic Bus Dubai' }}</div>
                <h1>{{ content.hero?.title || 'Luxury Bus Tour Dubai' }}</h1>
                <p>
                    {{ content.hero?.description || "Travel through Dubai, Al Ain, Fujairah or Abu Dhabi in a more comfortable and curated way. Acute Tourism's luxury bus tour Dubai experience is designed for premium guests who prefer hotel pick-up, guided sightseeing, included meals or tastings, selected attractions and a smoother day out without the feel of a crowded group tour." }}
                </p>
                <div class="acute-hero-actions">
                    <a class="acute-btn gold" href="#tours">{{ content.hero?.primaryCtaLabel || 'View Packages' }}</a>
                    <a class="acute-btn light" href="#enquiry">{{ content.hero?.secondaryCtaLabel || 'Request Availability' }}</a>
                </div>
                <div class="acute-fomo-line"><span class="acute-fomo-dot"></span> {{ content.hero?.fomoLine || 'Limited seats per scheduled tour - Early enquiry recommended' }}</div>
                <div class="acute-hero-facts">
                    <div v-for="fact in content.hero?.facts || []" :key="`${fact.value}-${fact.label}`" class="acute-hero-fact">
                        <strong>{{ fact.value }}</strong><span>{{ fact.label }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="highlights" class="acute-section acute-highlights reference-inspired">
            <div class="acute-container">
                <div class="acute-refined-intro">
                    <div class="acute-intro-mark">
                        <template v-for="line in (content.intro?.mark || 'Come\non\nboard').split('\n')" :key="line">
                            {{ line }}<br />
                        </template>
                    </div>
                    <div>
                        <div class="acute-eyebrow">{{ content.intro?.eyebrow || 'Why guests choose it' }}</div>
                        <h2 class="acute-heading">{{ content.intro?.title || 'A hosted UAE day experience with comfort, access and a premium pace' }}</h2>
                        <p class="acute-copy">{{ content.intro?.copy || 'The experience is built for guests who want the day arranged properly: hotel pick-up, a comfortable panoramic bus, a professional guide, selected route highlights, lunch or tasting value, refreshments and return drop-off.' }}</p>
                    </div>
                </div>
                <div class="acute-choice-stage">
                    <div class="acute-choice-media" role="img" :aria-label="content.choice?.mediaLabel || 'Luxury panoramic bus tour media area'" :style="choiceMediaStyle(content.choice?.mediaImageUrl)">
                        <video
                            v-if="content.choice?.mediaVideoUrl"
                            class="acute-choice-video"
                            :src="content.choice.mediaVideoUrl"
                            :poster="content.choice?.mediaImageUrl || undefined"
                            controls
                            playsinline
                            preload="metadata"
                        ></video>
                        <div v-else class="acute-video-card">
                            <div class="acute-play-icon" aria-hidden="true"></div>
                            <strong>{{ content.choice?.mediaTitle || 'Watch the experience' }}</strong>
                            <span>{{ content.choice?.mediaCopy || 'Experience video area: bus interior, guest welcome, route moments and destination highlights.' }}</span>
                        </div>
                    </div>
                    <div class="acute-choice-panel">
                        <div v-for="line in content.choice?.lines || []" :key="line.number + line.title" class="acute-choice-line"><span>{{ line.number }}</span><div><strong>{{ line.title }}</strong><p>{{ line.copy }}</p></div></div>
                        <div class="acute-choice-actions">
                            <a class="acute-btn gold" href="#tours">{{ content.choice?.primaryCtaLabel || 'Compare Tours' }}</a>
                            <a class="acute-btn outline" href="#private">{{ content.choice?.secondaryCtaLabel || 'Private Group Enquiry' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tours" class="acute-section">
            <div class="acute-container">
                <div class="acute-section-head">
                    <div class="acute-eyebrow">{{ content.routesSection?.eyebrow || 'Packages and prices' }}</div>
                    <h2 class="acute-heading">{{ content.routesSection?.title || 'Choose your panoramic bus tour' }}</h2>
                    <p class="acute-copy">{{ content.routesSection?.copy || 'Four curated routes from Dubai, each with hotel pick-up and drop-off, a professional guide, lunch or tasting value, water and soft drinks.' }}</p>
                </div>
                <div class="acute-tours-grid">
                    <Link v-for="route in editableRoutes" :key="route.key" :href="route.href || '#details'" class="acute-tour-card" :class="route.key" :style="imageBackgroundStyle(route.cardImageUrl)">
                        <div class="acute-tour-body">
                            <span class="acute-tour-day">{{ route.day }}</span>
                            <h3>{{ route.title }}</h3>
                            <div class="acute-price">{{ route.price }}</div>
                            <p>{{ route.copy }}</p>
                            <div class="acute-tags"><span v-for="tag in route.tags" :key="tag">{{ tag }}</span></div>
                        </div>
                    </Link>
                </div>
                <div class="acute-availability-note"><strong>Availability note:</strong> {{ content.routesSection?.availabilityNote || 'Scheduled seats are limited and preferred dates may close once capacity is reached. For families, celebrations, corporate groups or travel agencies, private bus requests are recommended for better date control.' }}</div>
            </div>
        </section>

        <section id="details" class="acute-section alt">
            <div class="acute-container">
                <div class="acute-section-head">
                    <div class="acute-eyebrow">{{ content.details?.eyebrow || 'Tour details' }}</div>
                    <h2 class="acute-heading">{{ content.details?.title || 'What each tour includes' }}</h2>
                    <p class="acute-copy">{{ content.details?.copy || 'Each route is structured around a clear experience theme, arranged transport, guided sightseeing, and included food or attraction value.' }}</p>
                </div>
                <div class="acute-details-list">
                    <article v-for="route in editableRoutes" :key="`${route.key}-details`" class="acute-tour-panel">
                        <div class="acute-panel-image" :class="route.key" :style="imageBackgroundStyle(route.detailImageUrl || route.cardImageUrl)"></div>
                        <div class="acute-panel-content">
                            <div class="acute-panel-top">
                                <span class="acute-panel-label">{{ route.label }}</span>
                                <span class="acute-panel-price">{{ route.panelPrice }}</span>
                            </div>
                            <h3>{{ route.title }}</h3>
                            <p>{{ route.bestFor }}</p>
                            <div class="acute-info-cols">
                                <div class="acute-list">
                                    <h4>Route highlights</h4>
                                    <ul><li v-for="item in route.highlights" :key="item">{{ item }}</li></ul>
                                </div>
                                <div class="acute-list">
                                    <h4>Included</h4>
                                    <ul><li v-for="item in route.included" :key="item">{{ item }}</li></ul>
                                </div>
                            </div>
                            <Link v-if="route.href" class="acute-btn gold" :href="route.href">View Details</Link>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="know-before" class="acute-section">
            <div class="acute-container">
                <div class="acute-section-head"><div class="acute-eyebrow">{{ content.beforeBooking?.eyebrow || 'Before you book' }}</div><h2 class="acute-heading">{{ content.beforeBooking?.title || 'Clear answers for confident booking' }}</h2><p class="acute-copy">{{ content.beforeBooking?.copy || 'Essential details to help you understand what is included, what may depend on availability and how your booking is confirmed.' }}</p></div>
                <div class="acute-safety-grid">
                    <div v-for="card in content.beforeBooking?.cards || []" :key="card.title" class="acute-safety-card"><h3>{{ card.title }}</h3><p>{{ card.copy }}</p></div>
                </div>
            </div>
        </section>

        <section id="private" class="acute-section acute-private">
            <div class="acute-container acute-private-grid">
                <div>
                    <div class="acute-eyebrow">{{ content.privateSection?.eyebrow || 'Private bus enquiry' }}</div>
                    <h2 class="acute-heading">{{ content.privateSection?.title || 'Reserve the bus for your own group' }}</h2>
                    <p class="acute-copy">{{ content.privateSection?.copy || 'The panoramic bus can also be requested for private groups, families, friends, corporate teams, celebrations, school groups, travel agencies and custom UAE experiences.' }}</p>
                    <div class="acute-line-list"><div v-for="line in content.privateSection?.lines || []" :key="line.label"><span>{{ line.label }}</span><strong>{{ line.value }}</strong></div></div>
                    <a class="acute-btn gold" href="#enquiry">{{ content.privateSection?.ctaLabel || 'Request Private Bus' }}</a>
                </div>
                <div class="acute-private-image" :style="plainBackgroundStyle(content.privateSection?.imageUrl)"></div>
            </div>
        </section>

        <section class="acute-section alt acute-audience">
            <div class="acute-container">
                <div class="acute-section-head">
                    <div class="acute-eyebrow">{{ content.audience?.eyebrow || 'Best for' }}</div>
                    <h2 class="acute-heading">{{ content.audience?.title || 'Designed for guests who value ease and exclusivity' }}</h2>
                    <p class="acute-copy">{{ content.audience?.copy || 'The panoramic bus format is especially useful when you want a premium day out arranged for you.' }}</p>
                </div>
                <div class="acute-audience-grid">
                    <div v-for="item in editableAudiences" :key="item.title" class="acute-audience-card"><h3>{{ item.title }}</h3><p>{{ item.copy }}</p></div>
                </div>
            </div>
        </section>

        <section id="media-gallery" class="acute-section acute-media-showcase">
            <div class="acute-container">
                <div class="acute-section-head">
                    <div class="acute-eyebrow">{{ content.gallery?.eyebrow || 'Experience media' }}</div>
                    <h2 class="acute-heading">{{ content.gallery?.title || 'A closer look at the journey' }}</h2>
                    <p class="acute-copy">{{ content.gallery?.copy || 'See the bus, onboard comfort, hosted moments and route highlights before you request availability.' }}</p>
                </div>
                <div class="acute-gallery-carousel" aria-label="Bus tour media carousel">
                    <div class="acute-gallery-carousel__controls">
                        <button
                            type="button"
                            class="acute-gallery-carousel__arrow"
                            aria-label="Scroll gallery left"
                            @click="scrollGallery(-1)"
                        >
                            ‹
                        </button>
                        <button
                            type="button"
                            class="acute-gallery-carousel__arrow"
                            aria-label="Scroll gallery right"
                            @click="scrollGallery(1)"
                        >
                            ›
                        </button>
                    </div>
                    <div ref="galleryTrackRef" class="acute-gallery-carousel__track">
                        <button
                            v-for="(item, index) in busGalleryMedia"
                            :key="item.key"
                            type="button"
                            class="acute-gallery-carousel__item"
                            :aria-label="`Open ${item.title} media ${index + 1}`"
                            @click="openMedia(index)"
                        >
                            <video
                                v-if="item.type === 'video'"
                                :src="item.url"
                                muted
                                playsinline
                                preload="metadata"
                            ></video>
                            <img
                                v-else
                                :src="item.url"
                                :alt="item.title"
                                loading="lazy"
                                decoding="async"
                            />
                            <span v-if="item.type === 'video'" class="acute-gallery-carousel__play" aria-hidden="true"></span>
                            <span class="acute-gallery-carousel__caption">
                                <strong>{{ item.title }}</strong>
                                <em>{{ index + 1 }} / {{ busGalleryMedia.length }}</em>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="acute-proof-note">{{ content.gallery?.note || 'Browse the bus setup, seating, destination highlights and onboard hospitality before requesting availability.' }}</div>
            </div>
        </section>

        <section id="enquiry" class="acute-section acute-lead">
            <div class="acute-container acute-lead-grid">
                <div class="acute-lead-copy"><div class="acute-eyebrow">{{ content.enquiry?.eyebrow || 'Check availability' }}</div><h2 class="acute-heading">{{ content.enquiry?.title || 'Request your panoramic bus experience' }}</h2><p class="acute-copy">{{ content.enquiry?.copy || 'Share your preferred route, date and group details. The Acute Tourism team will confirm availability, hotel pick-up timing, route flow, attraction access and private bus options before payment.' }}</p></div>
                <form class="acute-lead-form" @submit.prevent="submit">
                    <div v-if="page.props.flash.success" class="success-banner acute-full-field">{{ page.props.flash.success }}</div>
                    <div class="acute-form-grid">
                        <label>Full Name<input v-model="form.name" placeholder="Enter your name" required type="text" autocomplete="name" /></label>
                        <label>WhatsApp Number<input v-model="form.phone" placeholder="+971 5X XXX XXXX" required type="tel" autocomplete="tel" /></label>
                        <label>Preferred Tour<select v-model="form.route" required><option value="">Select tour</option><option v-for="route in editableRoutes" :key="route.title" :value="route.title">{{ route.title }}</option><option value="Private Bus Enquiry">Private Bus Enquiry</option></select></label>
                        <label>Preferred Date<input v-model="form.travel_date" type="date" /></label>
                    </div>
                    <label class="acute-full-field">Additional Request<textarea v-model="form.message" placeholder="Number of guests, hotel location, preferred route, private group request, or any special occasion." rows="4"></textarea></label>
                    <button class="acute-btn gold acute-submit" type="submit" :disabled="form.processing">{{ form.processing ? (content.enquiry?.processingLabel || 'Sending...') : (content.enquiry?.submitLabel || 'Send Enquiry') }}</button>
                    <a class="acute-quick-chat" :href="content.enquiry?.whatsappUrl || 'https://wa.me/971521926984?text=Hello%20Acute%20Tourism%2C%20I%20want%20to%20check%20availability%20for%20the%20panoramic%20bus%20tour.'" rel="noopener" target="_blank">{{ content.enquiry?.whatsappLabel || 'Prefer WhatsApp? Speak to our team directly' }}</a>
                    <div class="acute-note">{{ content.enquiry?.note || 'Submitting this form does not confirm booking. The team will confirm seat availability, hotel pick-up details, final timing and any supplier-dependent activities before payment.' }}</div>
                </form>
            </div>
        </section>

        <section class="acute-section cream">
            <div class="acute-container">
                <div class="acute-section-head"><div class="acute-eyebrow">{{ content.faq?.eyebrow || 'Questions guests may ask' }}</div><h2 class="acute-heading">{{ content.faq?.title || 'Frequently asked questions' }}</h2></div>
                <div class="acute-faq-accordion">
                    <details v-for="item in editableFaqs" :key="item.question" class="acute-faq-item"><summary>{{ item.question }}</summary><div class="acute-faq-answer">{{ item.answer }}</div></details>
                </div>
            </div>
        </section>

        <div v-if="showStickyCta" class="acute-sticky-cta" aria-label="Request panoramic bus tour availability">
            <span><strong>{{ content.sticky?.label || 'Limited seats' }}</strong> {{ content.sticky?.text || 'on scheduled departures' }}</span>
            <a class="acute-btn gold" href="#enquiry">{{ content.sticky?.ctaLabel || 'Request Availability' }}</a>
        </div>

        <div v-if="activeMedia" class="experience-lightbox acute-gallery-lightbox" @click.self="closeMedia">
            <button type="button" class="experience-lightbox__close" @click="closeMedia">Close</button>
            <div class="experience-lightbox__dialog acute-gallery-lightbox__dialog">
                <div class="acute-gallery-lightbox__media-wrap">
                    <button type="button" class="acute-gallery-lightbox__nav prev" aria-label="Previous media" @click="showMedia(activeMediaIndex - 1)">‹</button>
                    <video
                        v-if="activeMedia.type === 'video'"
                        class="experience-lightbox__media"
                        :src="activeMedia.url"
                        controls
                        autoplay
                        playsinline
                    ></video>
                    <img
                        v-else
                        class="experience-lightbox__media"
                        :src="activeMedia.url"
                        :alt="`${activeMedia.title} media ${activeMediaIndex + 1}`"
                    />
                    <button type="button" class="acute-gallery-lightbox__nav next" aria-label="Next media" @click="showMedia(activeMediaIndex + 1)">›</button>
                </div>
                <div class="acute-gallery-lightbox__content">
                    <div>
                        <span>{{ activeMediaIndex + 1 }} / {{ busGalleryMedia.length }}</span>
                        <h3>{{ activeMedia.title }}</h3>
                        <p>{{ activeMedia.copy }}</p>
                    </div>
                    <div class="acute-gallery-lightbox__thumbs" aria-label="Gallery media">
                        <button
                            v-for="(item, index) in busGalleryMedia"
                            :key="item.key"
                            type="button"
                            :class="{ active: index === activeMediaIndex }"
                            :aria-label="`Show media ${index + 1}`"
                            @click="showMedia(index)"
                        >
                            <video v-if="item.type === 'video'" :src="item.url" muted playsinline preload="metadata"></video>
                            <img v-else :src="item.url" :alt="`${item.title} thumbnail ${index + 1}`" loading="lazy" decoding="async" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
