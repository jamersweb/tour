<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import WhatsappFloat from '../Components/WhatsappFloat.vue';

const page = usePage();
const logoutForm = useForm({});
const headerRef = ref(null);
const footerRef = ref(null);
const mobileNavOpen = ref(false);
const footerShortDescription = 'Curated Dubai tours, holidays, visas, and travel support.';
const showMobileBottomNav = computed(() => {
    const currentPath = String(page.url || '/').split('?')[0].replace(/\/+$/, '') || '/';

    return currentPath === '/';
});
const partnerUrl = computed(() => page.props.site?.footerNavigation?.find((item) => item.label === 'Earn With Tourgrat')?.href || '/earn-with-tourgrat');
const paymentIcons = [
    { label: 'Cards', image: '/images/payment-card.svg' },
    { label: 'Wallet', image: '/images/payment-wallet.svg' },
    { label: 'Secure payment', image: '/images/payment-secure.svg' },
];
const footerExploreLinks = computed(() => [
    { label: 'Tours & Tickets', href: '/dubai-tours-and-tickets' },
    { label: 'Holiday Packages', href: '/dubai-holiday-packages' },
    { label: 'Visa Services', href: '/tourist-visa-assistance-uae-residents' },
    { label: 'Panoramic Bus', href: '/luxury-bus-tour-dubai' },
    { label: 'Corporate Events', href: '/corporate-travel-event-planning-dubai' },
]);
const footerSupportLinks = computed(() => [
    { label: 'Contact Us', href: '/contact' },
    { label: 'Terms & Conditions', href: '/terms-and-conditions' },
    { label: 'Privacy Policy', href: '/privacy-policy' },
    { label: 'Cancellation & Refund Policy', href: '/cancellation-policy' },
    { label: 'FAQs', href: '/faq' },
]);
const footerCompanyLinks = computed(() => [
    { label: 'About Us', href: '/about' },
    { label: 'Earn With Tourgrat', href: partnerUrl.value },
    { label: 'Blog', href: '/blog' },
]);
let revealObserver;
let footerMediaQuery;

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

