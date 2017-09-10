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
    methods: {
        getTitle() {
            return this.getOptions().title || 'メッセージ';
        },
        getMessages() {
            const op = this.getOptions();
            if (!Array.isArray(op.messages) && op.messages) {
                return [op.messages];
            }
            return op.messages;
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
            this.$store.dispatch('closeDialog');
        },
        getOptions() {
            return this.$store.getters.dialogOptions();
        },
    },
    mounted() {
        if (this.servMessage) {
            this.$store.dispatch('openDialog', {
                messages: this.servMessage.split(','),
                title: this.getTitle(),
                type: this.servType,
            });
        }
    },
}
</script>
