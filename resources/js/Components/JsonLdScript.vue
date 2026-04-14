<script>
import { h } from 'vue';

/** Renders JSON-LD; plain <script> in templates is ignored by Vue's client compiler. */
export default {
    name: 'JsonLdScript',
    props: {
        json: {
            type: String,
            required: true,
        },
    },
    render() {
        return h('script', { type: 'application/ld+json' }, this.json);
    },
};
</script>
