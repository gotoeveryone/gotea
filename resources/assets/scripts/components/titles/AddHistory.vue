<template>
  <ul class="boxes">
    <li class="label-row" v-text="text" />
    <li class="detail_box">
      <div class="detail_box_item box-2">
        <input :value="historyId" type="hidden" name="id" />
        <input :value="isTeamHidden ? 1 : 0" type="hidden" name="is_team" />
        <div class="input number">
          <label for="target-year">対象年度</label>
          <input
            id="target-year"
            v-model="year"
            type="number"
            maxlength="4"
            class="year"
            name="target_year"
          />
        </div>
      </div>
      <div class="detail_box_item box-2">
        <div class="input number">
          <label for="holding">期</label>
          <input v-model="holding" type="number" maxlength="4" class="holding" name="holding" />
        </div>
      </div>
      <div class="detail_box_item box-2">
        <div class="input number">
          <label for="acquired">取得日</label>
          <input
            id="acquired"
            v-model="acquired"
            type="date"
            class="acquired date-input"
            name="acquired"
          />
        </div>
      </div>
      <div class="detail_box_item box-2">
        <div class="input number">
          <label for="acquired">放映日</label>
          <input
            id="broadcasted"
            v-model="broadcasted"
            type="date"
            class="broadcasted date-input"
            name="broadcasted"
          />
        </div>
      </div>
      <div class="detail_box_item detail_box_item-buttons box-4">
        <div class="input checkbox">
          <input name="is_official" value="0" type="hidden" />
          <label class="checkbox-label" for="official">
            <input
              id="official"
              :checked="isOfficial"
              value="1"
              type="checkbox"
              class="official"
              name="is_official"
            />
            <span>公式戦</span>
          </label>
        </div>
        <div class="input checkbox">
          <input name="newest" value="0" type="hidden" />
          <label v-if="!edit" for="newest" class="checkbox-label">
            <input
              v-if="!edit"
              id="newest"
              :disabled="required"
              type="checkbox"
              name="newest"
              :checked="true"
            />
            最新として登録
          </label>
        </div>
        <div class="input">
          <button v-if="edit" type="button" @click="clearData()">編集をやめる</button>
          <button :disabled="required" type="submit" class="button button-primary">保存</button>
        </div>
      </div>
      <div v-if="isTeamHidden" class="detail_box_item">
        <div class="input text">
          <label for="win-group-name">優勝団体名</label>
          <input
            id="win-group-name"
            v-model="teamName"
            type="text"
            name="win_group_name"
            maxlength="30"
          />
        </div>
      </div>
      <div v-if="!isTeamHidden" class="detail_box_item box-3">
        <div class="input">
          <label>設定棋士名</label>
          <strong v-text="viewName" />
          <input v-model="playerId" type="hidden" name="player_id" />
          <input v-model="countryId" type="hidden" name="country_id" />
        </div>
      </div>
      <div v-if="!isTeamHidden" class="detail_box_item box-2">
        <div class="input">
          <label>設定棋士出場国</label>
          <select v-model="countryId" @change="changeCountry($event)">
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
      <li class="label-row">棋士検索</li>
      <li class="detail_box">
        <div class="detail_box_item box-3">
          <div class="input text">
            <label for="name">棋士名</label>
            <input id="name" v-model="name" type="text" class="name" />
          </div>
        </div>
        <div class="detail_box_item box-7" />
        <div class="detail_box_item detail_box_item-buttons box-2">
          <div class="input">
            <button type="button" class="button button-primary" @click="search()">検索</button>
          </div>
        </div>
      </li>
      <li v-if="players.length">
        <ul class="table-body retentions">
          <li v-for="(player, idx) in players" :key="idx" class="table-row">
            <span class="retentions-name" v-text="getName(player)" />
            <span class="retentions-name" v-text="player.nameEnglish" />
            <span class="retentions-country" v-text="player.countryName" />
            <span class="retentions-rank" v-text="player.rankName" />
            <span class="retentions-sex" v-text="player.sex" />
            <span class="retentions-select">
              <button class="button button-secondary" type="button" @click="select(player)">
                選択
              </button>
            </span>
          </li>
        </ul>
      </li>
    </template>
  </ul>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, toRefs, watch } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';

