<template>
    <div class="search-results">
        <ul class="table-header">
            <li class="table-row">
                <span class="name">タイトル名</span>
                <span class="name">タイトル名（英語）</span>
                <span class="holding">期</span>
                <span class="winner">優勝者</span>
                <span class="order">並び<br>順</span>
                <span class="team">団体</span>
                <span class="filename">HTML<br>ファイル名</span>
                <span class="modified">修正日</span>
                <span class="closed">終了<br>棋戦</span>
                <span>詳細</span>
            </li>
        </ul>
        <ul class="table-body" v-if="items.length">
            <li class="table-row" v-for="(item, idx) in items" :key="idx" :class="getRowClass(item)">
                <span class="name">
                    <input type="text" @change="save(item)" v-model="item.titleNameJp">
                </span>
                <span class="name">
                    <input type="text" @change="save(item)" v-model="item.titleName">
                </span>
                <span class="holding">
                    <input type="text" @change="save(item)" v-model="item.holding">
                </span>
                <span class="winner" v-text="getWinnerName(item)"></span>
                <span class="order">
                    <input type="text" @change="save(item)" v-model="item.sortOrder">
                </span>
                <span class="team">
                    <input type="checkbox" @change="save(item)" v-model="item.isTeam">
                </span>
                <span class="filename">
                    <input type="text" @change="save(item)" v-model="item.htmlFileName">
                </span>
                <span class="modified">
                    <input type="text" @change="saveDatepicker($event, item)" class="datepicker" v-model="item.htmlFileModified">
                </span>
                <span class="closed">
                    <input type="checkbox" @change="save(item)" v-model="item.isClosed">
                </span>
                <span>
                    <a @click="add(item)" v-if="!item.titleId">登録</a>
                    <a @click="select(item)" v-if="item.titleId">開く</a>
                </span>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        domain: String,
        detailUrl: String,
        items: Array,
    },
    methods: {
        getWinnerName(_item) {
            return _item.winnerName || '';
        },
        add(_item) {
            // 登録処理
            this.$http.post(`${this.domain}api/titles/`, JSON.stringify(_item)).then(res => {
                _item.titleId = res.body.response.titleId;
                this.$store.dispatch('openDialog', {
                    messages: `タイトル【${_item.titleNameJp}】を登録しました。`,
                });
            }).catch(res => {
                const message = res.body.response.message;
                this.$store.dispatch('openDialog', {
                    messages: (message || '登録に失敗しました…。'),
                    error: true,
                });
            });
        },
        save(_item) {
            if (!_item.titleId) {
                return;
            }
            // 更新処理
            this.$http.put(`${this.domain}api/titles/${_item.titleId}`, JSON.stringify(_item)).catch(res => {
                const message = res.body.response.message;
                this.$store.dispatch('openDialog', {
                    messages: (message || '更新に失敗しました…。'),
                    error: true,
                });
            });
        },
        select(_item) {
            this.$store.dispatch('openModal', {
                url: `${this.detailUrl}/${_item.titleId}`,
            });
        },
        saveDatepicker($event, _item) {
            _item.htmlFileModified = $event.target.value;
            this.save(_item);
        },
        getRowClass(_item) {
            return _item.isClosed ? 'closed' : '';
        },
    },
}
</script>
