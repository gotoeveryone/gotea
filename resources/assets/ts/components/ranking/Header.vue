<template>
    <ul class="search-header">
        <li class="search-row">
            <fieldset class="search-box">
                <label class="search-box_label ranking_label">抽出対象</label>
                <select v-model="select.year" name="year" class="ranking_year" @change="changeValue($event)">
                    <option v-for="(year, idx) in years" :key="idx" :value="year.value" v-text="year.text"></option>
                </select>
                <select v-model="select.country" name="country" class="ranking_country" @change="changeValue($event)">
                    <option v-for="(country, idx) in countries" :key="idx" :value="country.value" v-text="country.text"></option>
                </select>
                <select v-model="select.limit" name="limit" class="ranking_limit" @change="changeValue($event)">
                    <option v-for="(limit, idx) in limits" :key="idx" :value="limit.value" v-text="limit.text"></option>
                </select>
            </fieldset>
        </li>
        <li class="search-row">
            <fieldset class="search-box">
                <label class="search-box_label ranking_label">対局日</label>
                <input type="text" name="from" class="ranking_date datepicker"
                    v-model="select.from" @change="changeValue($event)" :disabled="!useInputDate()">
                <span class="ranking_date-duration">～</span>
                <input type="text" name="to" class="ranking_date datepicker"
                    v-model="select.to" @change="changeValue($event)" :disabled="!useInputDate()">
            </fieldset>
        </li>
        <li class="search-row">
            <fieldset class="search-box">
                <label class="search-box_label ranking_label">最終更新日</label>
                <span class="lastUpdate" v-text="lastUpdate"></span>
            </fieldset>
            <fieldset class="search-box search-box-right">
                <button type="button" @click="clearDate()">日付をクリア</button>
                <button type="button" @click="json()" class="button button-primary">JSON出力</button>
            </fieldset>
        </li>
    </ul>
</template>

<script lang="ts">
import Vue from 'vue'
import axios from 'axios'

import Header from './Header.vue'
import Items from './Items.vue'

export default Vue.extend({
    props: {
        lastUpdate: String,
    },
    data: () => {
        return {
            countries: Array(),
            years: Array(),
            select: {
                year: '',
                country: '',
                limit: 0,
                from: '',
                to: '',
            },
        }
    },
    methods: {
        changeValue($event: any) {
            this.select[$event.target.name] = $event.target.value
            this.search()
        },
        search() {
            this.$emit('search', this.select)
        },
        clearDate() {
            this.select.from = ''
            this.select.to = ''
            this.search()
        },
        json() {
            this.$emit('json', this.select)
        },
        useInputDate() {
            const selected = this.years.find(
                y => y.value === parseInt(this.select.year, 10)
            )
            return selected ? !selected.old : false
        },
    },
    mounted() {
        // 所属国
        Promise.all([
            axios.get('/api/countries'),
            axios.get('/api/years'),
        ])
            .then(res => {
                this.countries = res[0].data.response.map((obj: any) => ({
                    value: obj.code,
                    text: `${obj.name}棋戦`,
                }))
                this.years = res[1].data.response.map((obj: any) => ({
                    value: obj.year,
                    text: `${obj.year}年度`,
                    old: obj.old,
                }))
            })
            .then(() => {
                this.select.year = this.years[0].value,
                this.select.country = this.countries[0].value || '',
                this.select.limit = this.limits[0].value,
                this.search()
            })
    },
    computed: {
        limits() {
            const limits = []
            for (let l = 20; l <= 50; l = l + 10) {
                limits.push({
                    value: l,
                    text: `～${l}位`,
                })
            }
            return limits
        },
    },
})
</script>
