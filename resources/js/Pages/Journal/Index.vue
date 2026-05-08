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

    <div class="journal-page">
        <section class="about-hero journal-hero">
            <div class="container about-hero__grid">
                <div>
                    <p class="about-kicker">Journal</p>
                    <h1 class="about-title">Editorial content that helps visitors plan better and buy with more clarity.</h1>
                    <p class="about-copy">
                        The journal supports destination discovery with practical buying guidance, comparisons,
                        and editorial content tied to Dubai and UAE travel planning.
                    </p>
                </div>

                <div class="about-card about-card--primary">
                    <p class="about-card__label">What you will find</p>
                    <ul class="about-list about-list--tight">
                        <li>Planning guides and route comparisons</li>
                        <li>Experience selection context</li>
                        <li>Travel content that supports booking intent</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section-block journal-section">
            <div class="container">
                <article v-if="featuredArticle" class="showcase-card journal-feature journal-feature--refined">
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
                    <Link class="button-primary card-button" :href="`/journal/${featuredArticle.slug}`">Read article</Link>
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
    </div>
</template>
