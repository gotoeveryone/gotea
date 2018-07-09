<template>
    <transition name="modal">
        <div class="iframe-modal" @click="close()" v-if="isShow()">
            <div class="modal-parent" :style="{'height': height, 'width': width}">
                <iframe class="modal-body" :src="options.url"></iframe>
                <div class="modal-close" @click="close()">
                    <span class="modal-close-mark">×</span>
                </div>
            </div>
        </div>
    </transition>
</template>

<script lang="ts">
import Vue from 'vue'

export default Vue.extend({
    props: {
        options: Object,
    },
    methods: {
        close() {
            this.$store.dispatch('closeModal')
                .then(() => {
                    if (typeof this.options.callback === 'function') {
                        this.options.callback();
                    }
                })
        },
        isShow() {
            return (this.options.url !== '')
        },
    },
    computed: {
        height(): string {
            return this.options.height || '90%'
        },
        width(): string {
            return this.options.width || '90%'
        },
    },
})
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
    transition: opacity .5s
}
.modal-enter, .modal-leave-to {
    opacity: 0
}
</style>
