<template>
    <section class="titles">
        <title-header @add="addRow" @search="onSearch" @json="outputJson"></title-header>
        <title-items :items="items"></title-items>
    </section>
</template>

<script>
import Header from './Header.vue';
import Items from './Items.vue';
import { WEB_ROOT } from '../../common';

export default {
    props: {
        root: String,
        lastUpdate: String,
    },
    data: () => {
        return {
            domain: '',
            items: [],
            countries: [],
            select: {
                year: null,
                country: null,
                limit: null,
            },
        }
    },
    components: {
        titleHeader: Header,
        titleItems: Items,
    },
    methods: {
        onSearch(_params) {
            const params = {
                'admin': '1',
                'withJa': '1',
                'country_id': _params.country,
                'is_closed': _params.type,
            };

            this.$http.get(`${WEB_ROOT}api/news/`, { params: params })
                .then(res => {
                    this.items = res.body.response;
                });
        },
        addRow() {
            this.items.push({
                countryId: this.country,
                sortOrder: this.items.length,
            });
        },
        outputJson() {
            this.$http.get(`${WEB_ROOT}api/news/`, { params: {'make': '1'} })
                .then(res => this.openDialog('JSONを出力しました。'));
        },
        changeValue($event) {
            this.select[$event.target.name] = $event.target.value;
            this.search();
        },
    },
}
</script>
