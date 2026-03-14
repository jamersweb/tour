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
const fullTitle = computed(() =>
    resolvedTitle.value === siteName.value ? siteName.value : `${resolvedTitle.value} | ${siteName.value}`,
);
const structuredData = computed(() =>
    JSON.stringify(
        [
            {
                '@context': 'https://schema.org',
                '@type': organization.value.type || 'Organization',
                name: organization.value.name,
                legalName: organization.value.legalName,
                url: organization.value.url || canonicalUrl.value,
                logo: organization.value.logo || undefined,
                sameAs: organization.value.socialLinks?.length ? organization.value.socialLinks : undefined,
                contactPoint:
                    organization.value.contact?.email || organization.value.contact?.phone
                        ? {
                              '@type': 'ContactPoint',
                              email: organization.value.contact.email || undefined,
                              telephone: organization.value.contact.phone || undefined,
                              contactType: 'customer service',
                          }
                        : undefined,
                address: organization.value.contact?.address
                    ? {
                          '@type': 'PostalAddress',
                          streetAddress: organization.value.contact.address,
                          addressLocality: 'Dubai',
                          addressCountry: 'AE',
                      }
                    : undefined,
            },
            {
                '@context': 'https://schema.org',
                '@type': 'WebSite',
                name: siteName.value,
                url: organization.value.url || canonicalUrl.value,
            },
        ],
        null,
        0,
    ),
);
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
