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
            <h1 class="page-title">Guides, comparisons, and editorial content that support premium buying intent.</h1>
            <p class="page-copy">
                The journal supports discovery with destination knowledge, experience comparisons,
                and practical buyer education for Dubai and Abu Dhabi planning.
            </p>

            <article v-if="featuredArticle" class="showcase-card journal-feature">
                <div v-if="featuredArticle.heroImageUrl" class="showcase-media">
                    <img :src="featuredArticle.heroImageUrl" :alt="featuredArticle.title" />
                </div>
                <div class="showcase-meta">
                    <span class="card-tag-ghost">{{ featuredArticle.category }}</span>
                    <span class="card-tag-accent">{{ featuredArticle.readTime }}</span>
                </div>
                <h3>{{ featuredArticle.title }}</h3>
                <p>{{ featuredArticle.excerpt }}</p>
                <p v-if="featuredArticle.publishedAt" class="meta-copy">{{ featuredArticle.publishedAt }}</p>
                <Link class="button-primary card-button" :href="`/journal/${featuredArticle.slug}`">Read featured article</Link>
            </article>

            <div class="card-grid card-grid-three">
                <article v-for="article in articles" :key="article.slug" class="info-card">
                    <div v-if="article.heroImageUrl" class="card-media">
                        <img :src="article.heroImageUrl" :alt="article.title" />
                    </div>
                    <div class="showcase-meta">
                        <span class="card-tag-ghost">{{ article.category }}</span>
                        <span class="card-tag-accent">{{ article.readTime }}</span>
                    </div>
                    <h3>{{ article.title }}</h3>
                    <p>{{ article.excerpt }}</p>
                    <p class="meta-copy">{{ article.publishedAt }}</p>
                    <Link class="button-primary card-button" :href="`/journal/${article.slug}`">Read article</Link>
                </article>
            </div>
        </div>
    </section>
</template>
