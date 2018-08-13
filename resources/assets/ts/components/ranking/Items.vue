<template>
    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="table-column table-column_no">No.</span>
                <span class="table-column table-column_player">棋士名</span>
                <span class="table-column table-column_point">勝</span>
                <span class="table-column table-column_point">敗</span>
                <span class="table-column table-column_point">分</span>
                <span class="table-column table-column_percent">勝率</span>
            </li>
        </ul>
        <ul class="table-body" v-if="items.length">
            <li class="table-row" v-for="(item, idx) in items" :key="idx">
                <span class="table-column table-column_no">
                    <span v-text="getRank(idx, item)"></span>
                </span>
                <span class="table-column table-column_player">
                    <a class="view-link" :class="getSexClass(item)" @click="select(item)"
                        v-text="item.name"></a>
                </span>
                <span class="table-column table-column_point" v-text="item.win"></span>
                <span class="table-column table-column_point" v-text="item.lose"></span>
                <span class="table-column table-column_point" v-text="item.draw"></span>
                <span class="table-column table-column_percent" v-text="item.percentage"></span>
            </li>
        </ul>
    </div>
</template>

<script lang="ts">
import Vue from 'vue'

export default Vue.extend({
    props: {
        items: Array as () => any[],
    },
    methods: {
        getRank(_idx: number, _row: any) {
            if (this.items[_idx - 1]) {
                const beforeRank = this.items[_idx - 1].rank
                return _row.rank === beforeRank ? '' : `${_row.rank}`
            }
            return _row.rank
        },
        getSexClass(_row: any) {
            return _row.sex === '女性' ? 'female' : 'male'
        },
        select(_row: any) {
            this.$store.dispatch('openModal', {
                url: _row.url,
            })
        },
    },
})
</script>
