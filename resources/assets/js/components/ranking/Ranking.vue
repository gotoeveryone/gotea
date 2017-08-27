<template>
    <section class="ranking">
        <ranking-header :lastUpdate="lastUpdate" @search="onSearch" @json="outputJson"></ranking-header>
        <ranking-items :items="items"></ranking-items>
    </section>
</template>

<script>
import Header from './Header.vue';
import Items from './Items.vue';
import { WEB_ROOT } from '../../common';

export default {
    data: () => {
        return {
            lastUpdate: null,
            items: [],
        }
    },
    components: {
        rankingHeader: Header,
        rankingItems: Items,
    },
    methods: {
        onSearch(_params) {
            const url = `${WEB_ROOT}api/rankings/${_params.country}/${_params.year}/${_params.limit}`;
            this.$http.get(url, { params: {'withJa': '1'} }
                ).then(res => {
                    const json = res.body.response;
                    const dateObj = new Date(json.lastUpdate);
                    this.lastUpdate = `${dateObj.getFullYear()}年${(dateObj.getMonth() + 1)}月${dateObj.getDate()}日`;
                    this.items = json.ranking;
                });
        },
        outputJson(_params) {
            const url = `${WEB_ROOT}api/rankings/${_params.country}/${_params.year}/${_params.limit}`;
            this.$http.get(url, { params: {'make': '1'} })
                .then(res => {
                    this.openDialog('JSONを出力しました。');
                });
        },
    },
}
</script>
