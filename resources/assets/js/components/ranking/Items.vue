<template>
    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="no">No.</span>
                <span class="player">棋士名</span>
                <span class="point">勝</span>
                <span class="point">敗</span>
                <span class="point">分</span>
                <span class="percent">勝率</span>
            </li>
        </ul>
        <ul class="table-body" v-if="items.length">
            <li class="table-row" v-for="(item, idx) in items" :key="idx">
                <span class="right no">
                    <span v-text="getRank(idx, item)"></span>
                </span>
                <span class="left player">
                    <a class="player-link" :class="getSexClass(item)" @click="select(item)"
                        v-text="item.name"></a>
                </span>
                <span class="point" v-text="item.win"></span>
                <span class="point" v-text="item.lose"></span>
                <span class="point" v-text="item.draw"></span>
                <span class="percent" v-text="item.percentage"></span>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        detailUrl: String,
        items: Array,
    },
    data: () => {
        return {}
    },
    methods: {
        getRank(_idx, _row) {
            if (this.items[_idx - 1]) {
                const beforeRank = this.items[_idx - 1].rank;
                return (_row.rank === beforeRank) ? '' : `${_row.rank}`;
            }
            return _row.rank;
        },
        getSexClass(_row) {
            return (_row.sex === '女性' ? 'female' : 'male');
        },
        select(_row) {
            this.$store.dispatch('openModal', {
                url: `${this.detailUrl}/${_row.id}`,
            });
        },
    },
}
</script>
