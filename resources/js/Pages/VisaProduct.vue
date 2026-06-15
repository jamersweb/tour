<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    contact: Object,
    interestOptions: { type: Array, default: () => [] },
    visa: { type: Object, default: () => ({}) },
});

const page = usePage();
const fiveStars = '\u2605\u2605\u2605\u2605\u2605';
const defaultVisa = {
    shortTitle: 'Canada Visa',
    kicker: 'Canada Visa Assistance',
    title: 'Get Canada Visitor Visa Guidance Without the Confusion',
    copy: 'Get enough clarity online to understand the Canada visitor visa route, then speak with an Acute Tourism visa consultant to review your profile, documents, and next steps before you proceed.',
    image: 'https://images.unsplash.com/photo-1517935706615-2717063c2225?auto=format&fit=crop&w=1000&q=80',
    badge: 'Visitor Visa',
    requirementsTitle: 'Canada Visa Requirements',
    requirementsCopy: 'Use this as a starting checklist. Exact requirements depend on nationality, UAE residence status, employment type, travel history, and purpose of visit.',
    feesCopy: 'Canada visa processing can vary by applicant profile, season, biometrics availability, and immigration authority review time.',
    processTitle: 'How Canada Visa Support Works',
    processCopy: 'The page gives you the basic information. The real value is the direct consultation where an Acute visa agent reviews your situation and guides you personally.',
    whyTitle: 'Information Online. Final Clarity With a Real Agent.',
    whyCopy: 'Canada visa applications can feel complicated because the strength of the file depends on your personal situation, not just a general checklist.',
    disclaimer: 'Canada visa approval is decided only by the Canadian immigration authority. Acute Tourism provides document guidance, application preparation support, and file readiness review, but does not guarantee approval or processing time.',
    faqTitle: 'Canada Visa FAQs',
    faqCopy: 'Quick answers to help UAE residents understand the Canada visitor visa support process.',
    stats: [
        ['4.8\u2605', 'Customer rating'],
        ['60-120', 'Working days estimate'],
        ['AED 400', 'Service fee from'],
    ],
    quickFacts: [
        ['Visa type', 'Temporary Resident Visa'],
        ['Application style', 'Online + biometrics'],
        ['Main documents', 'Passport, funds, travel purpose'],
        ['Best for', 'Tourism, family visit, short stay'],
    ],
};

const visaPage = computed(() => ({
    ...defaultVisa,
    ...props.visa,
    stats: props.visa?.stats ?? defaultVisa.stats,
    quickFacts: props.visa?.quickFacts ?? defaultVisa.quickFacts,
}));

const defaultInterest = computed(() => {
    if (props.interestOptions?.includes('General Planning')) {
        return 'General Planning';
    }

    return props.interestOptions?.[0] ?? 'General Planning';
});

const whatsappHref = computed(() => {
    const number = props.contact?.whatsappNumber ? String(props.contact.whatsappNumber).replace(/\D/g, '') : '';
    const text = encodeURIComponent(`Hi Acute Tourism, I need help with a ${visaPage.value.shortTitle} application.`);

    return number ? `https://wa.me/${number}?text=${text}` : null;
});

const phoneHref = computed(() => {
    const phone = props.contact?.phone ? String(props.contact.phone).replace(/[^\d+]/g, '') : '';

    return phone ? `tel:${phone}` : '#consultant';
});

const defaultMessage = computed(() => `I need ${visaPage.value.shortTitle} assistance. Please review my profile, documents, and next steps.`);

const form = useForm({
    source: 'visa-product-page',
    name: '',
    email: '',
    phone: '',
    travel_date: '',
    guest_count: 1,
    interest: defaultInterest.value,
    message: defaultMessage.value,
});
const nationality = ref('');
const travelPurpose = ref('Tourism');
const applicantType = ref('Employee');
const travelTiming = ref('Within 1 month');
const previousApplication = ref('No');
const showStickyCta = ref(false);
let stickyCtaTimer = null;

