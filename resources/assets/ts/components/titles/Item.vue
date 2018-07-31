<template>
    <li class="table-row" :class="rowClass">
        <span class="table-column table-column_name">
            <input type="text" @change="save" v-model="item.name">
        </span>
        <span class="table-column table-column_name">
            <input type="text" @change="save" v-model="item.nameEnglish">
        </span>
        <span class="table-column table-column_holding">
            <input type="text" class="table-column_holding-input" @change="save" v-model="item.holding">
        </span>
        <span class="table-column table-column_winner" v-text="winnerName"></span>
        <span class="table-column table-column_order">
            <input type="text" class="table-column_order-input" @change="save" v-model="item.sortOrder">
        </span>
        <span class="table-column table-column_team">
            <input type="checkbox" @change="save" v-model="item.isTeam">
        </span>
        <span class="table-column table-column_filename">
            <input type="text" @change="save" v-model="item.htmlFileName">
        </span>
        <span class="table-column table-column_modified">
            <input type="text" class="datepicker table-column_modified-input" @change="saveDatepicker($event)" v-model="item.htmlFileModified">
        </span>
        <span class="table-column table-column_closed">
            <input type="checkbox" @change="save" v-model="item.isClosed" :disabled="!isSaved()">
        </span>
        <span class="table-column table-column_menu">
            <input type="checkbox" @change="save" v-model="item.hasMenu" :disabled="!isSaved()">
        </span>
        <span class="table-column table-column_open-detail">
            <a class="view-link" @click="select()" v-text="label"></a>
        </span>
    </li>
</template>

<script lang="ts">
import Vue from 'vue'
import axios from 'axios'

export default Vue.extend({
    props: {
        item: Object,
    },
    data: () => {
        return {
            label: '',
        }
    },
    methods: {
        save() {
            // 未登録なら何もしない
            if (!this.isSaved()) {
                return
            }
            // 更新処理
            axios.put(`/api/titles/${this.item.id}`, this.item)
                .catch(res => {
                    const message = res.data.response.message
                    this.$store.dispatch('openDialog', {
                        messages: message || '更新に失敗しました…。',
                        type: 'error',
                    })
            })
        },
        select() {
            if (!this.isSaved()) {
                this.add()
            } else {
                this.$emit('openModal', {
                    url: this.item.url,
                })
            }
        },
        add() {
            // 登録処理
            axios
                .post('/api/titles/', this.item)
                .then(res => {
                    this.item.id = res.data.response.id
                    this.setLabel()
                    this.$store.dispatch('openDialog', {
                        messages: `タイトル【${this.item.name}】を登録しました。`,
                    })
                })
                .catch(res => {
                    const message = res.data.response.message
                    this.$store.dispatch('openDialog', {
                        messages: message || '登録に失敗しました…。',
                        type: 'error',
                    })
                })
        },
        saveDatepicker($event: any) {
            if (this.item.htmlFileModified !== $event.target.value) {
                this.item.htmlFileModified = $event.target.value
                this.save()
            }
        },
        setLabel() {
            this.label = this.isSaved() ? '開く' : '登録'
        },
        isSaved() {
            return this.item.id !== null && this.item.id !== undefined
        },
    },
    computed: {
        winnerName(): string {
            return this.item.winnerName || ''
        },
        rowClass(): string {
            return this.item.isClosed ? 'table-row-closed' : ''
        }
    },
    mounted() {
        this.setLabel()
    },
})
</script>
