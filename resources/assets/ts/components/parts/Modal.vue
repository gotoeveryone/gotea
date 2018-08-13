<template>
    <transition name="modal">
        <div class="iframe-modal" @click="close()" v-if="isShow()">
            <div class="modal-parent" :style="{'height': height, 'width': width}">
                <iframe class="modal-body" :src="options.url"></iframe>
                <div class="modal-close" @click="close()" v-if="isShow()">
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

<style lang="scss" scoped>
.iframe-modal {
    @include display-center();
    background-color: rgba($c_black, .7);
    z-index: 200;
    cursor: pointer;
    visibility: visible;
    opacity: 1;
    transition-property: visibility, opacity, height;
    transition-timing-function: ease-in;
    transition-duration: .2s;
    &.hide {
        visibility: hidden;
        opacity: 0;
    }
}

.modal-parent {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: auto;
    border-radius: 10px;
    transition-property: height;
    transition-timing-function: ease-in;
    transition-duration: .2s;
    &.hide {
        height: 0;
    }
}

.modal-body {
    width: 100%;
    height: 100%;
    border: none;
    border-radius: 10px;
}

.modal-close {
    @include flex-center();
    position: absolute;
    width: 30px;
    height: 25px;
    top: 0;
    right: 5px;
    border-radius: 0 0 5px 5px;
    background-color: $c_black;
    color: $c_white;
    cursor: pointer;
    &:hover {
        background-color: #333;
    }
    &-mark {
        margin: -3px auto 0;
        font-size: 20px;
        text-align: center;
    }
}

.modal-enter-active, .modal-leave-active {
    transition: opacity .5s
}
.modal-enter, .modal-leave-to {
    opacity: 0
}
</style>
