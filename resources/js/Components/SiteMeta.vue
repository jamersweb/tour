<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: null,
    },
    description: {
        type: String,
        default: null,
    },
    image: {
        type: String,
        default: null,
    },
});

const page = usePage();

const siteName = computed(() => page.props.site.name);
const defaultMeta = computed(() => page.props.site.defaultMeta);
const canonicalUrl = computed(() => page.props.site.currentUrl);
const organization = computed(() => page.props.site.organization);
const resolvedTitle = computed(() => props.title || defaultMeta.value.title);
const resolvedDescription = computed(() => props.description || defaultMeta.value.description);
const resolvedImage = computed(() => props.image || defaultMeta.value.image);
const fullTitle = computed(() => (
    resolvedTitle.value === siteName.value ? siteName.value : `${resolvedTitle.value} | ${siteName.value}`
));
const canonicalPath = computed(() => {
    try {
        return new URL(canonicalUrl.value).pathname;
    } catch (error) {
        return page.url?.split('?')[0] || '/';
    }
});
const baseOrganizationSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': organization.value.type || 'Organization',
    name: organization.value.name,
    legalName: organization.value.legalName,
    url: organization.value.url || canonicalUrl.value,
    logo: organization.value.logo || undefined,
    sameAs: organization.value.socialLinks?.length ? organization.value.socialLinks : undefined,
    contactPoint: (() => {
        const contact = organization.value.contact;
        if (!contact?.email && !contact?.phone) {
            return undefined;
        }

        return {
            '@type': 'ContactPoint',
            email: contact.email || undefined,
            telephone: [contact.phone, contact.phoneSecondary].filter(Boolean).join(' / ') || undefined,
            contactType: 'customer service',
        };
    })(),
    address: organization.value.contact?.address
        ? {
            '@type': 'PostalAddress',
            streetAddress: organization.value.contact.address,
            addressLocality: 'Dubai',
            addressCountry: 'AE',
        }
        : undefined,
}));
const breadcrumbSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
        {
            '@type': 'ListItem',
            position: 1,
            name: 'Home',
            item: organization.value.url || '/',
        },
        ...(canonicalPath.value === '/'
            ? []
            : [{
                '@type': 'ListItem',
                position: 2,
                name: resolvedTitle.value,
                item: canonicalUrl.value,
            }]),
    ],
}));
const pageSpecificSchema = computed(() => {
    const path = canonicalPath.value;
    const pageNode = (type) => ({
        '@context': 'https://schema.org',
        '@type': type,
        name: resolvedTitle.value,
        description: resolvedDescription.value,
        url: canonicalUrl.value,
    });

    if (path === '/') {
        return [
            { ...baseOrganizationSchema.value, '@type': 'TravelAgency' },
            { ...baseOrganizationSchema.value, '@type': 'LocalBusiness' },
        ];
    }

    if (path === '/about') {
        return [pageNode('AboutPage'), { ...baseOrganizationSchema.value, '@type': 'TravelAgency' }];
    }

    if (path === '/dubai-tours-and-tickets' || path === '/dubai-holiday-packages') {
        return [pageNode('CollectionPage'), pageNode('ItemList')];
    }

    if (path === '/tourist-visa-assistance-uae-residents') {
        return [pageNode('Service'), pageNode('FAQPage')];
    }

    if (path === '/luxury-bus-tour-dubai') {
        return [pageNode('Service'), pageNode('TouristTrip'), pageNode('Product'), pageNode('FAQPage')];
    }

    if (path === '/corporate-travel-event-planning-dubai') {
        return [pageNode('Service'), pageNode('FAQPage')];
    }

    if (path === '/earn-with-tourgrat') {
        return [pageNode('WebPage'), baseOrganizationSchema.value];
    }

    if (path === '/blog') {
        return [pageNode('Blog'), breadcrumbSchema.value];
    }

    if (path.startsWith('/blog/')) {
        return [pageNode('BlogPosting'), breadcrumbSchema.value];
    }

    return [breadcrumbSchema.value];
});
const structuredData = computed(() => JSON.stringify([
    baseOrganizationSchema.value,
    {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: siteName.value,
        url: organization.value.url || canonicalUrl.value,
    },
    ...pageSpecificSchema.value,
]));
</script>

<template>
    <Head :title="resolvedTitle">
        <meta name="description" :content="resolvedDescription" />
        <link rel="canonical" :href="canonicalUrl" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" :content="siteName" />
        <meta property="og:title" :content="fullTitle" />
        <meta property="og:description" :content="resolvedDescription" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta v-if="resolvedImage" property="og:image" :content="resolvedImage" />
        <meta name="twitter:card" :content="resolvedImage ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title" :content="fullTitle" />
        <meta name="twitter:description" :content="resolvedDescription" />
        <meta v-if="resolvedImage" name="twitter:image" :content="resolvedImage" />
        <script type="application/ld+json" v-text="structuredData"></script>
    </Head>
</template>
