<template>
    <section class="categories">
        <ranks-header @search="onSearch"></ranks-header>
        <ranks-items :items="items"></ranks-items>
    </section>
</template>

<script lang="ts">
import Vue from 'vue'
import axios from 'axios'

import Header from '@/components/ranks/Header.vue';
import Items from '@/components/ranks/Items.vue';

export default Vue.extend({
    data: () => {
        return {
            items: Array(),
        }
    },
    components: {
        ranksHeader: Header,
        ranksItems: Items,
    },
    methods: {
        onSearch(_params: any) {
            axios.get(`/api/players/ranks/${_params.country}`)
                .then(res => this.items = res.data.response)
        },
    },
})
</script>
