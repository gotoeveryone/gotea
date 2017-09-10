<template>
    <li class="table-row" :class="getRowClass()">
        <span class="name">
            <input type="text" @change="save()" v-model="item.titleNameJp">
        </span>
        <span class="name">
            <input type="text" @change="save()" v-model="item.titleName">
        </span>
        <span class="holding">
            <input type="text" @change="save()" v-model="item.holding">
        </span>
        <span class="winner" v-text="getWinnerName()"></span>
        <span class="order">
            <input type="text" @change="save()" v-model="item.sortOrder">
        </span>
        <span class="team">
            <input type="checkbox" @change="save()" v-model="item.isTeam">
        </span>
        <span class="filename">
            <input type="text" @change="save()" v-model="item.htmlFileName">
        </span>
        <span class="modified">
            <input type="text" @change="saveDatepicker($event)" class="datepicker" v-model="item.htmlFileModified">
        </span>
        <span class="closed">
            <input type="checkbox" @change="save()" v-model="item.isClosed">
        </span>
        <span>
            <a @click="add()" v-if="!item.titleId">登録</a>
            <a @click="select()" v-if="item.titleId">開く</a>
        </span>
    </li>
</template>

<script>
import { DatePicker } from 'element-ui';
import 'element-ui/lib/theme-default/date-picker.css';

export default {
    props: {
        domain: String,
        detailUrl: String,
        item: Object,
    },
    components: {
        datePicker: DatePicker,
    },
    methods: {
        getWinnerName() {
            return this.item.winnerName || '';
        },
        add() {
            // 登録処理
            this.$http.post(`${this.domain}api/titles/`, JSON.stringify(this.item)).then(res => {
                this.item.titleId = res.body.response.titleId;
                this.$store.dispatch('openDialog', {
                    messages: `タイトル【${this.item.titleNameJp}】を登録しました。`,
                });
            }).catch(res => {
                const message = res.body.response.message;
                this.$store.dispatch('openDialog', {
                    messages: (message || '登録に失敗しました…。'),
                    error: true,
                });
            });
        },
        save() {
            if (!this.item.titleId) {
                return;
            }
            // 更新処理
            this.$http.put(`${this.domain}api/titles/${this.item.titleId}`, JSON.stringify(this.item)).catch(res => {
                const message = res.body.response.message;
                this.$store.dispatch('openDialog', {
                    messages: (message || '更新に失敗しました…。'),
                    error: true,
                });
            });
        },
        select() {
            this.$store.dispatch('openModal', {
                url: `${this.detailUrl}/${this.item.titleId}`,
            });
        },
        saveDatepicker($event) {
            this.item.htmlFileModified = $event.target.value;
            this.save();
        },
        getRowClass() {
            return this.item.isClosed ? 'closed' : '';
        },
    },
}
</script>
