<script setup>
import { reactive, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import SiteMeta from '../../Components/SiteMeta.vue';
import SiteLayout from '../../Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    cart: Object,
});

const page = usePage();
const itemForms = reactive({});

function hydrateForms() {
    Object.keys(itemForms).forEach((key) => delete itemForms[key]);
    (props.cart.items || []).forEach((item) => {
        itemForms[item.key] = {
            guest_count: item.guestCount,
            travel_date: item.travelDate || '',
        };
    });

}

hydrateForms();

watch(() => props.cart.items, hydrateForms, { deep: true });

function updateItem(item) {
    router.patch(`/cart/${encodeURIComponent(item.key)}`, itemForms[item.key], {
        preserveScroll: true,
    });
}

function removeItem(item) {
    router.delete(`/cart/${encodeURIComponent(item.key)}`, {
        preserveScroll: true,
    });
}

function clearCart() {
    router.delete('/cart', {
        preserveScroll: true,
    });
}
</script>

<template>
    <SiteMeta :title="seo.title" :description="seo.description" />

    <div class="cart-page">
        <section class="about-hero cart-hero">
            <div class="container about-hero__grid">
                <div>
                    <p class="about-kicker">Cart</p>
                    <h1 class="about-title">Review your trip selections before checkout.</h1>
                    <p class="about-copy">
                        Adjust guest count and travel date here, then continue to the secure checkout for the item you
                        want to confirm.
                    </p>
                </div>

                <article class="about-card about-card--primary">
                    <p class="about-card__label">Cart summary</p>
                    <h2>{{ cart.items.length }} {{ cart.items.length === 1 ? 'item' : 'items' }}</h2>
                    <p>{{ cart.subtotal }} estimated total before final availability checks.</p>
                </article>
            </div>
        </section>

        <section class="section-block cart-section">
            <div class="container">
                <div v-if="page.props.flash.success" class="success-banner">
                    {{ page.props.flash.success }}
                </div>
                <div v-if="page.props.flash.error" class="error-banner">
                    {{ page.props.flash.error }}
                </div>

                <div v-if="cart.items.length" class="cart-layout">
                    <div class="cart-items">
                        <article v-for="item in cart.items" :key="item.key" class="cart-item">
                            <div v-if="item.image" class="cart-item__media">
                                <img :src="item.image" :alt="item.title" />
                            </div>

                            <div class="cart-item__body">
                                <p class="card-tag">{{ item.label }}</p>
                                <h2>{{ item.title }}</h2>
                                <p>{{ item.summary }}</p>

                                <div class="cart-item__meta">
                                    <span v-if="item.duration">{{ item.duration }}</span>
                                    <span v-if="item.location">{{ item.location }}</span>
                                    <span>{{ item.unitAmount }} per guest</span>
                                </div>

                                <div class="cart-item__controls">
                                    <label class="field">
                                        <span>Travel Date</span>
                                        <input v-model="itemForms[item.key].travel_date" type="date" />
                                    </label>
                                    <label class="field">
                                        <span>Guests</span>
                                        <input v-model="itemForms[item.key].guest_count" type="number" min="1" max="100" />
                                    </label>
                                </div>

                                <div class="cart-item__actions">
                                    <button class="button-secondary" type="button" @click="updateItem(item)">Update</button>
                                    <Link class="button-secondary" :href="item.detailUrl">View details</Link>
                                    <button class="cart-remove-button" type="button" @click="removeItem(item)">Remove</button>
                                </div>
                            </div>

                            <div class="cart-item__total">
                                <span>Line total</span>
                                <strong>{{ item.lineTotal }}</strong>
                            </div>
                        </article>
                    </div>

                    <aside class="cart-summary-card">
                        <p class="about-card__label">Estimated total</p>
                        <h2>{{ cart.subtotal }}</h2>
                        <p>
                            Checkout includes every item currently in your cart. Update dates or guest counts first, then continue.
                        </p>
                        <a class="button-primary cart-summary-checkout" :href="cart.checkoutUrl">
                            Checkout
                        </a>
                        <button class="button-secondary" type="button" @click="clearCart">Clear cart</button>
                    </aside>
                </div>

                <article v-else class="about-card cart-empty-card">
                    <p class="about-card__label">Your cart is empty</p>
                    <h2>Start with an experience or package.</h2>
                    <p>Add something to your cart, then return here to review guest count and travel date.</p>
                    <div class="about-actions">
                        <Link class="button-primary" href="/experiences">Browse experiences</Link>
                        <Link class="button-secondary" href="/packages">Browse packages</Link>
                    </div>
                </article>
            </div>
        </section>

        <div v-if="cart.items.length" class="cart-bottom-checkout">
            <div>
                <strong>{{ cart.subtotal }}</strong>
                <span>{{ cart.items.length }} {{ cart.items.length === 1 ? 'item' : 'items' }} in cart</span>
            </div>
            <a class="button-primary" :href="cart.checkoutUrl">Checkout</a>
        </div>
    </div>
</template>
