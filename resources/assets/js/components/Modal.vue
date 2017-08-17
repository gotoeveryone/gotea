<template>
    <div class="iframe-modal" @click="close()">
        <div class="modal-parent" :class="{'hide': !options.url}"
            :style="{'height': options.height, 'width': options.width}">
            <iframe class="modal-body" :src="options.url"></iframe>
            <div class="modal-close" @click="close()"><span class="modal-close-mark">×</span></div>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => {
            return {}
        },
        props: [
            'options'
        ],
        methods: {
            close() {
                this.$emit('modal-close');
            }
        }
    }
</script>
