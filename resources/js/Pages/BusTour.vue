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
        key: 'dubai',
        title: 'Dubai Panoramic Bus Tour + Food Tasting',
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
        title: 'Al Ain Panoramic Bus Tour + Al Ain Zoo',
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
        title: 'Fujairah Panoramic Bus Tour',
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
        title: 'Abu Dhabi Panoramic Bus Tour + Ferrari World',
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

const galleryItems = [
    ['bus', 'Bus exterior', 'Luxury coach exterior and arrival setup.'],
    ['interior', 'Bus interior', 'Seating, comfort, space and onboard setup.'],
    ['hosted', 'Hosted welcome', 'Guest welcome, guide briefing and departure flow.'],
    ['dubai', 'Dubai city route', 'City views, photo stops and sundowner atmosphere.'],
    ['fujairah', 'Fujairah coastal route', 'Coastal, beach and marine moments.'],
    ['alain', 'Al Ain family route', 'Wildlife, heritage and mountain scenery.'],
    ['abudhabi', 'Abu Dhabi route', 'Grand Mosque and Ferrari World highlights.'],
    ['food', 'Lunch and tastings', 'Food tasting, lunch and refreshment moments.'],
    ['group', 'Private groups', 'Family, corporate and group travel moments.'],
    ['guest', 'Guest moments', 'Hosted service, route highlights and guest moments.'],
];

const audiences = [
    ['Premium travellers', 'For tourists who want a comfortable UAE day tour with hotel pick-up, guide support and route planning already handled.'],
    ['Residents hosting guests', 'For UAE residents who want to impress family or friends with a polished, easy-to-book experience.'],
    ['Families and small groups', 'For guests who prefer a more comfortable alternative to arranging cars, tickets, lunch and timings separately.'],
    ['Private occasions', 'For birthdays, corporate outings, school groups, social clubs and travel agencies that want a ready-made group journey.'],
];

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

