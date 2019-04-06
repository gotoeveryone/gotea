<template>
  <ul class="boxes">
    <li v-text="text" class="label-row" />
    <li class="detail-row">
      <input :value="historyId" type="hidden" name="id">
      <input v-model="teamTitle" type="hidden" name="is_team">
      <fieldset class="detail-box box1">
        <fieldset class="detail-box-row">
          <div class="input number">
            <label for="target-year" class="detail-box_label">対象年度</label>
            <input
              id="target-year"
              v-model="year"
              type="number"
              maxlength="4"
              class="year"
              name="target_year"
            >
          </div>
        </fieldset>
      </fieldset>
      <fieldset class="detail-box box1">
        <fieldset class="detail-box-row">
          <div class="input number">
            <label for="holding" class="detail-box_label">期</label>
            <input v-model="holding" type="number" maxlength="4" class="holding" name="holding">
          </div>
        </fieldset>
      </fieldset>
      <fieldset class="detail-box box1">
        <fieldset class="detail-box-row">
          <div class="input checkbox">
            <input name="newest" value="0" type="hidden">
            <label v-if="!edit" for="newest" class="checkbox-label">
              <input
                id="newest"
                :disabled="required()"
                v-if="!edit"
                type="checkbox"
                name="newest"
                checked="checked"
              >
              最新として登録
            </label>
          </div>
        </fieldset>
      </fieldset>
    </li>
    <li class="detail-row">
      <fieldset class="detail-box box1">
        <fieldset v-if="teamTitle" class="detail-box-row">
          <div class="input text">
            <label for="win-group-name" class="detail-box_label">優勝団体名</label>
            <input
              id="win-group-name"
              v-model="teamName"
              type="text"
              name="win_group_name"
              maxlength="30"
            >
          </div>
        </fieldset>
        <fieldset v-else class="detail-box-row">
          <div class="input">
            <label class="detail-box_label">設定棋士名</label>
            <strong v-text="viewName" />
            <input v-model="playerId" type="hidden" name="player_id">
            <input v-model="rankId" type="hidden" name="rank_id">
          </div>
        </fieldset>
      </fieldset>
      <fieldset class="detail-box detail-box-right">
        <fieldset class="detail-box-button-row">
          <div class="input">
            <button @click="clearData()" v-if="edit" type="button">
              編集をやめる
            </button>
            <button :disabled="required()" type="submit" class="button button-primary">
              保存
            </button>
          </div>
        </fieldset>
      </fieldset>
    </li>
    <li v-if="!teamTitle" class="label-row">
      棋士検索
    </li>
    <li v-if="!teamTitle" class="detail-row">
      <fieldset class="detail-box box1">
        <fieldset class="detail-box-row">
          <div class="input text">
            <label for="name" class="detail-box_label">棋士名</label>
            <input id="name" v-model="name" type="text" class="name">
          </div>
        </fieldset>
      </fieldset>
      <fieldset class="detail-box detail-box-right">
        <fieldset class="detail-box-button-row">
          <div class="input">
            <button @click="search()" type="button" class="button button-primary">
              検索
            </button>
          </div>
        </fieldset>
      </fieldset>
    </li>
    <li v-if="players.length">
      <ul class="table-body retentions">
        <li v-for="(player, idx) in players" :key="idx" class="table-row">
          <span v-text="getName(player)" class="retentions-name" />
          <span v-text="player.nameEnglish" class="retentions-name" />
          <span v-text="player.countryName" class="retentions-country" />
          <span v-text="player.rankName" class="retentions-rank" />
          <span v-text="player.sex" class="retentions-sex" />
          <span class="retentions-select">
            <button @click="select(player)" type="button">選択</button>
          </span>
        </li>
      </ul>
    </li>
  </ul>
</template>

<script lang="ts">
import Vue from 'vue';
import axios from 'axios';

import { Player } from '@/types/titles';

export default Vue.extend({
  props: {
    isTeam: {
      type: String,
      default: '',
    },
    historyId: {
      type: Number,
      default: null,
    },
  },
  data: () => {
    return {
      edit: false,
      viewName: '（検索エリアから棋士を検索してください。）',
      year: null,
      holding: null,
      name: '',
      playerId: null as number | null,
      rankId: null as number | null,
      teamName: null,
      teamTitle: '',
      players: [],
    };
  },
  computed: {
    key(): string | number | null {
      return this.isTeam ? this.teamName : this.playerId;
    },
    text(): string {
      return this.edit ? '編集' : '新規登録';
    },
  },
  watch: {
    historyId(_value: number) {
      if (_value) {
        axios.get(`/api/histories/${_value}`).then(res => {
          const json = res.data.response;
          this.teamTitle = json.isTeam || '';
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
  mounted() {
    this.teamTitle = this.isTeam || '';
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

      axios
        .post('/api/players', {
          name: this.name,
        })
        .then(res => {
          const players = res.data.response.results;
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
          const message = res.data.response.message;
          this.$store.dispatch('openDialog', {
            messages: message || '更新に失敗しました…。',
            type: 'error',
          });
        });
    },
    getName(_player: Player) {
      if (_player.nameOther) {
        return `${_player.name} [${_player.nameOther}]`;
      }
      return _player.name;
    },
    select(_player: Player) {
      this.playerId = _player.id || null;
      this.rankId = _player.rankId || null;
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
      this.teamTitle = this.isTeam || '';
      this.players = [];
      this.edit = false;
      this.$emit('cleared');
    },
  },
});
</script>
