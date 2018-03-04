<template>
    <ul class="boxes">
        <li class="label-row" v-text="text"></li>
        <li class="detail-row">
            <input type="hidden" :value="historyId" name="id">
            <input type="hidden" v-model="teamTitle" name="is_team">
            <div class="input-row">
                <div class="input-box">
                    <div class="input number">
                        <label for="target-year" class="label">対象年度</label>
                        <input type="number" v-model="year" id="target-year" maxlength="4" class="year" name="target_year">
                    </div>
                    <div class="input number">
                        <label for="holding" class="label">期</label>
                        <input type="number" v-model="holding" maxlength="4" class="holding" name="holding">
                    </div>
                    <div class="right-column">
                        <div class="input checkbox">
                            <input name="newest" value="0" type="hidden">
                            <label for="newest" class="label" v-if="!edit">
                                <input type="checkbox" id="newest" name="newest" checked="checked"
                                    :disabled="required()" v-if="!edit" />
                                最新として登録
                            </label>
                        </div>
                        <button type="button" @click="clearData()" v-if="edit">編集をやめる</button>
                        <button type="submit" :disabled="required()">保存</button>
                    </div>
                </div>
            </div>
        </li>
        <li class="detail-row">
            <div class="input-row">
                <div class="input-box" v-if="teamTitle">
                    <div class="input text">
                        <label for="win-group-name" class="label">優勝団体名</label>
                        <input type="text" id="win-group-name" name="win_group_name" v-model="teamName" maxlength="30" />
                    </div>
                </div>
                <div class="input-box" v-else>
                    <span>設定棋士名</span>
                    <strong v-text="viewName"></strong>
                    <input type="hidden" name="player_id" v-model="playerId" />
                    <input type="hidden" name="rank_id" v-model="rankId" />
                </div>
            </div>
        </li>
        <li class="label-row" v-if="!teamTitle">棋士検索</li>
        <li class="detail-row" v-if="!teamTitle">
            <div class="input-row">
                <div class="input-box">
                    <div class="input text">
                        <label for="name" class="label">棋士名</label>
                        <input type="text" id="name" v-model="name" class="name">
                    </div>
                </div>
                <div class="right-column">
                    <button type="button" @click="search()">検索</button>
                </div>
            </div>
        </li>
        <li v-if="players.length">
            <ul class="table-body retentions">
                <li class="table-row" v-for="(player, idx) in players" :key="idx">
                    <span class="retentions-name" v-text="getName(player)"></span>
                    <span class="retentions-name" v-text="player.nameEnglish"></span>
                    <span class="retentions-country" v-text="player.countryName"></span>
                    <span class="retentions-rank" v-text="player.rankName"></span>
                    <span class="retentions-sex" v-text="player.sex"></span>
                    <span class="retentions-select">
                        <button type="button" @click="select(player)">選択</button>
                    </span>
                </li>
            </ul>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        isTeam: String,
        historyId: Number,
    },
    data: () => {
        return {
            edit: false,
            viewName: '（検索エリアから棋士を検索してください。）',
            year: null,
            holding: null,
            name: '',
            playerId: null,
            rankId: null,
            teamName: null,
            teamTitle: false,
            players: [],
        };
    },
    methods: {
        required() {
            return !this.year || !this.holding || !this.key;
        },
        search() {
            if (this.name === '') {
                this.$store.dispatch('openDialog', {
                    messages: '棋士名は必須です。',
                    type: 'error',
                });
                return;
            }

            this.$http
                .post('/api/players', {
                    name: this.name,
                })
                .then(res => {
                    const players = res.body.response.results;
                    switch (players.length) {
                        case 0:
                            this.$store.dispatch('openDialog', {
                                messages: '検索結果が0件でした。',
                                type: 'warning',
                            });
                            break;
                        case 1:
                            this.select(players[0]);
                            break;
                        default:
                            this.players = players;
                            break;
                    }
                })
                .catch(res => {
                    const message = res.body.response.message;
                    this.$store.dispatch('openDialog', {
                        messages: message || '更新に失敗しました…。',
                        type: 'error',
                    });
                });
        },
        getName(_player) {
            if (_player.nameOther) {
                return `${_player.name} [${_player.nameOther}]`;
            }
            return _player.name;
        },
        select(_player) {
            this.playerId = _player.id;
            this.rankId = _player.rankId;
            this.viewName = `${_player.name} ${_player.rankName}`;
            this.name = '';
            this.players = [];
        },
        isNew() {
            return !this.historyId;
        },
        clearData() {
            this.viewName = '（検索エリアから棋士を検索してください。）';
            this.year = null;
            this.holding = null;
            this.name = '';
            this.playerId = null;
            this.rankId = null;
            this.teamName = null;
            this.teamTitle = this.isTeam;
            this.players = [];
            this.edit = false;
            this.$emit('cleared');
        },
    },
    mounted() {
        this.teamTitle = this.isTeam;
    },
    computed: {
        key() {
            return this.isTeam ? this.teamName : this.playerId;
        },
        text() {
            return this.edit ? '編集' : '新規登録';
        },
    },
    watch: {
        historyId(_value) {
            if (_value) {
                this.$http.get(`/api/histories/${_value}`).then(r => {
                    const json = r.body.response;
                    this.teamTitle = json.isTeam;
                    this.playerId = json.playerId;
                    this.holding = json.holding;
                    this.rankId = json.rankId;
                    this.year = json.targetYear;
                    this.viewName = `${json.winPlayerName} ${json.winRankName}`;
                    this.teamName = json.winGroupName;
                    this.edit = true;
                });
            }
        },
    },
};
</script>
