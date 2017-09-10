<template>
    <li class="row">
        <div class="box">
            <div class="label-row">新規登録</div>
            <div class="add-condition input-row">
                <div class="box">
                    対象年：
                    <input type="text" v-model="year" maxlength="4" class="year" name="target_year">
                    期：
                    <input type="text" v-model="holding" maxlength="4" class="holding" name="holding">
                </div>
                <div class="button-column">
                    <input type="checkbox" id="newest" checked :disabled="required()" />
                    <label for="newest">最新として登録</label>
                    <button type="submit" :disabled="required()">登録</button>
                </div>
            </div>
            <div class="add-condition input-row">
                <div class="box" v-if="isTeam">
                    優勝団体名：<input type="text" name="win_group_name" v-model="teamName" maxlength="30" />
                </div>
                <div class="box" v-else>
                    設定棋士名：
                    <strong v-text="viewName"></strong>
                    <input type="hidden" name="player_id" v-model="playerId" />
                    <input type="hidden" name="rank_id" v-model="rankId" />
                </div>
            </div>
            <div class="box" v-if="!isTeam">
                <div class="label-row">棋士検索</div>
                <div class="input-row">
                    <div class="box">
                        棋士名：
                        <input type="text" v-model="name" class="playerName">
                    </div>
                    <div class="button-column">
                        <button type="button" @click="search()">検索</button>
                    </div>
                </div>
                <div class="retentions" v-if="players.length">
                    <table>
                        <tr v-for="(player, idx) in players" :key="idx">
                            <td v-text="getName(player)"></td>
                            <td v-text="player.nameEnglish"></td>
                            <td v-text="player.countryName"></td>
                            <td v-text="player.rankName"></td>
                            <td v-text="player.sex"></td>
                            <td class="select">
                                <button type="button" @click="select(player)">選択</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
export default {
    props: {
        domain: String,
        isTeam: String,
    },
    data: () => {
        return {
            viewName: '（検索エリアから棋士を検索してください。）',
            year: null,
            holding: null,
            name: '',
            playerId: null,
            rankId: null,
            teamName: null,
            players: [],
        };
    },
    methods: {
        required() {
            return (!this.year || !this.holding || !this.key);
        },
        search() {
            if (this.name === '') {
                this.$store.dispatch('openDialog', {
                    messages: '棋士名は必須です。',
                    type: 'error',
                });
                return;
            }

            this.$http.post(`${this.domain}api/players`, {
                name: this.name,
            }).then(res => {
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
            }).catch(res => {
                const message = res.body.response.message;
                this.$store.dispatch('openDialog', {
                    messages: (message || '更新に失敗しました…。'),
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
    },
    computed: {
        key() {
            return (this.isTeam ? this.teamName : this.playerId);
        },
    },
}
</script>