function footerSocialIconPath(label) {
    const key = String(label || '').toLowerCase();

    if (key.includes('instagram')) {
        return 'M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4Zm5 5.2A3.8 3.8 0 1 0 12 15.8 3.8 3.8 0 0 0 12 8.2Zm5.2-1.1h.01';
    }

    if (key.includes('facebook')) {
        return 'M14 8h2V4h-2a5 5 0 0 0-5 5v2H7v4h2v6h4v-6h2.5l.5-4h-3V9a1 1 0 0 1 1-1Z';
    }

    if (key.includes('tiktok')) {
        return 'M14 4v9.1a4.1 4.1 0 1 1-4.1-4.1M14 4c.4 2.7 2.2 4.4 5 4.6';
    }

    if (key.includes('linkedin')) {
        return 'M6.5 10v8M6.5 6v.01M11 18v-8m0 3.4c0-2 1.3-3.4 3.2-3.4 2.1 0 3.3 1.4 3.3 3.8V18';
    }

    return 'M12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm0 0c2.1 2.2 3.2 4.9 3.2 8S14.1 17.8 12 20m0-16C9.9 6.2 8.8 8.9 8.8 12s1.1 5.8 3.2 8M4.7 9h14.6M4.7 15h14.6';
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

function closeMobileNavGroups() {
    headerRef.value?.querySelectorAll('details.mobile-flat-nav__group[open]').forEach((el) => {
        el.removeAttribute('open');
    });
}

function closeMobileNav() {
    mobileNavOpen.value = false;
    closeMobileNavGroups();
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

function isFooterAccordionMobile() {
    return footerMediaQuery?.matches ?? window.matchMedia('(max-width: 960px)').matches;
}

function setFooterPanelsForViewport() {
    const panels = footerRef.value?.querySelectorAll('details.footer-panel');
    if (!panels) {
        return;
    }

    panels.forEach((panel) => {
        if (isFooterAccordionMobile()) {
            panel.removeAttribute('open');
            return;
        }

        panel.setAttribute('open', '');
    });
}

function onFooterPanelToggle(event) {
    const el = event.target;
    if (!(el instanceof HTMLDetailsElement) || !el.classList.contains('footer-panel')) {
        return;
    }
    if (!isFooterAccordionMobile() || !el.open) {
        return;
    }

    footerRef.value?.querySelectorAll('details.footer-panel').forEach((panel) => {
        if (panel !== el) {
            panel.removeAttribute('open');
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
    footerMediaQuery = window.matchMedia('(max-width: 960px)');
    footerMediaQuery.addEventListener('change', setFooterPanelsForViewport);
    setFooterPanelsForViewport();
    initRevealObserver();
});

watch(
    () => page.url,
    async () => {
        await nextTick();
        setFooterPanelsForViewport();
        initRevealObserver();
    },
);

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', onDocumentPointerDown, true);
    document.removeEventListener('keydown', onDocumentKeydown);
    window.removeEventListener('scroll', onWindowScroll);
    footerMediaQuery?.removeEventListener('change', setFooterPanelsForViewport);
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
                        width="160"
                        height="70"
                        fetchpriority="high"
                        decoding="async"
                    />
                </Link>

                <div class="mobile-header-tools" aria-label="Mobile quick actions">
                    <Link
                        v-if="page.props.auth.user"
                        class="header-login-link"
                        :href="page.props.auth.user.dashboardUrl"
                        aria-label="My Account"
                        @click="closeMobileNav"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </Link>
                    <Link v-else class="header-login-link" href="/login" aria-label="Log in" @click="closeMobileNav">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </Link>

                    <Link class="header-cart-link" :href="page.props.cart.url" aria-label="Cart" @click="closeMobileNav">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 6h2l1.1 8.2a2 2 0 0 0 2 1.8h6.5a2 2 0 0 0 1.9-1.4L20 9H8"></path>
                            <path d="M10 20h.01"></path>
                            <path d="M17 20h.01"></path>
                        </svg>
                        <span v-if="page.props.cart.count" class="header-cart-link__count">{{ page.props.cart.count }}</span>
                    </Link>

                    <button
                        class="mobile-nav-toggle"
                        type="button"
                        :aria-label="mobileNavOpen ? 'Close menu' : 'Open menu'"
                        :aria-expanded="mobileNavOpen ? 'true' : 'false'"
                        aria-controls="site-primary-nav"
                        @click="toggleMobileNav"
                    >
                        <span class="mobile-nav-toggle__label">Menu</span>
                        <span class="mobile-nav-toggle__icon" :class="{ 'is-open': mobileNavOpen }" aria-hidden="true">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </button>
                </div>

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
                                        <template v-if="child.children">
                                            <span class="nav-group-heading">{{ child.label }}</span>
                                            <ul class="nav-group-sublist">
                                                <li v-for="grandchild in child.children" :key="grandchild.href">
                                                    <Link class="nav-group-link nav-group-link--child" :href="grandchild.href" @click="closeNavDropdowns(); closeMobileNav()">
                                                        {{ grandchild.label }}
                                                    </Link>
                                                </li>
                                            </ul>
                                        </template>
                                        <Link v-else class="nav-group-link" :href="child.href" @click="closeNavDropdowns(); closeMobileNav()">{{
                                            child.label
                                        }}</Link>
                                    </li>
                                </ul>
                            </details>
                            <Link v-else class="nav-link" :href="item.href" @click="closeMobileNav">{{ item.label }}</Link>
                        </template>
                    </nav>

                    <nav class="mobile-flat-nav" aria-label="Mobile main">
                        <template v-for="item in page.props.site.mobileNavigation" :key="item.label">
                            <details v-if="item.children" class="mobile-flat-nav__group">
                                <summary class="mobile-flat-nav__link mobile-flat-nav__summary">
                                    {{ item.label }}
                                    <span class="mobile-flat-nav__chevron" aria-hidden="true"></span>
                                </summary>
                                <div class="mobile-flat-nav__children">
                                    <template v-for="child in item.children" :key="child.href || child.label">
                                        <details v-if="child.children" class="mobile-flat-nav__subgroup">
                                            <summary class="mobile-flat-nav__child mobile-flat-nav__child--summary">
                                                {{ child.label }}
                                                <span class="mobile-flat-nav__chevron" aria-hidden="true"></span>
                                            </summary>
                                            <div class="mobile-flat-nav__grandchildren">
                                                <Link
                                                    v-for="grandchild in child.children"
                                                    :key="grandchild.href"
                                                    class="mobile-flat-nav__grandchild"
                                                    :href="grandchild.href"
                                                    @click="closeMobileNav"
                                                >
                                                    {{ grandchild.label }}
                                                </Link>
                                            </div>
                                        </details>
                                        <Link
                                            v-else
                                            class="mobile-flat-nav__child"
                                            :href="child.href"
                                            @click="closeMobileNav"
                                        >
                                            {{ child.label }}
                                        </Link>
                                    </template>
                                </div>
                            </details>
                            <Link
                                v-else
                                class="mobile-flat-nav__link"
                                :href="item.href"
                                @click="closeMobileNav"
                            >
                                {{ item.label }}
                            </Link>
                        </template>
                    </nav>

                    <div class="header-actions">
                        <Link
                            class="header-partner-link"
                            :href="partnerUrl"
                            @click="closeMobileNav"
                        >
                            Earn With Tourgrat
                        </Link>
                        <Link class="header-cart-link" :href="page.props.cart.url" aria-label="Cart" @click="closeMobileNav">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M5 6h2l1.1 8.2a2 2 0 0 0 2 1.8h6.5a2 2 0 0 0 1.9-1.4L20 9H8"></path>
                                <path d="M10 20h.01"></path>
                                <path d="M17 20h.01"></path>
                            </svg>
                            <span v-if="page.props.cart.count" class="header-cart-link__count">{{ page.props.cart.count }}</span>
                        </Link>
                        <Link
                            v-if="page.props.auth.user"
                            class="header-login-link"
                            :href="page.props.auth.user.dashboardUrl"
                            aria-label="My Account"
                            @click="closeMobileNav"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </Link>
                        <Link
                            v-else
                            class="header-login-link"
                            href="/login"
                            aria-label="Log in"
                            @click="closeMobileNav"
                        >
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </Link>
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

        <footer ref="footerRef" class="site-footer">
            <div class="container footer-grid footer-grid--columns footer-grid--branded">
                <div class="footer-brand-column">
                    <div class="footer-brand">
                        <img
                            v-if="page.props.site.footerLogoUrl"
                            class="brand-logo footer-logo"
                            :src="page.props.site.footerLogoUrl"
                            alt=""
                            width="160"
                            height="70"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>
                    <p class="footer-copy">{{ footerShortDescription }}</p>
                    <div class="footer-tourgrat">
                        <strong>Tourgrat</strong>
                        <span>Acute Tourism's referrer platform Tourgrat lets referrers share Acute Tourism tours and earn rewards on verified bookings.</span>
                    </div>
                </div>

                <details class="footer-panel footer-panel--explore" open @toggle="onFooterPanelToggle">
                    <summary class="footer-label">Explore</summary>
                    <ul class="footer-list">
                        <li v-for="item in footerExploreLinks" :key="item.href">
                            <a
                                v-if="String(item.href).startsWith('http')"
                                class="footer-link"
                                :href="item.href"
                                target="_blank"
                                rel="noreferrer"
                            >
                                {{ item.label }}
                            </a>
                            <Link v-else class="footer-link" :href="item.href">{{ item.label }}</Link>
                        </li>
                    </ul>
                </details>

                <details class="footer-panel footer-panel--support" open @toggle="onFooterPanelToggle">
                    <summary class="footer-label">Support</summary>
                    <ul class="footer-list">
                        <li v-for="item in footerSupportLinks" :key="item.href">
                            <a
                                v-if="String(item.href).startsWith('http')"
                                class="footer-link"
                                :href="item.href"
                                target="_blank"
                                rel="noreferrer"
                            >
                                {{ item.label }}
                            </a>
                            <Link v-else class="footer-link" :href="item.href">{{ item.label }}</Link>
                        </li>
                    </ul>
                </details>

                <div class="footer-work-column">
                    <details class="footer-panel footer-panel--company" open @toggle="onFooterPanelToggle">
                        <summary class="footer-label">Company</summary>
                        <ul class="footer-list">
                            <li v-for="item in footerCompanyLinks" :key="`${item.label}-${item.href}`">
                                <a
                                    v-if="String(item.href).startsWith('http')"
                                    class="footer-link"
                                    :href="item.href"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    {{ item.label }}
                                </a>
                                <Link v-else class="footer-link" :href="item.href">{{ item.label }}</Link>
                            </li>
                        </ul>
                    </details>
                    <div class="footer-payments-block">
                        <p class="footer-label">Ways You Can Pay</p>
                        <div class="footer-payment-icons" aria-label="Payment support">
                            <span
                                v-for="item in paymentIcons"
                                :key="item.label"
                                :title="item.label"
                                :aria-label="item.label"
                            >
                                <img :src="item.image" :alt="item.label" width="64" height="64" loading="lazy" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container footer-bottom footer-bottom--columns">
                <div class="footer-bottom-copy">
                    <span>{{ page.props.site.footer.legalName }}</span>
                    <span>{{ page.props.site.trust.licenseText }}</span>
                    <span>{{ page.props.site.trust.responseTime }}</span>
                </div>
                <div class="footer-social-icons" aria-label="Social links">
                    <a
                        v-for="link in page.props.site.footer.socialLinks"
                        :key="`icon-${link.href}`"
                        :href="link.href"
                        target="_blank"
                        rel="noreferrer"
                        :aria-label="link.label"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path :d="footerSocialIconPath(link.label)"></path>
                        </svg>
                    </a>
                    <a
                        :href="page.props.site.footer.website"
                        target="_blank"
                        rel="noreferrer"
                        aria-label="Official Website"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path :d="footerSocialIconPath('website')"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </footer>

        <nav v-if="showMobileBottomNav" class="mobile-bottom-nav" aria-label="Mobile quick navigation">
            <Link class="mobile-bottom-nav__item" href="/dubai-tours-and-tickets">
                <span class="mobile-bottom-nav__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M4 7h16"></path>
                        <path d="M6 7v12"></path>
                        <path d="M18 7v12"></path>
                        <path d="M6 19h12"></path>
                        <path d="M9 7V5h6v2"></path>
                    </svg>
                </span>
                <span class="mobile-bottom-nav__label">Tours &amp; Tickets</span>
            </Link>
            <Link class="mobile-bottom-nav__item" href="/dubai-holiday-packages">
                <span class="mobile-bottom-nav__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M4 8h16v11H4z"></path>
                        <path d="M8 8V6a4 4 0 0 1 8 0v2"></path>
                        <path d="M4 13h16"></path>
                    </svg>
                </span>
                <span class="mobile-bottom-nav__label">Holiday Packages</span>
            </Link>
            <Link class="mobile-bottom-nav__item" href="/tourist-visa-assistance-uae-residents">
                <span class="mobile-bottom-nav__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M7 3h7l4 4v14H7z"></path>
                        <path d="M14 3v5h5"></path>
                        <path d="M9 13h6"></path>
                        <path d="M9 17h4"></path>
                    </svg>
                </span>
                <span class="mobile-bottom-nav__label">Visas</span>
            </Link>
        </nav>

        <WhatsappFloat />
    </div>
</template>
