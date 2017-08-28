<template>
    <section class="categories">
        <ranks-header :domain="domain" @search="onSearch"></ranks-header>
        <ranks-items :items="items"></ranks-items>
    </section>
</template>

<script>
import Header from './Header.vue';
import Items from './Items.vue';

export default {
    props: {
        domain: String,
    },
    data: () => {
        return {
            items: [],
        }
    },
    components: {
        ranksHeader: Header,
        ranksItems: Items,
    },
    methods: {
        onSearch(_params) {
            const url = `${this.domain}api/ranks/${_params.country}`;
            this.$http.get(url).then(res => {
                this.items = res.body.response;
            });
        },
    },
}
</script>
