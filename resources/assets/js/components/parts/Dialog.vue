<template>
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
                <button @click="close()">閉じる</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        options: Object,
        servType: String,
        servMessages: {
            type: Array,
            required: false,
        },
    },
    directives: {
        focus: {
            inserted: (el) => {
                el.focus();
            },
        },
    },
    methods: {
        close() {
            this.$store.dispatch('closeDialog');
        },
        isShow() {
            return this.messages.length > 0;
        },
    },
    computed: {
        title() {
            return this.options.title || 'メッセージ';
        },
        messages() {
            const m = this.options.messages;
            if (!Array.isArray(m)) {
                return m ? [m] : [];
            }
            return m;
        },
        messageClass() {
            if (this.options.type) {
                return `message-${this.options.type}`;
            }
            return 'message-info';
        },
    },
    mounted() {
        // サーバからのメッセージを保持している場合、それをオプションに設定
        console.log(this.servMessages);
        if (this.servMessages && this.servMessages.length) {
            this.$store.dispatch('openDialog', {
                title: this.title,
                messages: this.servMessages,
                type: this.servType,
            });
        }
    },
}
</script>
