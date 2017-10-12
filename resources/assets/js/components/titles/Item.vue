<template>
    <li class="table-row" :class="getRowClass()">
        <span class="table-column name">
            <input type="text" @change="save" v-model="item.name">
        </span>
        <span class="table-column name">
            <input type="text" @change="save" v-model="item.nameEnglish">
        </span>
        <span class="table-column holding">
            <input type="text" class="input-holding" @change="save" v-model="item.holding">
        </span>
        <span class="table-column winner" v-text="getWinnerName()"></span>
        <span class="table-column order">
            <input type="text" class="input-sortorder" @change="save" v-model="item.sortOrder">
        </span>
        <span class="table-column team">
            <input type="checkbox" @change="save" v-model="item.isTeam">
        </span>
        <span class="table-column filename">
            <input type="text" @change="save" v-model="item.htmlFileName">
        </span>
        <span class="table-column modified">
            <date-picker type="date" @change="saveDatepicker" v-model="item.htmlFileModified" size="small" format="yyyy/MM/dd"></date-picker>
        </span>
        <span class="table-column closed">
            <input type="checkbox" @change="save" v-model="item.isClosed">
        </span>
        <span class="table-column open-detail">
            <a @click="add()" v-if="!item.id">登録</a>
            <a @click="select()" v-if="item.id">開く</a>
        </span>
    </li>
</template>

<script>
import { DatePicker } from 'element-ui';
import 'element-ui/lib/theme-default/date-picker.css';

export default {
    props: {
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
            this.$http.post(`/api/titles/`, this.item).then(res => {
                this.item.id = res.body.response.id;
                this.$store.dispatch('openDialog', {
                    messages: `タイトル【${this.item.titleNameJp}】を登録しました。`,
                });
            }).catch(res => {
                const message = res.body.response.message;
                this.$store.dispatch('openDialog', {
                    messages: (message || '登録に失敗しました…。'),
                    type: 'error',
                });
            });
        },
        save() {
            if (!this.item.id) {
                return;
            }
            // 更新処理
            this.$http.put(`/api/titles/${this.item.id}`, this.item).catch(res => {
                const message = res.body.response.message;
                this.$store.dispatch('openDialog', {
                    messages: (message || '更新に失敗しました…。'),
                    type: 'error',
                });
            });
        },
        select() {
            this.$store.dispatch('openModal', {
                url: `${this.detailUrl}/${this.item.id}`,
            });
        },
        saveDatepicker(value) {
            if (this.item.htmlFileModified !== value) {
                this.item.htmlFileModified = value;
                this.save();
            }
        },
        getRowClass() {
            return this.item.isClosed ? 'table-row-closed' : '';
        },
    },
}
</script>
