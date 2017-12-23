<template>
    <section class="titles">
        <title-header @add="addRow" @search="onSearch" @json="outputJson"></title-header>
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
                <title-item v-for="(item, idx) in items" :key="idx" :item="item"></title-item>
            </ul>
        </div>
    </section>
</template>

<script>
import Header from './Header.vue';
import Item from './Item.vue';

export default {
    data: () => {
        return {
            items: [],
        };
    },
    components: {
        titleHeader: Header,
        titleItem: Item,
    },
    methods: {
        onSearch(_params) {
            const params = {
                country_id: _params.country,
                is_closed: _params.type,
            };

            this.$http.get('/api/titles', { params: params }).then(res => {
                this.items = res.body.response;
            });
        },
        addRow(_params) {
            this.items.push({
                countryId: _params.country,
                holding: 1,
                sortOrder: this.items.length + 1,
                htmlFileModified: '',
                isClosed: false,
            });
        },
        outputJson() {
            this.$http
                .post('/api/titles/news')
                .then(() =>
                    this.$store.dispatch('openDialog', {
                        messages: 'JSONを出力しました。',
                    })
                )
                .catch(() =>
                    this.$store.dispatch('openDialog', {
                        messages: 'JSON出力に失敗しました…。',
                        type: 'error',
                    })
                );
        },
        changeValue($event) {
            this.select[$event.target.name] = $event.target.value;
            this.search();
        },
    },
};
</script>
