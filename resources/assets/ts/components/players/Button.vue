<template>
    <button type="button" :disabled="isDisable()" country="country"
        @click="newPlayer()">新規作成</button>
</template>

<script lang="ts">
import Vue from 'vue'

export default Vue.extend({
    props: {
        url: String,
        countryId: String,
        paramId: String,
        changed: Boolean,
    },
    methods: {
        newPlayer() {
            this.$store.dispatch('openModal', {
                url: `${this.url}?country_id=${this.targetId}`,
            })
        },
        isDisable() {
            return this.targetId === ''
        },
    },
    computed: {
        targetId(): string {
            if (!this.changed) {
                return this.paramId
            }
            return this.countryId
        },
    },
})
</script>
