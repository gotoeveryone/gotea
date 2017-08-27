<template>
    <div class="dialog" :class="{'hide': !isShow()}">
        <div class="dialog-content" :class="{'hide': !isShow()}">
            <div class="dialog-content-header">
                <div class="dialog-content-title" v-text="getTitle()"></div>
            </div>
            <div class="dialog-content-body">
                <div class="dialog-content-body-text" v-html="getMessages()"></div>
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
                return this.getOptions().messages;
            },
            isShow() {
                return (this.getMessages());
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
