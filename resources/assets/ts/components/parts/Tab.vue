<template>
    <ul class="tabs">
        <li class="tab" v-for="(tab, idx) in tabs" :key="idx"
            v-text="tab" :class="{selectTab: isSelect(tab)}" @click="select(tab)"></li>
    </ul>
</template>

<script lang="ts">
import Vue from 'vue'

export default Vue.extend({
    props: {
        tabs: Array as () => string[],
    },
    data: () => {
        return {
            selectTab: '',
        }
    },
    methods: {
        select(tab: string) {
            this.selectTab = tab
        },
        isSelect(tab: string) {
            return this.selectTab === tab
        },
    },
    mounted() {
        this.selectTab = this.tabs[0]
    },
})
</script>