const updateStickyCta = () => {
    const hero = document.querySelector('.visa-product-hero');
    const heroBottom = hero?.getBoundingClientRect().bottom ?? 0;

    showStickyCta.value = heroBottom < 0;
};

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

const resetLeadForm = () => {
    form.reset('name', 'email', 'phone', 'travel_date', 'guest_count');
    form.interest = defaultInterest.value;
    form.message = defaultMessage.value;
    nationality.value = '';
    travelPurpose.value = 'Tourism';
    applicantType.value = 'Employee';
    travelTiming.value = 'Within 1 month';
    previousApplication.value = 'No';
};

const submit = () => {
    const typedMessage = form.message;

    form.interest = defaultInterest.value;
    form.message = [
        `Nationality: ${nationality.value || 'Not provided'}`,
        `Travel purpose: ${travelPurpose.value}`,
        `Applicant type: ${applicantType.value}`,
        `Travel timing: ${travelTiming.value}`,
        `Applied before: ${previousApplication.value}`,
        `Message: ${form.message}`,
    ].join('\n');
    form.post('/inquiries', {
        preserveScroll: true,
        onSuccess: () => resetLeadForm(),
        onError: () => {
            form.message = typedMessage;
        },
    });
};

const defaultTrustItems = [
    ['\uD83D\uDC64 Direct visa consultant', 'Human guidance, not self-service only'],
    ['\uD83D\uDCC4 Document guidance', 'Know what to prepare'],
    ['\uD83D\uDD0E File readiness review', 'Spot weak areas early'],
    ['\uD83D\uDD12 Secure payment', 'Clear confirmation'],
];

const defaultRequirements = [
    ['\uD83D\uDCD8', 'Valid passport', 'Clear passport copy with sufficient validity and travel history pages.'],
    ['\uD83E\uDEAA', 'UAE residence proof', 'Emirates ID and UAE residence visa, where applicable.'],
    ['\uD83C\uDFE6', 'Bank statement', 'Recent bank statement showing stable financial capacity.'],
    ['\uD83D\uDCBC', 'Employment or business proof', 'Salary certificate, NOC, trade license, or proof of occupation.'],
    ['\u2708\uFE0F', 'Travel purpose', 'Tourism plan, family visit details, invitation, or itinerary where required.'],
    ['\uD83D\uDD90\uFE0F', 'Biometrics', 'Most applicants are required to complete biometrics after submission.'],
];

const defaultFeeCards = [
    ['Acute Service Fee', 'AED 400', ['Document checklist guidance', 'Profile-based document review', 'Application preparation support', 'Clear next-step guidance'], true],
    ['Processing Estimate', '60-120', ['Working days estimate', 'Timeline may vary', 'Biometrics may be required', 'Embassy/authority decision only'], false],
    ['Application Style', 'Online', ['Visitor visa route', 'Digital application preparation', 'Biometrics after submission', 'Passport request if approved'], false],
];

const defaultTimelineSteps = [
    ['Step 1', 'Read the key requirements', 'Use the page to understand the visa type, basic documents, fees, and timeline.'],
    ['Step 2', 'Share your profile with an agent', 'A consultant checks your travel purpose, employment status, residence details, and timing.'],
    ['Step 3', 'Review your documents together', 'The agent helps identify missing documents, weak proof, or areas that need clearer explanation.'],
    ['Step 4', 'Prepare for submission', 'You receive clearer next steps for online application, biometrics, and follow-up.'],
    ['Step 5', 'Wait for authority decision', 'Final approval, refusal, or passport request is decided only by the immigration authority.'],
];

const defaultInfoCards = [
    ['\uD83D\uDC64', 'Direct agent support', 'Speak with someone who can understand your case, not just show a generic document list.'],
    ['\uD83D\uDD0E', 'Profile-based review', 'Get guidance based on your job, travel history, funds, purpose of visit, and timing.'],
    ['\uD83E\uDDED', 'Clear next steps', 'Know what to prepare, what may be weak, and what to discuss before moving forward.'],
];

