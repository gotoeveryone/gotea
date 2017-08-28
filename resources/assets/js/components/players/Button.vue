<template>
    <button type="button" :disabled="isDisable()" country="country"
        @click="newPlayer()">新規作成</button>
</template>

<script>
export default {
    props: {
        url: String,
        countryId: String,
        paramId: String,
        changed: Boolean,
    },
    methods: {
        newPlayer() {
            this.$store.dispatch('openModal', {
                url: `${this.url}?country_id=${this.countryId}`,
            });
        },
        isDisable() {
            if (!this.changed) {
                return this.paramId === '';
            }
            return this.countryId === '';
        },
    },
}
</script>
