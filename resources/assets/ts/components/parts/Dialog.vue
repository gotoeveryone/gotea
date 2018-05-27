<template>
    <transition name="dialog">
        <div class="dialog" v-if="isShow()">
            <div class="dialog-content">
                <div class="dialog-content-header">
                    <div class="dialog-content-title" v-text="title"></div>
                </div>
                <div class="dialog-content-body">
                    <div class="dialog-content-body-text">
                        <ul :class="messageClass">
                            <li v-for="(message, idx) in messages" :key="idx" v-html="message"></li>
                        </ul>
                    </div>
                </div>
                <div class="dialog-content-footer">
                    <button v-focus @click="close()">閉じる</button>
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
        servType: String,
        servMessages: Array as () => string[],
    },
    directives: {
        focus: {
            inserted: (el) => {
                el.focus()
            },
        },
    },
    methods: {
        close() {
            this.$store.dispatch('closeDialog')
        },
        isShow(): boolean {
            return this.messages.length > 0 &&
                ((this.isServ() && this.options.server) || (!this.isServ() && !this.options.server))
        },
        isServ(): boolean {
            return (this.servMessages && this.servMessages.length > 0)
        },
    },
    computed: {
        title(): string {
            return this.options.title || 'メッセージ'
        },
        messages(): string[] {
            const m = this.options.messages
            if (!Array.isArray(m)) {
                return m ? [m] : []
            }
            return m
        },
        messageClass(): string {
            if (this.options.type) {
                return `message-${this.options.type}`
            }
            return 'message-info'
        },
    },
    mounted() {
        // サーバからのメッセージを保持している場合、それをオプションに設定
        if (this.isServ()) {
            this.$store.dispatch('openDialog', {
                title: this.title,
                messages: this.servMessages,
                type: this.servType,
                server: true,
            })
        }
    },
})
</script>

<style scoped>
.dialog-enter-active, .dialog-leave-active {
    transition: opacity .5s
}
.dialog-enter, .dialog-leave-to {
    opacity: 0
}
</style>
