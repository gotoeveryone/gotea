<template>
    <ul class="tabs">
        <li class="tab" v-for="(tab, idx) in tabs" :key="idx"
            v-text="tab" :class="{selectTab: isSelect(tab)}" @click="select(tab)"></li>
    </ul>
</template>

<script>
export default {
    props: {
        tabs: Array,
        id: Number,
        hasHistories: Boolean,
    },
    data: () => {
        return {
            selectTab: '',
        };
    },
    methods: {
        select(_tab) {
            this.selectTab = _tab;
        },
        isSelect(_tab) {
            return this.selectTab === _tab;
        },
    },
    mounted() {
        this.selectTab = this.tabs[0];
    },
}
</script>
