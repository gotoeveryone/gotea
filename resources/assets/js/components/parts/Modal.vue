<template>
    <div class="iframe-modal" @click="close()" :class="{'hide': !isShow()}">
        <div class="modal-parent" :class="{'hide': !isShow()}"
            :style="{'height': getHeight(), 'width': getWidth()}">
            <iframe class="modal-body" :src="getUrl()"></iframe>
            <div class="modal-close" @click="close()"><span class="modal-close-mark">×</span></div>
        </div>
    </div>
</template>

<script>
    export default {
        methods: {
            getUrl() {
                return this.getOptions().url;
            },
            getWidth() {
                return this.getOptions().width || '90%';
            },
            getHeight() {
                return this.getOptions().height || '90%';
            },
            isShow() {
                return (this.getUrl() !== '');
            },
            close() {
                this.$store.dispatch('closeModal');
            },
            getOptions() {
                return this.$store.getters.modalOptions();
            },
        },
    }
</script>