import { Player } from '@/types/titles';
import { Country } from '@/types';

const props = defineProps({
  isTeam: {
    type: Boolean,
    default: false,
  },
  historyId: {
    type: Number,
    default: null,
  },
});
const { isTeam, historyId } = toRefs(props);
const emit = defineEmits<{ cleared: [] }>();
const store = useStore();
const state = reactive({
  countries: [] as Country[],
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
  players: [] as Player[],
});
const {
  countries,
  edit,
  viewName,
  year,
  holding,
  acquired,
  broadcasted,
  isOfficial,
  name,
  playerId,
  countryId,
  teamName,
  isTeamHidden,
  players,
} = toRefs(state);
const key = computed(() => (props.isTeam ? state.teamName : state.playerId));
const text = computed(() => (state.edit ? '編集' : '新規登録'));
const required = computed(
  () => !(!!state.year && !!state.holding && !!state.acquired && !!key.value),
);
const initialViewName = '（検索エリアから棋士を検索してください。）';
const getTeamHidden = (value: boolean) => (value ? '1' : '');
const loadHistory = (value: number | null) => {
  if (value) {
    axios.get(`/api/histories/${value}`).then((res) => {
      const json = res.data.response;
      Object.assign(state, {
        isTeamHidden: getTeamHidden(json.isTeam),
        playerId: json.playerId,
        holding: json.holding,
        countryId: json.countryId,
        year: json.targetYear,
        acquired: json.acquired,
        isOfficial: json.isOfficial,
        broadcasted: json.broadcasted,
        viewName: json.winPlayerName,
        teamName: json.winGroupName,
        edit: true,
      });
    });
  }
};
watch(historyId, loadHistory);
onMounted(() => {
  state.viewName = initialViewName;
  state.isTeamHidden = getTeamHidden(props.isTeam);
  axios.get('/api/countries/').then((res) => (state.countries = res.data.response));
  loadHistory(props.historyId);
});
const changeCountry = ($event: Event) => {
  const target = $event.target as HTMLInputElement;
  state.countryId = Number(target.value);
};
const search = () => {
  if (state.name === '') {
    store.dispatch('openDialog', {
      messages: '棋士名は必須です。',
      type: 'error',
    });
    return;
  }

  axios
    .post('/api/players', {
      name: state.name,
    })
    .then((res) => {
      const players = res.data.response.results;
      switch (players.length) {
        case 0:
          store.dispatch('openDialog', {
            messages: '検索結果が0件でした。',
            type: 'warning',
          });
          break;
        case 1:
          select(players[0]);
          break;
        default:
          state.players = players;
          break;
      }
    })
    .catch((res) => {
      const message = res.data.response.message;
      store.dispatch('openDialog', {
        messages: message || '更新に失敗しました…。',
        type: 'error',
      });
    });
};
const getName = (player: Player) => {
  if (player.nameOther) {
    return `${player.name} [${player.nameOther}]`;
  }
  return player.name;
};
const select = (player: Player) => {
  state.playerId = player.id || null;
  state.countryId = player.countryId || null;
  state.viewName = `${player.name} ${player.rankName}`;
  state.name = '';
  state.players = [];
};
const clearData = () => {
  Object.assign(state, {
    viewName: initialViewName,
    isTeamHidden: getTeamHidden(props.isTeam),
    year: '',
    holding: '',
    acquired: '',
    isOfficial: true,
    broadcasted: '',
    name: '',
    playerId: null,
    countryId: null,
    teamName: null,
    players: [],
    edit: false,
  });
  emit('cleared');
};
</script>
