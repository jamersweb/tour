<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import WhatsappFloat from '../Components/WhatsappFloat.vue';

const page = usePage();
const logoutForm = useForm({});
const headerRef = ref(null);

function closeNavDropdowns() {
    headerRef.value?.querySelectorAll('details.nav-group[open]').forEach((el) => {
        el.removeAttribute('open');
    });
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
    }
}

onMounted(() => {
    document.addEventListener('pointerdown', onDocumentPointerDown, true);
    document.addEventListener('keydown', onDocumentKeydown);
    window.addEventListener('scroll', onWindowScroll, { passive: true });
});

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', onDocumentPointerDown, true);
    document.removeEventListener('keydown', onDocumentKeydown);
    window.removeEventListener('scroll', onWindowScroll);
});

const footerWhatsappHref = computed(() => {
    const raw = page.props.site?.contact?.whatsappNumber;
    const number = raw ? String(raw).replace(/[^0-9]/g, '') : '';

    return number ? `https://wa.me/${number}` : null;
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

                <nav class="primary-nav" aria-label="Main">
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
                                    <Link class="nav-group-link" :href="child.href" @click="closeNavDropdowns">{{
                                        child.label
                                    }}</Link>
                                </li>
                            </ul>
                        </details>
                        <Link v-else class="nav-link" :href="item.href">{{ item.label }}</Link>
                    </template>
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
                        <img
                            v-if="page.props.site.footerLogoUrl"
                            class="brand-logo footer-logo"
                            :src="page.props.site.footerLogoUrl"
                            alt=""
                        />
                    </div>
                    <p class="footer-copy">{{ page.props.site.footer.description }}</p>
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
                        <li v-if="page.props.site.contact.phone">
                            <a class="footer-link" :href="`tel:${String(page.props.site.contact.phone).replace(/[^\d+]/g, '')}`">
                                {{ page.props.site.contact.phone }}
                            </a>
                        </li>
                        <li v-if="page.props.site.contact.phoneSecondary">
                            <a
                                class="footer-link"
                                :href="`tel:${String(page.props.site.contact.phoneSecondary).replace(/[^\d+]/g, '')}`"
                            >
                                {{ page.props.site.contact.phoneSecondary }}
                            </a>
                        </li>
                        <li v-if="footerWhatsappHref">
                            <a class="footer-link" :href="footerWhatsappHref" target="_blank" rel="noreferrer">
                                WhatsApp
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

        <WhatsappFloat />
    </div>
</template>
