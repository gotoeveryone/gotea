<template>
    <li class="table-row" :class="getRowClass()">
        <span class="name">
            <input type="text" @change="save" v-model="item.name">
        </span>
        <span class="name">
            <input type="text" @change="save" v-model="item.nameEnglish">
        </span>
        <span class="holding">
            <input type="text" @change="save" v-model="item.holding">
        </span>
        <span class="winner" v-text="getWinnerName()"></span>
        <span class="order">
            <input type="text" @change="save" v-model="item.sortOrder">
        </span>
        <span class="team">
            <input type="checkbox" @change="save" v-model="item.isTeam">
        </span>
        <span class="filename">
            <input type="text" @change="save" v-model="item.htmlFileName">
        </span>
        <span class="modified">
            <date-picker type="date" @change="saveDatepicker" v-model="item.htmlFileModified"
                size="small" format="yyyy/MM/dd"></date-picker>
        </span>
        <span class="closed">
            <input type="checkbox" @change="save" v-model="item.isClosed">
        </span>
        <span>
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
            this.$http.post(`${this.domain}api/titles/`, this.item).then(res => {
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
            this.$http.put(`${this.domain}api/titles/${this.item.id}`, this.item).catch(res => {
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
            return this.item.isClosed ? 'closed' : '';
        },
    },
}
</script>
