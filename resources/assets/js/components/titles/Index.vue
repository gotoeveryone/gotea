<template>
    <section class="titles">
        <title-header :domain="domain" @add="addRow" @search="onSearch" @json="outputJson"></title-header>
        <div class="search-results">
            <ul class="table-header">
                <li class="table-row">
                    <span class="table-column name">タイトル名</span>
                    <span class="table-column name">タイトル名（英語）</span>
                    <span class="table-column holding">期</span>
                    <span class="table-column winner">優勝者</span>
                    <span class="table-column order">並び順</span>
                    <span class="table-column team">団体</span>
                    <span class="table-column filename">ファイル名</span>
                    <span class="table-column modified">修正日</span>
                    <span class="table-column closed">終了<br>棋戦</span>
                    <span class="table-column open-detail">詳細</span>
                </li>
            </ul>
            <ul class="table-body" v-if="items.length">
                <title-item v-for="(item, idx) in items" :key="idx"
                    :domain="domain" :detail-url="detailUrl" :item="item"></title-item>
            </ul>
        </div>
    </section>
</template>

<script>
import Header from './Header.vue';
import Item from './Item.vue';

export default {
    props: {
        domain: String,
        detailUrl: String,
    },
    data: () => {
        return {
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
        titleItem: Item,
    },
    methods: {
        onSearch(_params) {
            const params = {
                'country_id': _params.country,
                'is_closed': _params.type,
            };

            this.$http.get(`${this.domain}api/titles`, { params: params })
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
            this.$http.post(`${this.domain}api/create-news`)
                .then(() => this.$store.dispatch('openDialog', {
                    messages: 'JSONを出力しました。',
                })).catch(() => this.$store.dispatch('openDialog', {
                    messages: 'JSON出力に失敗しました…。',
                    type: 'error',
                }));
        },
        changeValue($event) {
            this.select[$event.target.name] = $event.target.value;
            this.search();
        },
    },
}
</script>
