<template>
    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="table-column_rank">段位</span>
                <span class="table-column_count">人数</span>
            </li>
        </ul>
        <ul class="table-body" v-if="items.length">
            <li class="table-row" v-for="(item, idx) in items" :key="idx">
                <span class="table-column_rank" v-text="item.name"></span>
                <span class="table-column_count" v-text="count(item)"></span>
            </li>
        </ul>
    </div>
</template>

<script lang="ts">
import Vue from 'vue'

export default Vue.extend({
    props: {
        items: Array,
    },
    methods: {
        count(_item: any) {
            return `${_item.count}人`
        },
    },
})
</script>
