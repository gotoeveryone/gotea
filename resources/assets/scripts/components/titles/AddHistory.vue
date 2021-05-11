<template>
  <ul class="boxes">
    <li
      class="label-row"
      v-text="text"
    />
    <li class="detail_box">
      <div class="detail_box_item box-2">
        <input
          :value="historyId"
          type="hidden"
          name="id"
        >
        <input
          :value="isTeamHidden ? 1 : 0"
          type="hidden"
          name="is_team"
        >
        <div class="input number">
          <label for="target-year">対象年度</label>
          <input
            id="target-year"
            v-model="year"
            type="number"
            maxlength="4"
            class="year"
            name="target_year"
          >
        </div>
      </div>
      <div class="detail_box_item box-2">
        <div class="input number">
          <label for="holding">期</label>
          <input
            v-model="holding"
            type="number"
            maxlength="4"
            class="holding"
            name="holding"
          >
        </div>
      </div>
      <div class="detail_box_item box-2">
        <div class="input number">
          <label for="acquired">取得日</label>
          <input
            id="acquired"
            v-model="acquired"
            type="text"
            maxlength="4"
            class="acquired datepicker"
            autocomplete="off"
            name="acquired"
            @change="onChangeAcquired"
          >
        </div>
      </div>
      <div class="detail_box_item box-2">
        <div class="input number">
          <label for="acquired">放映日</label>
          <input
            id="broadcasted"
            v-model="broadcasted"
            type="text"
            maxlength="4"
            class="broadcasted datepicker"
            autocomplete="off"
            name="broadcasted"
            @change="onChangeBroadcasted"
          >
        </div>
      </div>
      <div class="detail_box_item detail_box_item-buttons box-4">
        <div class="input checkbox">
          <input
            name="is_official"
            value="0"
            type="hidden"
          >
          <label
            class="checkbox-label"
            for="official"
          >
            <input
              id="official"
              :checked="isOfficial"
              value="1"
              type="checkbox"
              class="official"
              name="is_official"
            >
            <span>公式戦</span>
          </label>
        </div>
        <div class="input checkbox">
          <input
            name="newest"
            value="0"
            type="hidden"
          >
          <label
            v-if="!edit"
            for="newest"
            class="checkbox-label"
          >
            <input
              v-if="!edit"
              id="newest"
              :disabled="required"
              type="checkbox"
              name="newest"
              checked="checked"
            >
            最新として登録
          </label>
        </div>
        <div class="input">
          <button
            v-if="edit"
            type="button"
            @click="clearData()"
          >
            編集をやめる
          </button>
          <button
            :disabled="required"
            type="submit"
            class="button button-primary"
          >
            保存
          </button>
        </div>
      </div>
      <div
        v-if="isTeamHidden"
        class="detail_box_item"
      >
        <div class="input text">
          <label for="win-group-name">優勝団体名</label>
          <input
            id="win-group-name"
            v-model="teamName"
            type="text"
            name="win_group_name"
            maxlength="30"
          >
        </div>
      </div>
      <div
        v-if="!isTeamHidden"
        class="detail_box_item box-3"
      >
        <div class="input">
          <label>設定棋士名</label>
          <strong v-text="viewName" />
          <input
            v-model="playerId"
            type="hidden"
            name="player_id"
          >
          <input
            v-model="countryId"
            type="hidden"
            name="country_id"
          >
        </div>
      </div>
      <div
        v-if="!isTeamHidden"
        class="detail_box_item box-2"
      >
        <div class="input">
          <label>設定棋士出場国</label>
          <select
            v-model="countryId"
            @change="changeCountry($event)"
          >
            <option
              v-for="country in countries"
              :key="country.id"
              :value="country.id"
              v-text="country.name"
            />
          </select>
        </div>
      </div>
      <div class="detail_box_item box-7" />
    </li>
    <template v-if="!isTeamHidden">
      <li class="label-row">
        棋士検索
      </li>
      <li class="detail_box">
        <div class="detail_box_item box-3">
          <div class="input text">
            <label for="name">棋士名</label>
            <input
              id="name"
              v-model="name"
              type="text"
              class="name"
            >
          </div>
        </div>
        <div class="detail_box_item box-7" />
        <div class="detail_box_item detail_box_item-buttons box-2">
          <div class="input">
            <button
              type="button"
              class="button button-primary"
              @click="search()"
            >
              検索
            </button>
          </div>
        </div>
      </li>
      <li v-if="players.length">
        <ul class="table-body retentions">
          <li
            v-for="(player, idx) in players"
            :key="idx"
            class="table-row"
          >
            <span
              class="retentions-name"
              v-text="getName(player)"
            />
            <span
              class="retentions-name"
              v-text="player.nameEnglish"
            />
            <span
              class="retentions-country"
              v-text="player.countryName"
            />
            <span
              class="retentions-rank"
              v-text="player.rankName"
            />
            <span
              class="retentions-sex"
              v-text="player.sex"
            />
            <span class="retentions-select">
              <button
                class="button button-secondary"
                type="button"
                @click="select(player)"
              >選択</button>
            </span>
          </li>
        </ul>
      </li>
    </template>
  </ul>
