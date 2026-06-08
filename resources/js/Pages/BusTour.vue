<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
});

const page = usePage();

const routes = [
    {
        title: 'Dubai Panoramic Bus Tour + Food Tasting',
        day: 'Dubai route',
        price: 'Request availability',
        image: 'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1200&q=85',
        copy: 'Hotel pick-up, Museum of the Future photo stop, Al Seef, DIFC, food tasting, lunch, and a sundowner at The Palm.',
        tags: ['Food tasting', 'Lunch', 'Hotel pickup'],
    },
    {
        title: 'Al Ain Panoramic Bus Tour + Al Ain Zoo',
        day: 'Al Ain route',
        price: 'Family friendly',
        image: 'https://images.unsplash.com/photo-1551969014-7d2c4cddf0b6?auto=format&fit=crop&w=1200&q=85',
        copy: 'A comfortable wildlife and heritage day with Al Ain Zoo admission, Jebel Hafeet, Al Jahili Fort, lunch, and guide.',
        tags: ['Zoo entry', 'Guide', 'Lunch'],
    },
    {
        title: 'Fujairah Panoramic Bus Tour',
        day: 'Coastal route',
        price: 'Subject to conditions',
        image: 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=85',
        copy: 'Friday Market, Al Hayl Castle, Khorfakkan scenery, oyster farm visit, beach access, and marine-focused experiences.',
        tags: ['Beach access', 'Oyster farm', 'Heritage stops'],
    },
    {
        title: 'Abu Dhabi Panoramic Bus Tour + Ferrari World',
        day: 'Abu Dhabi route',
        price: 'Ferrari World included',
        image: 'https://images.unsplash.com/photo-1512632578888-169bbbc64f33?auto=format&fit=crop&w=1200&q=85',
        copy: 'A focused Abu Dhabi day combining Sheikh Zayed Grand Mosque, Ferrari World Theme Park admission, lunch, guide, and refreshments.',
        tags: ['Ferrari World', 'Grand Mosque', 'Refreshments'],
    },
];

const inclusions = [
    ['Hotel pickup', 'Start and end from your hotel, making the day easier for visitors, families, and hosted guests.'],
    ['Professional guide', 'Enjoy guided sightseeing, landmark context, and assistance throughout the journey.'],
    ['Food and drinks', 'Each tour includes food, drinks, and route-specific value such as Al Ain Zoo or Ferrari World where applicable.'],
    ['Private groups', 'Reserve the bus for family outings, birthdays, corporate groups, travel agencies, or special occasions.'],
];

const audiences = [
    ['Premium travellers', 'For tourists who want a comfortable UAE day tour with hotel pick-up, guide support, and route planning handled.'],
    ['Residents hosting guests', 'For UAE residents who want to impress family or friends with a polished, easy-to-book day out.'],
    ['Families and small groups', 'For guests who prefer a simpler alternative to arranging cars, tickets, lunch, and timings separately.'],
    ['Private occasions', 'For birthdays, corporate outings, school groups, social clubs, and travel agencies.'],
];

const faqs = [
    ['Are seats limited?', 'Yes. Scheduled departures are intentionally limited to create a more comfortable hosted experience.'],
    ['Is hotel pick-up included?', 'Hotel pick-up and drop-off are included. Exact timing is confirmed after availability is checked.'],
    ['Can I request a private bus?', 'Yes. The bus can be requested for private groups, families, corporate teams, school groups, and travel agencies.'],
    ['Are attractions included?', 'Al Ain Zoo and Ferrari World admission are included in their respective tours. Operating hours and entry rules may apply.'],
];

const form = useForm({
    source: 'bus-tour-page',
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 2,
    interest: 'Bus Tour',
    route: routes[0].title,
    message: '',
});

