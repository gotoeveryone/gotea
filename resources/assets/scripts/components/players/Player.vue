<template>
  <section data-contentname="player" class="tab-contents">
    <form v-if="player" class="main-form">
      <div class="page-header">
        棋士情報
        <span v-text="` (ID: ${player.id})`" />
      </div>
      <ul class="detail_box">
        <li class="detail_box_item box-4">
          <my-text :disabled="!isAdmin" :maxlength="20" :value="player.name" @change="onChange" name="name" label="名前" />
        </li>
        <li class="detail_box_item box-4">
          <my-text :disabled="!isAdmin" :maxlength="20" name="name_english" label="名前（英語）" />
        </li>
        <li class="detail_box_item box-4">
          <my-text :disabled="!isAdmin" :maxlength="20" name="name_other" label="名前（その他）" />
        </li>
        <li class="detail_box_item box-3">
          <my-text :disabled="!isAdmin" name="birthday" label="生年月日" input-class="birthday datepicker" autocomplete="off" />
          <span v-text="player.age" class="age" />
        </li>
        <li class="detail_box_item box-3">
          <my-select :disabled="!isAdmin" :required="true" :options="sexes" name="sex" label="性別" />
        </li>
        <li class="detail_box_item box-3">
          <my-select :disabled="!isAdmin" :required="true" :options="ranks" name="rank" label="段位" />
        </li>
        <li class="detail_box_item box-3">
          <my-select :disabled="!isAdmin" :required="true" :options="sexes" name="sex" label="性別" />
        </li>
        <li class="detail_box_item box-2">
          <my-select :disabled="!isAdmin" :required="true" :options="countries" name="country" label="所属国" />
        </li>
        <li class="detail_box_item box-3">
          <my-select :disabled="!isAdmin" :required="true" :options="organizations" name="organization" label="所属組織" />
        </li>
        <li class="detail_box_item box-3">
          <div class="input">
            <div class="label-row">
              引退
            </div>
            <my-text :disabled="!isAdmin" name="birthday" label="引退" input-class="birthday datepicker" autocomplete="off" />
          </div>
        </li>
        <li class="detail_box_item box-4">
          <div class="input">
            <div class="label-row">
              最終更新日時
            </div>
            <div class="label-field-row">
              <span v-text="player.modified" />
            </div>
          </div>
        </li>
        <li class="detail_box_item">
          <my-textarea :disabled="!isAdmin" name="remarks" label="その他備考" />
        </li>
        <li v-if="isAdmin" class="detail_box_item button-row">
          <button type="button" class="button button-primary">
            続けて登録
          </button>
          <button type="submit" class="button button-primary">
            保存
          </button>
        </li>
      </ul>
    </form>
  </section>
</template>

<script lang="ts">
import axios from 'axios';
import Vue from 'vue';
import MySelect from '@/components/parts/MySelect.vue';
import MyText from '@/components/parts/MyText.vue';
import MyTextarea from '@/components/parts/MyTextarea.vue';

interface Player {
  id: number;
  name: string;
  nameEnglish: string;
  nameOther: string | null;
  sex: string;
  countryId: number;
  organizationId: number;
  rankId: number;
  isRetired: false;
  retired: string | null;
}

export default Vue.extend({
  components: {
    MySelect,
    MyText,
    MyTextarea,
  },
  props: {
    isAdmin: {
      type: Boolean,
      default: false,
    },
    id: {
      type: Number,
      required: true,
    },
  },
  data: () => ({
    player: null as Player | null,
    countries: [],
    ranks: [],
    organizations: [],
  }),
  computed: {
    sexes() {
      return [
        { value: '男性', label: '男性' },
        { value: '女性', label: '女性' },
      ];
    },
  },
  mounted() {
    Promise.all([
      axios.get('/api/countries')
        .then(res => res.data)
        .then(data => data.response)
        .then((response) => {
          this.countries = response.map(c => ({
            value: c.id,
            label: c.name,
          }));
        }),
      axios.get('/api/ranks')
        .then(res => res.data)
        .then(data => data.response)
        .then((response) => {
          this.ranks = response.map(c => ({
            value: c.id,
            label: c.name,
          }));
        }),
      axios.get('/api/organizations')
        .then(res => res.data)
        .then(data => data.response)
        .then((response) => {
          this.organizations = response.map(c => ({
            value: c.id,
            label: c.name,
          }));
        }),
      axios.get(`/api/players/${this.id}`)
        .then(res => res.data)
        .then(data => data.response)
        .then((response) => {
          this.player = response;
        }),
    ]);
  },
  methods: {
    onChange(name: string, value: string) {
      (this.player as Player)[name] = value;
    },
  },
});
</script>
