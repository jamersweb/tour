<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    featuredArticle: Object,
    categories: Array,
    selectedCategory: Object,
    articles: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="journal-page">
        <section class="about-hero journal-hero">
            <div class="container about-hero__grid">
                <div>
                    <p class="about-kicker">Blog</p>
                    <h1 class="about-title">
                        {{ selectedCategory ? selectedCategory.name : 'Dubai Travel Planning Tips' }}
                    </h1>
                    <p class="about-copy">
                        {{
                            selectedCategory?.description ||
                            'Simple articles to help customers choose Dubai tours, plan holiday packages, understand visa requirements, compare experiences, and book with more confidence.'
                        }}
                    </p>
                    <div class="hero-pills">
                        <span>Dubai Tours</span>
                        <span>Holiday Packages</span>
                        <span>Visa Guides</span>
                        <span>Corporate Travel</span>
                    </div>
                </div>

                <div class="about-card about-card--primary">
                    <p class="about-card__label">Featured article</p>
                    <h2>{{ featuredArticle?.title || 'How to Choose a Premium Desert Safari in Dubai' }}</h2>
                    <p>{{ featuredArticle?.excerpt || 'A simple guide to comparing desert safari options based on comfort, pickup, camp quality, food, entertainment, and service level.' }}</p>
                    <ul class="about-list about-list--tight">
                        <li>Dubai tours</li>
                        <li>Holiday packages</li>
                        <li>Visa guides</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section-block journal-section">
            <div class="container">
                <div v-if="categories.length" class="tag-row">
                    <Link class="filter-chip" :class="{ active: !selectedCategory }" href="/blog">All articles</Link>
                    <Link
                        v-for="category in categories"
                        :key="category.slug"
                        class="filter-chip"
                        :class="{ active: selectedCategory?.slug === category.slug }"
                        :href="`/blog?category=${category.slug}`"
                    >
                        {{ category.name }} <span v-if="category.articleCount">({{ category.articleCount }})</span>
                    </Link>
                </div>

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
                    <Link class="button-primary card-button" :href="`/blog/${featuredArticle.slug}`">Read article</Link>
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
                        <Link class="button-primary card-button" :href="`/blog/${article.slug}`">Read article</Link>
                    </article>
                </div>
            </div>
        </section>
    </div>
</template>
