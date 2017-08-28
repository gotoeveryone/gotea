<template>
    <div class="dialog" :class="{'hide': !isShow()}">
        <div class="dialog-content" :class="{'hide': !isShow()}">
            <div class="dialog-content-header">
                <div class="dialog-content-title" v-text="getTitle()"></div>
            </div>
            <div class="dialog-content-body">
                <div class="dialog-content-body-text">
                    <ul :class="{'message-error': isError()}">
                        <li v-for="(message, idx) in getMessages()" :key="idx" v-text="message"></li>
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
            propsMessage: String,
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
            isError() {
                return this.getOptions().error;
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
            if (this.propsMessage) {
                this.$store.dispatch('openDialog', this.propsMessage, this.getTitle());
            }
        },
    }
</script>
