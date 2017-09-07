<template>
    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="rank">段位</span>
                <span class="count">人数</span>
            </li>
        </ul>
        <ul class="table-body" v-if="items.length">
            <li class="table-row" v-for="(item, idx) in items" :key="idx">
                <span class="rank" v-text="item.name"></span>
                <span class="count" v-text="count(item)"></span>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        items: Array,
    },
    methods: {
        count(_item) {
            return `${_item.count}人`;
        },
    },
}
</script>