const form = useForm({
    source: 'bus-tour-page',
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 2,
    interest: 'Bus Tour',
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

</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <main class="acute-bus-page">
        <section id="top" class="acute-hero">
            <div class="acute-hero-inner">
                <div class="acute-eyebrow">Luxury Bus Tour Dubai</div>
                <h1>Luxury Bus Tour Dubai for Premium UAE Day Experiences</h1>
                <p>
                    Travel through Dubai, Al Ain, Fujairah or Abu Dhabi in a more comfortable and curated way. Acute Tourism's luxury bus tour Dubai experience is designed for premium guests who prefer hotel pick-up, guided sightseeing, included meals or tastings, selected attractions and a smoother day out without the feel of a crowded group tour.
                </p>
                <div class="acute-hero-actions">
                    <a class="acute-btn gold" href="#tours">View Packages</a>
                    <a class="acute-btn light" href="#enquiry">Request Availability</a>
                </div>
                <div class="acute-fomo-line"><span class="acute-fomo-dot"></span> Limited seats per scheduled tour - Early enquiry recommended</div>
                <div class="acute-hero-facts">
                    <div class="acute-hero-fact"><strong>12 Guests Only</strong><span>Intentionally limited per departure</span></div>
                    <div class="acute-hero-fact"><strong>18 Seats</strong><span>More room on board</span></div>
                    <div class="acute-hero-fact"><strong>Hotel Pick-up</strong><span>Included with every route</span></div>
                    <div class="acute-hero-fact"><strong>High-demand Dates</strong><span>Availability confirmed on request</span></div>
                </div>
            </div>
        </section>

        <section id="highlights" class="acute-section acute-highlights reference-inspired">
            <div class="acute-container">
                <div class="acute-refined-intro">
                    <div class="acute-intro-mark">Come<br />on<br />board</div>
                    <div>
                        <div class="acute-eyebrow">Why guests choose it</div>
                        <h2 class="acute-heading">A hosted UAE day experience with comfort, access and a premium pace</h2>
                        <p class="acute-copy">The experience is built for guests who want the day arranged properly: hotel pick-up, a comfortable panoramic bus, a professional guide, selected route highlights, lunch or tasting value, refreshments and return drop-off.</p>
                    </div>
                </div>
                <div class="acute-choice-stage">
                    <div class="acute-choice-media" role="img" aria-label="Luxury panoramic bus tour media area">
                        <div class="acute-video-card">
                            <div class="acute-play-icon" aria-hidden="true"></div>
                            <strong>Watch the experience</strong>
                            <span>Experience video area: bus interior, guest welcome, route moments and destination highlights.</span>
                        </div>
                    </div>
                    <div class="acute-choice-panel">
                        <div class="acute-choice-line"><span>01</span><div><strong>Limited scheduled capacity</strong><p>Each scheduled tour is intentionally limited to 12 guests, creating a more exclusive, spacious and personal experience.</p></div></div>
                        <div class="acute-choice-line"><span>02</span><div><strong>Hotel pick-up and return</strong><p>Start and end from your hotel, making the tour easier for tourists, families and visiting guests.</p></div></div>
                        <div class="acute-choice-line"><span>03</span><div><strong>Professional guided route</strong><p>Enjoy guided sightseeing, landmark context and assistance throughout the journey.</p></div></div>
                        <div class="acute-choice-line"><span>04</span><div><strong>Lunch, tastings or attraction value</strong><p>Each tour includes food, drinks and route-specific value such as Al Ain Zoo or Ferrari World where applicable.</p></div></div>
                        <div class="acute-choice-line"><span>05</span><div><strong>Private group option</strong><p>Reserve the bus for family outings, birthdays, corporate groups, travel agencies or special occasions.</p></div></div>
                        <div class="acute-choice-actions">
                            <a class="acute-btn gold" href="#tours">Compare Tours</a>
                            <a class="acute-btn outline" href="#private">Private Group Enquiry</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tours" class="acute-section">
            <div class="acute-container">
                <div class="acute-section-head">
                    <div class="acute-eyebrow">Packages and prices</div>
                    <h2 class="acute-heading">Choose your panoramic bus tour</h2>
                    <p class="acute-copy">Four curated routes from Dubai, each with hotel pick-up and drop-off, a professional guide, lunch or tasting value, water and soft drinks.</p>
                </div>
                <div class="acute-tours-grid">
                    <article v-for="route in routes" :key="route.key" class="acute-tour-card" :class="route.key">
                        <div class="acute-tour-body">
                            <span class="acute-tour-day">{{ route.day }}</span>
                            <h3>{{ route.title }}</h3>
                            <div class="acute-price">{{ route.price }}</div>
                            <p>{{ route.copy }}</p>
                            <div class="acute-tags"><span v-for="tag in route.tags" :key="tag">{{ tag }}</span></div>
                        </div>
                    </article>
                </div>
                <div class="acute-availability-note"><strong>Availability note:</strong> Scheduled seats are limited and preferred dates may close once capacity is reached. For families, celebrations, corporate groups or travel agencies, private bus requests are recommended for better date control.</div>
            </div>
        </section>

        <section id="details" class="acute-section alt">
            <div class="acute-container">
                <div class="acute-section-head">
                    <div class="acute-eyebrow">Tour details</div>
                    <h2 class="acute-heading">What each tour includes</h2>
                    <p class="acute-copy">Each route is structured around a clear experience theme, arranged transport, guided sightseeing, and included food or attraction value.</p>
                </div>
                <div class="acute-details-list">
                    <article v-for="route in routes" :key="`${route.key}-details`" class="acute-tour-panel">
                        <div class="acute-panel-image" :class="route.key"></div>
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
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="know-before" class="acute-section">
            <div class="acute-container">
                <div class="acute-section-head"><div class="acute-eyebrow">Before you book</div><h2 class="acute-heading">Clear answers for confident booking</h2><p class="acute-copy">Essential details to help you understand what is included, what may depend on availability and how your booking is confirmed.</p></div>
                <div class="acute-safety-grid">
                    <div class="acute-safety-card"><h3>Pick-up and timing</h3><p>Hotel pick-up and drop-off are included. Exact pick-up timing, route order and return timing are confirmed after availability is checked.</p></div>
                    <div class="acute-safety-card"><h3>Fujairah marine activities</h3><p>Swimming with turtles, shark sessions, beach access and oyster farm visits are confirmed subject to supplier availability, weather, sea conditions and guest safety requirements.</p></div>
                    <div class="acute-safety-card"><h3>Attraction access</h3><p>Al Ain Zoo and Ferrari World admission are included in their respective tours. Attraction operating hours, ride availability and entry rules may apply.</p></div>
                </div>
            </div>
        </section>

        <section id="private" class="acute-section acute-private">
            <div class="acute-container acute-private-grid">
                <div>
                    <div class="acute-eyebrow">Private bus enquiry</div>
                    <h2 class="acute-heading">Reserve the bus for your own group</h2>
                    <p class="acute-copy">The panoramic bus can also be requested for private groups, families, friends, corporate teams, celebrations, school groups, travel agencies and custom UAE experiences.</p>
                    <div class="acute-line-list"><div><span>Best for</span><strong>Families, companies, groups and occasions</strong></div><div><span>Route options</span><strong>Dubai, Al Ain, Fujairah, Abu Dhabi or custom</strong></div><div><span>Add-ons</span><strong>Shopping, photography and videography</strong></div></div>
                    <a class="acute-btn gold" href="#enquiry">Request Private Bus</a>
                </div>
                <div class="acute-private-image"></div>
            </div>
        </section>

        <section class="acute-section alt acute-audience">
            <div class="acute-container">
                <div class="acute-section-head">
                    <div class="acute-eyebrow">Best for</div>
                    <h2 class="acute-heading">Designed for guests who value ease and exclusivity</h2>
                    <p class="acute-copy">The panoramic bus format is especially useful when you want a premium day out arranged for you.</p>
                </div>
                <div class="acute-audience-grid">
                    <div v-for="[title, copy] in audiences" :key="title" class="acute-audience-card"><h3>{{ title }}</h3><p>{{ copy }}</p></div>
                </div>
            </div>
        </section>

        <section id="media-gallery" class="acute-section acute-media-showcase">
            <div class="acute-container">
                <div class="acute-section-head">
                    <div class="acute-eyebrow">Experience media</div>
                    <h2 class="acute-heading">A closer look at the journey</h2>
                    <p class="acute-copy">See the bus, onboard comfort, hosted moments and route highlights before you request availability.</p>
                </div>
                <div class="acute-gallery-wrap acute-gallery-wrap--expanded">
                    <div class="acute-gallery-feature" role="img" aria-label="Luxury panoramic bus exterior and route image"><div class="acute-gallery-caption"><strong>Luxury panoramic bus</strong><span>Exterior and route visuals from the experience.</span></div></div>
                    <div class="acute-gallery-grid">
                        <div v-for="[key, title, copy] in galleryItems" :key="key" class="acute-gallery-tile" :class="key"><div class="acute-gallery-caption"><strong>{{ title }}</strong><span>{{ copy }}</span></div></div>
                    </div>
                </div>
                <div class="acute-proof-note">Browse the bus setup, seating, destination highlights and onboard hospitality before requesting availability.</div>
            </div>
        </section>

        <section id="enquiry" class="acute-section acute-lead">
            <div class="acute-container acute-lead-grid">
                <div class="acute-lead-copy"><div class="acute-eyebrow">Check availability</div><h2 class="acute-heading">Request your panoramic bus experience</h2><p class="acute-copy">Share your preferred route, date and group details. The Acute Tourism team will confirm availability, hotel pick-up timing, route flow, attraction access and private bus options before payment.</p></div>
                <form class="acute-lead-form" @submit.prevent="submit">
                    <div v-if="page.props.flash.success" class="success-banner acute-full-field">{{ page.props.flash.success }}</div>
                    <div class="acute-form-grid">
                        <label>Full Name<input v-model="form.name" placeholder="Enter your name" required type="text" autocomplete="name" /></label>
                        <label>WhatsApp Number<input v-model="form.phone" placeholder="+971 5X XXX XXXX" required type="tel" autocomplete="tel" /></label>
                        <label>Preferred Tour<select v-model="form.route" required><option value="">Select tour</option><option v-for="route in routes" :key="route.title" :value="route.title">{{ route.title }}</option><option value="Private Bus Enquiry">Private Bus Enquiry</option></select></label>
                        <label>Preferred Date<input v-model="form.travel_date" type="date" /></label>
                    </div>
                    <label class="acute-full-field">Additional Request<textarea v-model="form.message" placeholder="Number of guests, hotel location, preferred route, private group request, or any special occasion." rows="4"></textarea></label>
                    <button class="acute-btn gold acute-submit" type="submit" :disabled="form.processing">{{ form.processing ? 'Sending...' : 'Send Enquiry' }}</button>
                    <a class="acute-quick-chat" href="https://wa.me/97145654898?text=Hello%20Acute%20Tourism%2C%20I%20want%20to%20check%20availability%20for%20the%20panoramic%20bus%20tour." rel="noopener" target="_blank">Prefer WhatsApp? Speak to our team directly</a>
                    <div class="acute-note">Submitting this form does not confirm booking. The team will confirm seat availability, hotel pick-up details, final timing and any supplier-dependent activities before payment.</div>
                </form>
            </div>
        </section>

        <section class="acute-section cream">
            <div class="acute-container">
                <div class="acute-section-head"><div class="acute-eyebrow">Questions guests may ask</div><h2 class="acute-heading">Frequently asked questions</h2></div>
                <div class="acute-faq-accordion">
                    <details v-for="[question, answer] in faqs" :key="question" class="acute-faq-item"><summary>{{ question }}</summary><div class="acute-faq-answer">{{ answer }}</div></details>
                </div>
            </div>
        </section>

        <div class="acute-sticky-cta" aria-label="Request panoramic bus tour availability">
            <span><strong>Limited seats</strong> on scheduled departures</span>
            <a class="acute-btn gold" href="#enquiry">Request Availability</a>
        </div>
    </main>
</template>
