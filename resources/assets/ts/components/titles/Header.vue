<template>
    <ul class="search-header">
        <li class="search-row">
            <div>
                <label class="search-row_label">対象国</label>
                <select class="titles-country" v-model="select.country" @change="changeValue($event)">
                    <option v-for="(country, idx) in countries" :key="idx" :value="country.value" v-text="country.text"></option>
                </select>
            </div>
            <div>
                <label class="search-row_label">終了棋戦</label>
                <select class="titles-closed" v-model="select.type" @change="changeValue($event)">
                    <option v-for="(type, idx) in types" :key="idx" :value="type.value" v-text="type.text"></option>
                </select>
            </div>
            <div class="button-wrap">
                <button type="button" @click="add()">行追加</button>
                <button type="button" @click="json()">JSON出力</button>
            </div>
        </li>
    </ul>
</template>

<script lang="ts">
import Vue from 'vue'
import axios from 'axios'

export default Vue.extend({
    data: () => {
        return {
            countries: Array(),
            select: {
                country: '',
                type: 0,
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
        add() {
            this.$emit('add', this.select)
        },
        json() {
            this.$emit('json', this.select)
        },
    },
    computed: {
        types() {
            return [
                {
                    value: 0,
                    text: '検索しない',
                },
                {
                    value: 1,
                    text: '検索する',
                },
            ]
        },
    },
    mounted() {
        axios.get('/api/countries/')
            .then(res => res.data.response.map((obj: any) => ({
                value: obj.id,
                text: `${obj.name}棋戦`,
            })))
            .then(countries => {
                this.countries = countries
                this.select = {
                    country: this.countries[0].value || '',
                    type: this.types[0].value,
                }
                this.search()
            })
    },
})
</script>
