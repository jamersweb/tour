<script setup>
import { Link } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    article: Object,
    relatedArticles: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" :image="seo.image" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">{{ article.category }}</p>
            <h1 class="page-title">{{ article.title }}</h1>
            <p class="page-copy">{{ article.excerpt }}</p>

            <div class="tag-row">
                <span class="filter-chip active">{{ article.readTime }}</span>
                <span v-if="article.publishedAt" class="filter-chip">{{ article.publishedAt }}</span>
            </div>

            <div v-if="article.heroImageUrl" class="page-hero-media">
                <img :src="article.heroImageUrl" :alt="article.title" />
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="container narrow">
            <article class="journal-content">{{ article.content }}</article>
        </div>
    </section>

    <section v-if="relatedArticles.length" class="section-block section-contrast">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Related Journal</p>
                <h2>Keep content discovery inside the premium planning path.</h2>
            </div>

            <div class="card-grid card-grid-three">
                <article v-for="item in relatedArticles" :key="item.slug" class="info-card">
                    <p class="card-tag">{{ item.category }}</p>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.excerpt }}</p>
                    <p>{{ item.readTime }}</p>
                    <Link class="card-link" :href="`/journal/${item.slug}`">Read article</Link>
                </article>
            </div>
        </div>
    </section>
</template>
