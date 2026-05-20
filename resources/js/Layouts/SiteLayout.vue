<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import WhatsappFloat from '../Components/WhatsappFloat.vue';

const page = usePage();
const logoutForm = useForm({});
const headerRef = ref(null);
const mobileNavOpen = ref(false);
let revealObserver;

function phoneHref(value) {
    return `tel:${String(value).replace(/[^\d+]/g, '')}`;
}

function whatsappHref(value) {
    const number = value ? String(value).replace(/\D/g, '') : '';
    if (!number) {
        return null;
    }

    return `https://wa.me/${number}?text=${encodeURIComponent('Hi Acute Tourism, I would like to plan a trip.')}`;
}

function initRevealObserver() {
    revealObserver?.disconnect();
    document.documentElement.classList.add('has-motion');

    const nodes = document.querySelectorAll('[data-reveal]');
    revealObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }
                entry.target.classList.add('is-visible');
                revealObserver?.unobserve(entry.target);
            });
        },
        { threshold: 0.14, rootMargin: '0px 0px -8% 0px' },
    );

    nodes.forEach((node) => {
        const rect = node.getBoundingClientRect();
        if (rect.top < window.innerHeight * 0.92) {
            node.classList.add('is-visible');
            return;
        }
        revealObserver.observe(node);
    });
}

function closeNavDropdowns() {
    headerRef.value?.querySelectorAll('details.nav-group[open]').forEach((el) => {
        el.removeAttribute('open');
    });
}

function closeMobileNav() {
    mobileNavOpen.value = false;
}

function toggleMobileNav() {
    mobileNavOpen.value = !mobileNavOpen.value;
    if (!mobileNavOpen.value) {
        closeNavDropdowns();
    }
}

/** Keep only one flyout open; native toggle runs before this (toggle fires after state change). */
function onNavGroupToggle(event) {
    const el = event.target;
    if (!(el instanceof HTMLDetailsElement) || !el.classList.contains('nav-group')) {
        return;
    }
    if (!el.open) {
        return;
    }
    headerRef.value?.querySelectorAll('details.nav-group').forEach((d) => {
        if (d !== el) {
            d.removeAttribute('open');
        }
    });
}

function onDocumentPointerDown(event) {
    const target = event.target;
    if (!(target instanceof Node) || !headerRef.value) {
        return;
    }
    if (!headerRef.value.contains(target)) {
        closeNavDropdowns();
        closeMobileNav();
        return;
    }
    if (!target.closest('details.nav-group')) {
        closeNavDropdowns();
    }
}

function onWindowScroll() {
    closeNavDropdowns();
}

function onDocumentKeydown(event) {
    if (event.key === 'Escape') {
        closeNavDropdowns();
        closeMobileNav();
    }
}

onMounted(() => {
    document.addEventListener('pointerdown', onDocumentPointerDown, true);
    document.addEventListener('keydown', onDocumentKeydown);
    window.addEventListener('scroll', onWindowScroll, { passive: true });
    initRevealObserver();
});

watch(
    () => page.url,
    async () => {
        await nextTick();
        initRevealObserver();
    },
);

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', onDocumentPointerDown, true);
    document.removeEventListener('keydown', onDocumentKeydown);
    window.removeEventListener('scroll', onWindowScroll);
    revealObserver?.disconnect();
});

</script>

