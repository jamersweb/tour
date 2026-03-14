<script setup>
import SiteMeta from '../Components/SiteMeta.vue';
import SiteLayout from '../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({
    seo: Object,
    featuredFaqs: Array,
    faqGroups: Array,
});
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <section class="page-intro">
        <div class="container narrow">
            <p class="eyebrow">FAQ</p>
            <h1 class="page-title">Launch questions answered in one place.</h1>

            <div v-if="featuredFaqs.length" class="faq-stack">
                <article v-for="faq in featuredFaqs" :key="faq.question" class="faq-item">
                    <p class="card-tag">{{ faq.category }}</p>
                    <h3>{{ faq.question }}</h3>
                    <p>{{ faq.answer }}</p>
                </article>
            </div>

            <div class="faq-group-stack">
                <section v-for="group in faqGroups" :key="group.category" class="faq-group">
                    <div class="section-heading">
                        <p class="eyebrow">{{ group.category }}</p>
                    </div>

                    <div class="faq-stack">
                        <article v-for="faq in group.items" :key="faq.question" class="faq-item">
                            <h3>{{ faq.question }}</h3>
                            <p>{{ faq.answer }}</p>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </section>
</template>
