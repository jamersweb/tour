<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    featuredArticle: Object,
    articles: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container">
            <p class="eyebrow">Journal</p>
            <h1 class="page-title">Editorial support for SEO and premium positioning.</h1>
            <p class="page-copy">
                This section will house destination guides, comparison content, and buyer education
                for high-intent Dubai experience searches.
            </p>

            <article v-if="featuredArticle" class="showcase-card journal-feature">
                <div v-if="featuredArticle.heroImageUrl" class="showcase-media">
                    <img :src="featuredArticle.heroImageUrl" :alt="featuredArticle.title" />
                </div>
                <div class="showcase-meta">
                    <span>{{ featuredArticle.category }}</span>
                    <span>{{ featuredArticle.readTime }}</span>
                </div>
                <h3>{{ featuredArticle.title }}</h3>
                <p>{{ featuredArticle.excerpt }}</p>
                <p v-if="featuredArticle.publishedAt" class="meta-copy">{{ featuredArticle.publishedAt }}</p>
                <Link class="card-link" :href="`/journal/${featuredArticle.slug}`">Read featured article</Link>
            </article>

            <div class="card-grid card-grid-three">
                <article v-for="article in articles" :key="article.slug" class="info-card">
                    <div v-if="article.heroImageUrl" class="card-media">
                        <img :src="article.heroImageUrl" :alt="article.title" />
                    </div>
                    <p class="card-tag">{{ article.category }}</p>
                    <h3>{{ article.title }}</h3>
                    <p>{{ article.excerpt }}</p>
                    <p class="meta-copy">{{ article.readTime }}<span v-if="article.publishedAt"> • {{ article.publishedAt }}</span></p>
                    <Link class="card-link" :href="`/journal/${article.slug}`">Read article</Link>
                </article>
            </div>
        </div>
    </section>
</template>