<template>
    <div class="site-shell">
        <div class="ambient ambient-one"></div>
        <div class="ambient ambient-two"></div>

        <header ref="headerRef" class="site-header">
            <div class="container nav-row">
                <Link class="brand-mark brand-mark-header" :href="page.props.site.homeUrl" aria-label="Home">
                    <img
                        v-if="page.props.site.logoUrl"
                        class="brand-logo"
                        :src="page.props.site.logoUrl"
                        alt=""
                    />
                </Link>

                <button
                    class="mobile-nav-toggle"
                    type="button"
                    :aria-expanded="mobileNavOpen ? 'true' : 'false'"
                    aria-controls="site-primary-nav"
                    @click="toggleMobileNav"
                >
                    <span>{{ mobileNavOpen ? 'Close' : 'Menu' }}</span>
                </button>

                <div class="site-header-panel" :class="{ 'is-open': mobileNavOpen }">
                    <nav id="site-primary-nav" class="primary-nav" aria-label="Main">
                        <template v-for="item in page.props.site.primaryNavigation" :key="item.label">
                            <details v-if="item.children" class="nav-group" @toggle="onNavGroupToggle">
                                <summary class="nav-group-trigger">
                                    {{ item.label }}
                                    <svg class="nav-group-chevron" width="11" height="11" viewBox="0 0 12 12" aria-hidden="true">
                                        <path
                                            d="M2.5 4.5 6 8 9.5 4.5"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.35"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </summary>
                                <ul class="nav-group-list">
                                    <li v-for="child in item.children" :key="child.href">
                                        <Link class="nav-group-link" :href="child.href" @click="closeNavDropdowns(); closeMobileNav()">{{
                                            child.label
                                        }}</Link>
                                    </li>
                                </ul>
                            </details>
                            <Link v-else class="nav-link" :href="item.href" @click="closeMobileNav">{{ item.label }}</Link>
                        </template>
                    </nav>

                    <nav class="mobile-flat-nav" aria-label="Mobile main">
                        <Link
                            v-for="item in page.props.site.mobileNavigation"
                            :key="item.href"
                            class="mobile-flat-nav__link"
                            :href="item.href"
                            @click="closeMobileNav"
                        >
                            {{ item.label }}
                        </Link>
                    </nav>

                    <div class="header-actions">
                        <Link class="header-cart-link" :href="page.props.cart.url" @click="closeMobileNav">
                            Cart
                            <span v-if="page.props.cart.count" class="header-cart-link__count">{{ page.props.cart.count }}</span>
                        </Link>
                        <Link v-if="page.props.auth.user" class="header-cta" :href="page.props.auth.user.dashboardUrl" @click="closeMobileNav">My Account</Link>
                        <Link v-else class="header-cta" href="/login" @click="closeMobileNav">Login</Link>
                        <button
                            v-if="page.props.auth.user"
                            class="button-secondary header-logout"
                            @click="logoutForm.post('/logout')"
                        >
                            Logout
                        </button>
                    </div>
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
                        <img
                            v-if="page.props.site.footerLogoUrl"
                            class="brand-logo footer-logo"
                            :src="page.props.site.footerLogoUrl"
                            alt=""
                        />
                    </div>
                    <p class="footer-copy">{{ page.props.site.footer.description }}</p>
                </div>

                <details class="footer-panel" open>
                    <summary class="footer-label">Explore</summary>
                    <ul class="footer-list">
                        <li v-for="item in page.props.site.footerNavigation" :key="item.href">
                            <Link class="footer-link" :href="item.href">{{ item.label }}</Link>
                        </li>
                    </ul>
                </details>

                <details class="footer-panel" open>
                    <summary class="footer-label">Contact</summary>
                    <ul class="footer-list">
                        <li>
                            <a class="footer-link" :href="`mailto:${page.props.site.contact.email}`">
                                {{ page.props.site.contact.email }}
                            </a>
                        </li>
                        <li v-if="page.props.site.contact.phone">
                            <a class="footer-link" :href="phoneHref(page.props.site.contact.phone)">
                                {{ page.props.site.contact.phone }}
                            </a>
                        </li>
                        <li v-if="page.props.site.contact.phoneSecondary">
                            <a
                                class="footer-link"
                                :href="phoneHref(page.props.site.contact.phoneSecondary)"
                            >
                                {{ page.props.site.contact.phoneSecondary }}
                            </a>
                        </li>
                        <li v-if="whatsappHref(page.props.site.contact.whatsappNumber)">
                            <a class="footer-link" :href="whatsappHref(page.props.site.contact.whatsappNumber)" target="_blank" rel="noreferrer">
                                WhatsApp {{ page.props.site.contact.whatsappNumber }}
                            </a>
                        </li>
                        <li>{{ page.props.site.contact.address }}</li>
                    </ul>
                </details>

                <details class="footer-panel" open>
                    <summary class="footer-label">Connect</summary>
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
                </details>
            </div>

            <div class="container footer-bottom">
                <span>{{ page.props.site.footer.legalName }}</span>
                <span>{{ page.props.site.trust.licenseText }}</span>
                <span>{{ page.props.site.trust.responseTime }}</span>
            </div>
        </footer>

        <WhatsappFloat />
    </div>
</template>