</template>

<script lang="ts">
import Vue from 'vue';
import axios from 'axios';

import { Player } from '@/types/titles';

export default Vue.extend({
  props: {
    isTeam: {
      type: Boolean,
      default: false,
    },
    historyId: {
      type: Number,
      default: null,
    },
  },
  data: () => {
    return {
      countries: [],
      edit: false,
      viewName: '',
      year: '' as number | string,
      holding: '' as number | string,
      acquired: '',
      broadcasted: '',
      isOfficial: true,
      name: '',
      playerId: null as number | null,
      countryId: null as number | null,
      teamName: null,
      isTeamHidden: '',
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
    required(): boolean {
      return !(!!this.year && !!this.holding && !!this.acquired && !!this.key);
    },
    initialViewName(): string {
      return '（検索エリアから棋士を検索してください。）';
    },
  },
  watch: {
    historyId(_value: number) {
      if (_value) {
        axios.get(`/api/histories/${_value}`).then(res => {
          const json = res.data.response;
          this.isTeamHidden = this.getTeamHidden(json.isTeam);
          this.playerId = json.playerId;
          this.holding = json.holding;
          this.countryId = json.countryId;
          this.year = json.targetYear;
          this.acquired = json.acquired;
          this.isOfficial = json.isOfficial;
          this.broadcasted = json.broadcasted;
          this.viewName = json.winPlayerName;
          this.teamName = json.winGroupName;
          this.edit = true;
        });
      }
    },
  },
  mounted() {
    this.viewName = this.initialViewName;
    this.isTeamHidden = this.getTeamHidden(this.isTeam);
    axios.get('/api/countries/').then(res => (this.countries = res.data.response));
  },
  methods: {
    changeCountry($event: Event) {
      const target = $event.target as HTMLInputElement;
      this.countryId = Number(target.value);
    },
    getTeamHidden(value: boolean) {
      return value ? '1' : '';
    },
    onChangeAcquired($event: Event) {
      const target = $event.target as HTMLInputElement;
      if (this.acquired !== target.value) {
        this.acquired = target.value;
      }
    },
    onChangeBroadcasted($event: Event) {
      const target = $event.target as HTMLInputElement;
      if (this.broadcasted !== target.value) {
        this.broadcasted = target.value;
      }
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
    getName(player: Player) {
      if (player.nameOther) {
        return `${player.name} [${player.nameOther}]`;
      }
      return player.name;
    },
    select(player: Player) {
      this.playerId = player.id || null;
      this.countryId = player.countryId || null;
      this.viewName = `${player.name} ${player.rankName}`;
      this.name = '';
      this.players = [];
    },
    clearData() {
      this.viewName = this.initialViewName;
      this.isTeamHidden = this.getTeamHidden(this.isTeam);
      this.year = '';
      this.holding = '';
      this.acquired = '';
      this.isOfficial = true;
      this.broadcasted = '';
      this.name = '';
      this.playerId = null;
      this.countryId = null;
      this.teamName = null;
      this.players = [];
      this.edit = false;
      this.$emit('cleared');
    },
  },
});
</script>
