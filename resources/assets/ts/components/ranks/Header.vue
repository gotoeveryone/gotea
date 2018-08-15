<template>
    <ul class="search-header">
        <li class="search-row">
            <fieldset class="search-box">
                <label class="search-box_label">対象国</label>
                <select class="country" v-model="select.country" @change="changeValue($event)">
                    <option v-for="(country, idx) in countries" :key="idx" :value="country.value" v-text="country.text"></option>
                </select>
            </fieldset>
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
    },
    mounted() {
        axios.get('/api/countries/', { params: { 'has_title': '1' } })
            .then(res => res.data.response.map((obj: any) => ({
                value: obj.id,
                text: obj.name,
            })))
            .then(countries => {
                this.countries = countries;
                this.select = {
                    country: this.countries[0].value || '',
                };
                this.search();
            })
    },
})
</script>
