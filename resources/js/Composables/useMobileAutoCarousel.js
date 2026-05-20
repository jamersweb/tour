import { nextTick, onMounted, onUnmounted, ref } from 'vue';

export function useMobileAutoCarousel({ breakpoint = 640, intervalMs = 3200 } = {}) {
    const carouselRef = ref(null);
    let timer = null;
    let mediaQuery = null;
    let userPausedUntil = 0;

    const stop = () => {
        if (timer) {
            window.clearInterval(timer);
            timer = null;
        }
    };

    const scrollNext = () => {
        const el = carouselRef.value;
        if (!el || !mediaQuery?.matches || Date.now() < userPausedUntil) {
            return;
        }

        const cards = Array.from(el.children);
        const firstCard = cards[0];
        if (cards.length < 2 || !firstCard || el.scrollWidth <= el.clientWidth) {
            return;
        }

        const styles = window.getComputedStyle(el);
        const gap = Number.parseFloat(styles.columnGap || styles.gap || '0') || 0;
        const step = firstCard.getBoundingClientRect().width + gap;
        const maxScroll = el.scrollWidth - el.clientWidth;
        const nextLeft = el.scrollLeft + step >= maxScroll - 8 ? 0 : el.scrollLeft + step;

        el.scrollTo({ left: nextLeft, behavior: 'smooth' });
    };

    const start = () => {
        stop();
        if (!mediaQuery?.matches) {
            return;
        }
        timer = window.setInterval(scrollNext, intervalMs);
    };

    const pauseForUser = () => {
        userPausedUntil = Date.now() + intervalMs * 2;
    };

    onMounted(async () => {
        await nextTick();

        mediaQuery = window.matchMedia(`(max-width: ${breakpoint}px)`);
        mediaQuery.addEventListener('change', start);
        carouselRef.value?.addEventListener('pointerdown', pauseForUser, { passive: true });
        carouselRef.value?.addEventListener('touchstart', pauseForUser, { passive: true });
        start();
    });

    onUnmounted(() => {
        stop();
        mediaQuery?.removeEventListener('change', start);
        carouselRef.value?.removeEventListener('pointerdown', pauseForUser);
        carouselRef.value?.removeEventListener('touchstart', pauseForUser);
    });

    return carouselRef;
}