function submit() {
    const message = [
        `Preferred route: ${form.route}`,
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
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="bus-tour-page">
        <section class="bus-tour-hero">
            <div class="container bus-tour-hero__inner">
                <p class="bus-tour-eyebrow">Premium panoramic UAE experiences</p>
                <h1>Luxury Bus Tour Dubai for Premium UAE Day Experiences</h1>
                <p>
                    Travel through Dubai, Al Ain, Fujairah or Abu Dhabi in a more comfortable and curated way with hotel pick-up,
                    professional guide support, selected attractions, lunch, refreshments, and private group options.
                </p>
                <div class="bus-tour-actions">
                    <a class="bus-tour-btn bus-tour-btn--gold" href="#bus-tour-request">Request availability</a>
                    <a class="bus-tour-btn bus-tour-btn--light" href="#bus-tour-routes">View routes</a>
                </div>
                <div class="bus-tour-facts">
                    <article><strong>12 guests</strong><span>Limited scheduled seats</span></article>
                    <article><strong>4 routes</strong><span>Dubai, Al Ain, Fujairah, Abu Dhabi</span></article>
                    <article><strong>Pickup</strong><span>Hotel pick-up and drop-off</span></article>
                    <article><strong>Private</strong><span>Custom groups available</span></article>
                </div>
            </div>
        </section>

        <section class="bus-tour-section">
            <div class="container bus-tour-split">
                <div class="bus-tour-panel">
                    <p class="bus-tour-eyebrow">Hosted day experience</p>
                    <h2>A hosted UAE day experience with comfort, access and a premium pace</h2>
                    <p>
                        The experience is built for guests who want the day arranged properly: comfortable panoramic bus,
                        professional guide, selected route highlights, food or tasting value, refreshments, and return drop-off.
                    </p>
                    <div class="bus-tour-mini-grid">
                        <span>Limited seats</span>
                        <span>Hotel pickup</span>
                        <span>Guide support</span>
                    </div>
                </div>
                <div class="bus-tour-image bus-tour-image--coach" aria-hidden="true"></div>
            </div>
        </section>

        <section id="bus-tour-routes" class="bus-tour-section bus-tour-section--cream">
            <div class="container">
                <div class="bus-tour-heading">
                    <p class="bus-tour-eyebrow">Choose your panoramic bus tour</p>
                    <h2>Four curated routes from Dubai</h2>
                    <p>
                        Each route includes hotel pick-up and drop-off, guide support, lunch or tasting value,
                        water and soft drinks, with route-specific attraction access where applicable.
                    </p>
                </div>
                <div class="bus-tour-grid">
                    <article v-for="route in routes" :key="route.title" class="bus-tour-card" :style="{ '--bus-card-image': `url(${route.image})` }">
                        <div class="bus-tour-card__body">
                            <span>{{ route.day }}</span>
                            <h3>{{ route.title }}</h3>
                            <strong>{{ route.price }}</strong>
                            <p>{{ route.copy }}</p>
                            <div>
                                <em v-for="tag in route.tags" :key="tag">{{ tag }}</em>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="bus-tour-section bus-tour-section--blue">
            <div class="container">
                <div class="bus-tour-heading">
                    <p class="bus-tour-eyebrow">What each tour includes</p>
                    <h2>Comfort, guidance, and route value in one plan</h2>
                    <p>Each route is structured around a clear experience theme, arranged transport, guided sightseeing, and included food or attraction value.</p>
                </div>
                <div class="bus-tour-inclusions">
                    <article v-for="[title, copy] in inclusions" :key="title">
                        <span></span>
                        <h3>{{ title }}</h3>
                        <p>{{ copy }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="bus-tour-section">
            <div class="container bus-tour-split bus-tour-split--reverse">
                <div class="bus-tour-image bus-tour-image--private" aria-hidden="true"></div>
                <div class="bus-tour-panel">
                    <p class="bus-tour-eyebrow">Private bus requests</p>
                    <h2>Reserve the bus for your own group</h2>
                    <p>
                        The panoramic bus can be requested for private groups, families, friends, corporate teams,
                        celebrations, school groups, travel agencies and custom UAE experiences.
                    </p>
                    <a class="bus-tour-btn bus-tour-btn--blue" href="#bus-tour-request">Request private options</a>
                </div>
            </div>
        </section>

        <section class="bus-tour-section bus-tour-section--cream">
            <div class="container">
                <div class="bus-tour-heading">
                    <p class="bus-tour-eyebrow">Designed for ease</p>
                    <h2>For guests who value ease and exclusivity</h2>
                </div>
                <div class="bus-tour-audience">
                    <article v-for="[title, copy] in audiences" :key="title">
                        <strong>{{ title }}</strong>
                        <span>{{ copy }}</span>
                    </article>
                </div>
            </div>
        </section>

        <section id="bus-tour-request" class="bus-tour-section bus-tour-lead">
            <div class="container bus-tour-lead__grid">
                <div>
                    <p class="bus-tour-eyebrow">Request your panoramic bus experience</p>
                    <h2>Share your preferred route, date and group details.</h2>
                    <p>
                        The Acute Tourism team will confirm availability, hotel pick-up timing, route flow,
                        attraction access and private bus options before payment.
                    </p>
                </div>
                <div class="bus-tour-form-card">
                    <div v-if="page.props.flash.success" class="success-banner">{{ page.props.flash.success }}</div>
                    <form class="bus-tour-form" @submit.prevent="submit">
                        <label><span>Name</span><input v-model="form.name" type="text" autocomplete="name" /></label>
                        <label><span>Email</span><input v-model="form.email" type="email" autocomplete="email" /></label>
                        <label><span>Phone / WhatsApp</span><input v-model="form.phone" type="tel" autocomplete="tel" /></label>
                        <label><span>Preferred route</span><select v-model="form.route"><option v-for="route in routes" :key="route.title" :value="route.title">{{ route.title }}</option></select></label>
                        <label><span>Travel date</span><input v-model="form.travel_date" type="date" /></label>
                        <label><span>Guests</span><input v-model="form.guest_count" type="number" min="1" max="100" /></label>
                        <label class="bus-tour-form__full"><span>Request</span><textarea v-model="form.message" rows="5" placeholder="Tell us your route, group type, pickup area, or private bus request."></textarea></label>
                        <button class="bus-tour-btn bus-tour-btn--gold bus-tour-form__full" type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Sending...' : 'Request availability' }}
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section class="bus-tour-section bus-tour-section--faq">
            <div class="container">
                <div class="bus-tour-heading">
                    <p class="bus-tour-eyebrow">Frequently asked questions</p>
                    <h2>Clear answers for confident booking</h2>
                </div>
                <div class="bus-tour-faq">
                    <details v-for="[question, answer] in faqs" :key="question">
                        <summary>{{ question }}</summary>
                        <p>{{ answer }}</p>
                    </details>
                </div>
            </div>
        </section>
    </div>
</template>
