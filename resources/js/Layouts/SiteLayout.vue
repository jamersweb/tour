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
                <Link class="brand-mark" :href="page.props.site.homeUrl">
                    <div class="brand-lockup">
                        <img
                            v-if="page.props.site.logoUrl"
                            class="brand-logo"
                            :src="page.props.site.logoUrl"
                            :alt="page.props.site.name"
                        />
                        <div>
                            <span class="brand-kicker">{{ page.props.site.brandKicker }}</span>
                            <span class="brand-name">{{ page.props.site.brandName }}</span>
                        </div>
                    </div>
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
                    <div class="footer-brand">
                        <div class="brand-lockup footer-lockup">
                            <img
                                v-if="page.props.site.logoUrl"
                                class="brand-logo footer-logo"
                                :src="page.props.site.logoUrl"
                                :alt="page.props.site.name"
                            />
                            <div>
                                <span class="brand-kicker">{{ page.props.site.brandKicker }}</span>
                                <span class="brand-name">{{ page.props.site.brandName }}</span>
                            </div>
                        </div>
                    </div>
                    <p class="footer-copy">{{ page.props.site.footer.description }}</p>
                    <p class="footer-meta">{{ page.props.site.footer.legalName }}</p>
                </div>

                <div>
                    <p class="footer-label">Explore</p>
                    <ul class="footer-list">
                        <li v-for="item in page.props.site.footerNavigation" :key="item.href">
                            <Link class="footer-link" :href="item.href">{{ item.label }}</Link>
                        </li>
                    </ul>
                </div>

                <div>
                    <p class="footer-label">Contact</p>
                    <ul class="footer-list">
                        <li>
                            <a class="footer-link" :href="`mailto:${page.props.site.contact.email}`">
                                {{ page.props.site.contact.email }}
                            </a>
                        </li>
                        <li>
                            <a class="footer-link" :href="`tel:${page.props.site.contact.phone}`">
                                {{ page.props.site.contact.phone }}
                            </a>
                        </li>
                        <li>{{ page.props.site.contact.address }}</li>
                    </ul>
                </div>

                <div>
                    <p class="footer-label">Connect</p>
                    <ul class="footer-list">
                        <li v-for="link in page.props.site.footer.socialLinks" :key="link.href">
                            <a class="footer-link" :href="link.href" target="_blank" rel="noreferrer">
                                {{ link.label }}
                            </a>
                        </li>
                        <li>
                            <a class="footer-link" :href="page.props.site.footer.website" target="_blank" rel="noreferrer">
                                Official Website
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
</template>
