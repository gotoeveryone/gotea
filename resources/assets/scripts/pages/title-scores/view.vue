<template>
  <div class="detail-dialog">
    <ul data-selecttab="" class="tabs">
      <li data-tabname="score" class="tab selectTab">
        成績詳細
      </li>
    </ul>
    <div class="detail">
      <section data-contentclass="tab-contents">
        <form method="post" accept-charset="utf-8" :action="submitPath" class="main-form">
          <div class="page-header">
            成績詳細 (ID: {{ id }})
          </div>
          <ul class="detail_box">
            <li class="detail_box_item box-3">
              <div class="input text required">
                <label for="started" class="label-row">対局日（FROM）</label>
                <input id="started" :value="item.started" type="text" autocomplete="off" required="required" class="input-row datepicker" @change="onChangeStarted">
              </div>
            </li>
            <li class="detail_box_item box-3">
              <div class="input">
                <div class="label-row">
                  対局日（TO）
                </div>
                <div class="input checkbox-with-text-field-row">
                  <div class="input checkbox">
                    <label for="is-same-started" class="checkbox-label">
                      <input id="is-same-started" v-model="item.isSameStarted" type="checkbox">
                      <span class="text">開始日と同じ</span>
                    </label>
                  </div>
                  <input :value="item.ended" type="text" autocomplete="off" required="required" class="datepicker" :disabled="item.isSameStarted" @change="onChangeEnded">
                </div>
              </div>
            </li>
            <li class="detail_box_item box-4">
              <div class="input select">
                <label for="country-id" class="label-row">所属国</label>
                <select id="country-id" v-model="item.countryId" data-id="country" class="input-row">
                  <template v-for="country in countries">
                    <option :key="country.id" :value="country.id" v-text="country.name" />
                  </template>
                </select>
              </div>
            </li>
            <li class="detail_box_item box-1">
              <div class="input">
                <label for="is-world" class="label-row">国際棋戦</label>
                <div class="input checkbox">
                  <input id="is-world" v-model="item.isWorld" type="checkbox" class="input-row">
                </div>
              </div>
            </li>
            <li class="detail_box_item box-1">
              <div class="input">
                <label for="is-official" class="label-row">公式戦</label>
                <div class="input checkbox">
                  <input id="is-official" v-model="item.isOfficial" type="checkbox" checked="checked" class="input-row">
                </div>
              </div>
            </li>
            <li class="detail_box_item box-3">
              <div class="input select">
                <label for="title-id" class="label-row">タイトル</label>
                <select id="title-id" v-model="item.titleId" class="input-row">
                  <option value="" />
                  <template v-for="title in titles">
                    <option :key="title.id" :value="title.id" v-text="title.name" />
                  </template>
                </select>
              </div>
            </li>
            <li class="detail_box_item box-5">
              <div class="input text">
                <label for="name" class="label-row">タイトル名</label>
                <input id="name" v-model="item.name" type="text" maxlength="100" class="input-row">
              </div>
            </li>
            <li class="detail_box_item box-4">
              <div class="input">
                <div class="label-row">
                  最終更新日時
                </div>
                <div class="input-row">
                  <span v-text="modifiedLabel" />
                </div>
              </div>
            </li>
            <li class="detail_box_item box-3">
              <div class="input">
                <div class="label-row">
                  勝者
                </div>
                <div v-if="winner" class="input-row">
                  <span v-text="winner.id" />: <span v-text="winner.name" />
                </div>
              </div>
            </li>
            <li class="detail_box_item box-3">
              <div class="input">
                <div class="label-row">
                  敗者
                </div>
                <div v-if="loser" class="input-row">
                  <span v-text="loser.id" />: <span v-text="loser.name" />
                </div>
              </div>
            </li>
            <li class="detail_box_item box-6">
              <div class="input text">
                <label for="result" class="label-row">結果</label>
                <input id="result" v-model="item.result" type="text" maxlength="30" class="input-row">
              </div>
            </li>
            <li class="detail_box_item button-row">
              <button type="button" class="button button-primary" @click="save">
                保存
              </button>
              <button type="button" class="button button-secondary" @click="switchDivision">
                勝敗変更
              </button>
            </li>
          </ul>
        </form>
      </section>
    </div>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import axios from 'axios';

import {
  Country,
  CountryResponse,
} from '@/types/country';
import {
  TitleResponse,
  TitleResultItem,
} from '@/types/titles';
import {
  Player,
  TitleScore as Item,
  TitleScoreResponse as Response,
} from '@/types/title-score';
import dayjs from 'dayjs';

export default Vue.extend({
  props: {
    id: {
      type: Number,
      default: null,
    },
  },
  data: () => ({
    hide: false,
    countries: [] as Country[],
    titles: [] as TitleResultItem[],
    item: {} as Item,
  }),
  computed: {
    submitPath(): string {
      return `/title-scores/${this.id}`;
    },
    winner(): Player | null {
      return this.item.winner;
    },
    loser(): Player | null {
      return this.item.loser;
    },
    modifiedLabel(): string {
      if (!this.item.modified) {
        return '';
      }
      return dayjs(this.item.modified * 1000).format('YYYY年MM月DD日 HH時mm分ss秒');
    },
  },
  mounted() {
    this.onLoad();
  },
  methods: {
    onChangeStarted($event: Event) {
      const target = $event.target as HTMLInputElement;
      if (this.item.started !== target.value) {
        this.item.started = target.value;
      }
    },
    onChangeEnded($event: Event) {
      const target = $event.target as HTMLInputElement;
      if (this.item.ended !== target.value) {
        this.item.ended = target.value;
      }
    },
    async onLoad() {
      return Promise.resolve([
        axios.get<CountryResponse>('/api/countries')
          .then(res => res.data)
          .then(({ response: item }) => {
            this.countries = item;
          }),
        axios.get<TitleResponse>('/api/titles')
          .then(res => res.data)
          .then(({ response: item }) => {
            this.titles = item;
          }),
        axios.get<Response>(`/api/title-scores/${this.id}`)
          .then(res => res.data)
          .then(({ response: item }) => {
            this.item = item;
          }),
      ]);
    },
    async save() {
      const {
        id,
        countryId: country_id,
        titleId: title_id,
        name,
        title,
        result,
        started,
        ended,
        isWorld: is_world,
        isOfficial: is_official,
        isSameStarted: is_same_started,
      } = this.item;

      return axios.put<Response>(`/api/title-scores/${this.id}`, {
        id,
        country_id,
        title_id,
        name,
        title,
        result,
        started,
        ended,
        is_world,
        is_official,
        is_same_started,
      })
        .then(res => res.data)
        .then(({ response: item }) => {
          this.item = item;
        })
        .catch((res) => {
          const { message } = res.data.response;
          this.$store.dispatch('openDialog', {
            messages: Object.values(message).map<string[]>(Object.values).reduce((a, b) => a.concat(b)) || '更新に失敗しました…。',
            type: 'error',
          });
        });
    },
    async switchDivision() {
      return axios.put<Response>(`/api/title-scores/${this.id}/switch-division`)
        .then(res => res.data)
        .then(({ response: item }) => {
          this.item = item;
        })
        .then(() => {
          this.$store.dispatch('openDialog', {
            messages: `ID【${this.item.id}】の勝敗を変更しました。`,
            type: 'info',
          });
        })
        .catch((res) => {
          const { message } = res.data.response;
          this.$store.dispatch('openDialog', {
            messages: Object.values(message).map<string[]>(Object.values).reduce((a, b) => a.concat(b)) || '更新に失敗しました…。',
            type: 'error',
          });
        });
    },
  },
});
</script>
