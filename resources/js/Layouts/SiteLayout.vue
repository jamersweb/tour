<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const logoutForm = useForm({});
</script>

<template>
    <div class="site-shell">
        <div class="ambient ambient-one"></div>
        <div class="ambient ambient-two"></div>

        <header class="site-header">
            <div class="container nav-row">
                <Link class="brand-mark" :href="route('home')">
                    <span class="brand-kicker">{{ page.props.site.brandKicker }}</span>
                    <span class="brand-name">{{ page.props.site.brandName }}</span>
                </Link>

                <nav class="primary-nav">
                    <Link
                        v-for="item in page.props.site.primaryNavigation"
                        :key="item.href"
                        class="nav-link"
                        :href="item.href"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="header-actions">
                    <Link v-if="page.props.auth.user" class="header-cta" :href="page.props.auth.user.dashboardUrl">My Account</Link>
                    <Link v-else class="header-cta" href="/login">Login</Link>
                    <button
                        v-if="page.props.auth.user"
                        class="button-secondary header-logout"
                        @click="logoutForm.post('/logout')"
                    >
                        Logout
                    </button>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <footer class="site-footer">
            <div class="container footer-grid">
                <div>
                    <p class="footer-label">{{ page.props.site.name }}</p>
                    <p class="footer-copy">{{ page.props.site.footer.description }}</p>
                </div>

                <div>
                    <p class="footer-label">Build Notes</p>
                    <ul class="footer-list">
                        <li v-for="note in page.props.site.footer.buildNotes" :key="note">{{ note }}</li>
                    </ul>
                </div>

                <div>
                    <p class="footer-label">Next Milestones</p>
                    <ul class="footer-list">
                        <li v-for="milestone in page.props.site.footer.milestones" :key="milestone">{{ milestone }}</li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
</template>