const defaultReviewCards = [
    ['The team helped me understand what was missing in my Canada visitor visa documents before I applied.', 'Ahmed R.'],
    ['Clear guidance and fast response. The document checklist made the process easier to manage.', 'Mariam A.'],
    ['I liked that they explained the weak areas in my file instead of just asking for documents.', 'Priya S.'],
];

const defaultFaqItems = [
    ['Can Acute Tourism guarantee Canada visa approval?', 'No. Canada visa approval is decided only by the Canadian immigration authority. Acute Tourism helps with document guidance, file preparation, and readiness review.'],
    ['How long does Canada visa processing take?', 'The estimate shown here is 60-120 working days. Actual timelines can vary based on applicant profile, biometrics, season, and authority review.'],
    ['Do I need biometrics for Canada visa?', 'Most applicants are required to complete biometrics after submission. The exact requirement depends on applicant profile and official instructions.'],
    ['What documents are most important?', 'Passport, UAE residence proof, bank statement, employment or business proof, travel purpose, and supporting documents are commonly important. Exact requirements vary.'],
    ['Can you help if I started the application already?', 'Yes. Acute can help review your file readiness and identify missing or weak documents, depending on your application stage.'],
];

const trustItems = computed(() => props.visa?.trustItems ?? defaultTrustItems);
const requirements = computed(() => props.visa?.requirements ?? defaultRequirements);
const feeCards = computed(() => props.visa?.feeCards ?? defaultFeeCards);
const timelineSteps = computed(() => props.visa?.timelineSteps ?? defaultTimelineSteps);
const infoCards = computed(() => props.visa?.infoCards ?? defaultInfoCards);
const reviewCards = computed(() => props.visa?.reviewCards ?? defaultReviewCards);
const faqItems = computed(() => props.visa?.faqItems ?? defaultFaqItems);
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="visa-product-reference">
        <div class="visa-product-ambient visa-product-ambient--one" aria-hidden="true"></div>
        <div class="visa-product-ambient visa-product-ambient--two" aria-hidden="true"></div>

        <div class="container visa-product-breadcrumb">
            <Link href="/tourist-visa-assistance-uae-residents">Visa Services</Link>
            <span>/</span>
            <span>{{ visaPage.shortTitle }}</span>
        </div>

        <main>
            <section class="visa-product-hero">
                <div class="container visa-product-hero__grid">
                    <div>
                        <p class="visa-product-kicker">{{ visaPage.kicker }}</p>
                        <h1 class="visa-product-title">{{ visaPage.title }}</h1>
                        <p class="visa-product-copy">
                            {{ visaPage.copy }}
                        </p>
                        <div class="visa-product-actions">
                            <a class="visa-product-btn visa-product-btn--gold" href="#requirements">Check Required Documents</a>
                            <a class="visa-product-btn visa-product-btn--outline" href="#consultant">Speak to a Visa Consultant</a>
                        </div>
                        <div class="visa-product-stats">
                            <div v-for="[value, label] in visaPage.stats" :key="label" class="visa-product-stat">
                                <strong>{{ value }}</strong>
                                <span>{{ label }}</span>
                            </div>
                        </div>
                    </div>

                    <aside class="visa-product-card">
                        <div class="visa-product-card__image">
                            <img :src="visaPage.image" :alt="visaPage.kicker" />
                            <span class="visa-product-card__badge">{{ visaPage.badge }}</span>
                        </div>
                        <div class="visa-product-card__body">
                            <div v-for="[label, value] in visaPage.quickFacts" :key="label" class="visa-product-quick-row">
                                <span>{{ label }}</span>
                                <strong>{{ value }}</strong>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="visa-product-trust">
                <div class="container visa-product-trust__grid">
                    <div v-for="[title, copy] in trustItems" :key="title" class="visa-product-trust__item">
                        <strong>{{ title }}</strong>
                        <span>{{ copy }}</span>
                    </div>
                </div>
            </section>

            <section id="requirements" class="visa-product-section">
                <div class="container">
                    <div class="visa-product-section-heading">
                        <div>
                            <p class="visa-product-eyebrow">Requirements</p>
                            <h2>{{ visaPage.requirementsTitle }}</h2>
                            <p>{{ visaPage.requirementsCopy }}</p>
                        </div>
                    </div>

                    <div class="visa-product-requirement-grid">
                        <div class="visa-product-panel">
                            <div class="visa-product-requirement-list">
                                <div v-for="[icon, title, copy] in requirements" :key="title" class="visa-product-req-item">
                                    <div class="visa-product-req-icon">{{ icon }}</div>
                                    <div><strong>{{ title }}</strong><span>{{ copy }}</span></div>
                                </div>
                            </div>
                        </div>

                        <div id="consultant" class="visa-product-panel">
                            <div class="visa-product-check-card">
                                <div class="visa-product-check-card__label">Talk to a visa consultant</div>
                                <h3>Not sure if your documents are ready?</h3>
                                <p>Use the quick form to prepare the basics, then a sales agent can review your profile, explain what may be missing, and guide you on the next step.</p>
                            </div>
                            <form class="visa-product-form" @submit.prevent="submit">
                                <div v-if="page.props.flash?.success" class="success-banner">{{ page.props.flash.success }}</div>
                                <div class="visa-product-form-row">
                                    <label class="visa-product-field">
                                        <span>Full name</span>
                                        <input v-model="form.name" type="text" placeholder="Your name" autocomplete="name" />
                                        <small v-if="form.errors.name">{{ form.errors.name }}</small>
                                    </label>
                                    <label class="visa-product-field">
                                        <span>Phone / WhatsApp</span>
                                        <input v-model="form.phone" type="tel" placeholder="+971 5X XXX XXXX" autocomplete="tel" />
                                        <small v-if="form.errors.phone">{{ form.errors.phone }}</small>
                                    </label>
                                </div>
                                <div class="visa-product-form-row">
                                    <label class="visa-product-field">
                                        <span>Email address</span>
                                        <input v-model="form.email" type="email" placeholder="name@email.com" autocomplete="email" />
                                        <small v-if="form.errors.email">{{ form.errors.email }}</small>
                                    </label>
                                    <label class="visa-product-field">
                                        <span>Nationality</span>
                                        <input v-model="nationality" type="text" placeholder="Your nationality" />
                                    </label>
                                </div>
                                <div class="visa-product-form-row">
                                    <label class="visa-product-field">
                                        <span>Date of travel</span>
                                        <input v-model="form.travel_date" type="date" />
                                        <small v-if="form.errors.travel_date">{{ form.errors.travel_date }}</small>
                                    </label>
                                    <label class="visa-product-field">
                                        <span>Number of people</span>
                                        <input v-model="form.guest_count" type="number" min="1" placeholder="e.g. 2" />
                                        <small v-if="form.errors.guest_count">{{ form.errors.guest_count }}</small>
                                    </label>
                                </div>
                                <div class="visa-product-form-row">
                                    <label class="visa-product-field">
                                        <span>Travel purpose</span>
                                        <select v-model="travelPurpose">
                                            <option>Tourism</option>
                                            <option>Family visit</option>
                                            <option>Business visit</option>
                                            <option>Transit / Other</option>
                                        </select>
                                    </label>
                                    <label class="visa-product-field">
                                        <span>Applicant type</span>
                                        <select v-model="applicantType">
                                            <option>Employee</option>
                                            <option>Business owner</option>
                                            <option>Student</option>
                                            <option>Family sponsor / Dependent</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="visa-product-form-row">
                                    <label class="visa-product-field">
                                        <span>Travel timing</span>
                                        <select v-model="travelTiming">
                                            <option>Within 1 month</option>
                                            <option>1-3 months</option>
                                            <option>3+ months</option>
                                            <option>Not decided yet</option>
                                        </select>
                                    </label>
                                    <label class="visa-product-field">
                                        <span>Have you applied before?</span>
                                        <select v-model="previousApplication">
                                            <option>No</option>
                                            <option>Yes, approved before</option>
                                            <option>Yes, refused before</option>
                                            <option>Not sure</option>
                                        </select>
                                    </label>
                                </div>
                                <label class="visa-product-field">
                                    <span>Message / Concern</span>
                                    <textarea v-model="form.message" placeholder="Tell us your travel plan, current document concern, or urgency."></textarea>
                                    <small v-if="form.errors.message">{{ form.errors.message }}</small>
                                </label>
                                <p class="visa-product-form-note">A sales agent will use these details to understand your case before contacting you. Visa approval is still decided by the Canadian immigration authority.</p>
                                <button class="visa-product-form-btn" type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Sending...' : 'Request Consultant Review' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <section id="fees" class="visa-product-section">
                <div class="container">
                    <div class="visa-product-section-heading"><div><p class="visa-product-eyebrow">Fees & Timeline</p><h2>Visa Fees & Processing Time</h2><p>{{ visaPage.feesCopy }}</p></div></div>
                    <div class="visa-product-fee-grid">
                        <article v-for="[title, price, items, featured] in feeCards" :key="title" class="visa-product-fee-card" :class="{ 'is-featured': featured }">
                            <h3>{{ title }}</h3>
                            <div class="visa-product-fee-price">{{ price }}</div>
                            <ul><li v-for="item in items" :key="item">{{ item }}</li></ul>
                        </article>
                    </div>
                </div>
            </section>

            <section class="visa-product-section">
                <div class="container">
                    <div class="visa-product-section-heading"><div><p class="visa-product-eyebrow">Process</p><h2>{{ visaPage.processTitle }}</h2><p>{{ visaPage.processCopy }}</p></div></div>
                    <div class="visa-product-panel">
                        <div class="visa-product-timeline">
                            <div v-for="[time, title, copy] in timelineSteps" :key="title" class="visa-product-timeline-step">
                                <div class="visa-product-timeline-time">{{ time }}</div>
                                <div class="visa-product-timeline-content"><strong>{{ title }}</strong><span>{{ copy }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="visa-product-section">
                <div class="container">
                    <div class="visa-product-section-heading"><div><p class="visa-product-eyebrow">Why prepare with Acute</p><h2>{{ visaPage.whyTitle }}</h2><p>{{ visaPage.whyCopy }}</p></div></div>
                    <div class="visa-product-info-grid">
                        <article v-for="[icon, title, copy] in infoCards" :key="title" class="visa-product-info-card">
                            <div class="visa-product-info-card__icon">{{ icon }}</div>
                            <h3>{{ title }}</h3>
                            <p>{{ copy }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="visa-product-section">
                <div class="container visa-product-disclaimer"><div class="visa-product-disclaimer-icon">!</div><div><strong>Important:</strong> {{ visaPage.disclaimer }}</div></div>
            </section>

            <section class="visa-product-section">
                <div class="container">
                    <div class="visa-product-section-heading"><div><p class="visa-product-eyebrow">Customer Reviews</p><h2>What travelers say</h2></div></div>
                    <div class="visa-product-review-grid">
                        <article v-for="[quote, author] in reviewCards" :key="quote" class="visa-product-review-card">
                            <div class="visa-product-review-stars">{{ fiveStars }} 5.0</div>
                            <p>"{{ quote }}"</p>
                            <strong>{{ author }}</strong>
                        </article>
                    </div>
                </div>
            </section>

            <section class="visa-product-section">
                <div class="container">
                    <div class="visa-product-section-heading"><div><p class="visa-product-eyebrow">Common questions</p><h2>{{ visaPage.faqTitle }}</h2><p>{{ visaPage.faqCopy }}</p></div></div>
                    <div class="visa-product-faq-list">
                        <details v-for="[question, answer] in faqItems" :key="question" class="visa-product-faq-item" :open="question === faqItems[0][0]">
                            <summary>{{ question }}</summary>
                            <p>{{ answer }}</p>
                        </details>
                    </div>
                </div>
            </section>
        </main>

        <div v-if="showStickyCta" class="visa-product-sticky-cta">
            <a class="visa-product-sticky-cta__gold" href="#consultant">Request Visa Review</a>
            <a class="visa-product-sticky-cta__outline" :href="phoneHref">Call Consultant</a>
        </div>
    </div>
</template>
