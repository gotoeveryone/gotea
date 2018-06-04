<template>
    <section class="titles">
        <title-header @add="addRow" @search="onSearch" @json="outputJson"></title-header>
        <div class="search-results">
            <ul class="table-header">
                <li class="table-row">
                    <span class="table-column table-column_name">タイトル名</span>
                    <span class="table-column table-column_name">タイトル名（英語）</span>
                    <span class="table-column table-column_holding">期</span>
                    <span class="table-column table-column_winner">優勝者</span>
                    <span class="table-column table-column_order">並び順</span>
                    <span class="table-column table-column_team">団体</span>
                    <span class="table-column table-column_filename">ファイル名</span>
                    <span class="table-column table-column_modified">修正日</span>
                    <span class="table-column table-column_closed">終了<br>棋戦</span>
                    <span class="table-column table-column_open-detail">詳細</span>
                </li>
            </ul>
            <ul class="table-body" v-if="items.length">
                <title-item v-for="(item, idx) in items" :key="idx" :item="item"></title-item>
            </ul>
        </div>
    </section>
</template>

<script lang="ts">
import Vue from 'vue'
import axios from 'axios'

import Header from '@/components/titles/Header.vue'
import Item from '@/components/titles/Item.vue'

export default Vue.extend({
    data: () => {
        return {
            items: Array(),
        }
    },
    components: {
        titleHeader: Header,
        titleItem: Item,
    },
    methods: {
        onSearch(_params: any) {
            const params = {
                country_id: _params.country,
                is_closed: _params.type,
            }

            axios.get('/api/titles', { params: params })
                .then(res => this.items = res.data.response)
        },
        addRow(_params: any) {
            this.items.push({
                countryId: _params.country,
                holding: 1,
                sortOrder: this.items.length + 1,
                htmlFileModified: '',
                isClosed: false,
            })
        },
        outputJson() {
            axios
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
                )
        },
    }
})
</script>
