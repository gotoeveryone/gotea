<template>
    <section class="categories">
        <ranks-header @search="onSearch"></ranks-header>
        <ranks-items :items="items"></ranks-items>
    </section>
</template>

<script>
import Header from './Header.vue';
import Items from './Items.vue';

export default {
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
            this.$http.get(`/api/ranks/${_params.country}`).then(res => {
                this.items = res.body.response;
            });
        },
    },
}
</script>
