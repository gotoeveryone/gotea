<template>
    <div class="dialog" :class="{'hide': !isShow()}">
        <div class="dialog-content" :class="{'hide': !isShow()}">
            <div class="dialog-content-header">
                <div class="dialog-content-title" v-text="getTitle()"></div>
            </div>
            <div class="dialog-content-body">
                <div class="dialog-content-body-text">
                    <ul :class="getMessageClass()">
                        <li v-for="(message, idx) in getMessages()" :key="idx" v-html="message"></li>
                    </ul>
                </div>
            </div>
            <div class="dialog-content-footer">
                <button id="dialog-close" class="dialog-close" autofocus @click="close()">閉じる</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        servType: String,
        servMessage: String,
    },
    data: () => {
        return {
            options: {
                title: '',
                messages: [],
                type: 'info',
            },
        };
    },
    methods: {
        getOptions() {
            // 保持しているオプションがあればそれを返却
            if (this.options.messages.length) {
                return this.options;
            }
            const options = this.$store.getters.dialogOptions();

            // 配列に変換
            if (!Array.isArray(options.messages) && options.messages) {
                options.messages = [options.messages];
            }
            return options;
        },
        getTitle() {
            return this.getOptions().title || 'メッセージ';
        },
        getMessages() {
            return this.getOptions().messages;
        },
        getMessageClass() {
            const op = this.getOptions();
            if (op.type) {
                return `message-${op.type}`;
            }
            return 'message-info';
        },
        isShow() {
            return this.getMessages().length > 0;
        },
        close() {
            if (this.servMessage) {
                this.options = {
                    title: '',
                    messages: [],
                    type: 'info',
                };
            } else {
                this.$store.dispatch('closeDialog');
            }
        },
    },
    mounted() {
        // サーバからのメッセージを保持している場合、それをオプションに設定
        if (this.servMessage) {
            this.options = {
                title: this.getTitle(),
                messages: this.servMessage.split(','),
                type: this.servType,
            }
        }
    },
}
</script>
