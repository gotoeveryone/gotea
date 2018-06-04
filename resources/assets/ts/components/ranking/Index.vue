<template>
    <section class="ranking">
        <ranking-header :lastUpdate="lastUpdate" @search="onSearch" @json="outputJson"></ranking-header>
        <ranking-items :items="items"></ranking-items>
    </section>
</template>

<script lang="ts">
import Vue from 'vue'
import axios from 'axios'
import moment from 'moment'

import RankingHeader from '@/components/ranking/Header.vue'
import RankingItems from '@/components/ranking/Items.vue'

export default Vue.extend({
    data: () => {
        return {
            lastUpdate: '',
            items: Array(),
        }
    },
    components: {
        rankingHeader: RankingHeader,
        rankingItems: RankingItems,
    },
    methods: {
        onSearch(_params: any) {
            const params = {
                from: _params.from || '',
                to: _params.to || '',
            }

            axios.get(this.createUrl(_params), { params: params }).then(res => {
                const json = res.data.response
                if (json.lastUpdate) {
                    const dateObj = moment(json.lastUpdate)
                    this.lastUpdate = `${dateObj.year()}年${dateObj.month() + 1}月${dateObj.date()}日`
                }
                this.items = json.ranking
            })
        },
        outputJson(_params: any) {
            const params = {
                from: _params.from || '',
                to: _params.to || '',
            }

            axios
                .post(this.createUrl(_params), params)
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
        createUrl(_params: any) {
            return `/api/players/ranking/${_params.country}/${_params.year}/${_params.limit}`
        },
    },
})
</script>
