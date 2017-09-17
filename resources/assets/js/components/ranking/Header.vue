<template>
    <ul class="search-header">
        <li class="search-row">
            <label>抽出対象：</label>
            <select v-model="select.year" name="year" @change="changeValue($event)">
                <option v-for="(year, idx) in years" :key="idx" :value="year.value" v-text="year.text"></option>
            </select>
            <select v-model="select.country" name="country" @change="changeValue($event)">
                <option v-for="(country, idx) in countries" :key="idx" :value="country.value" v-text="country.text"></option>
            </select>
            <select v-model="select.limit" name="limit" @change="changeValue($event)">
                <option v-for="(limit, idx) in limits" :key="idx" :value="limit.value" v-text="limit.text"></option>
            </select>
        </li>
        <li class="search-row">
            <label>最終更新日：</label>
            <span class="lastUpdate" v-text="lastUpdate"></span>
            <div class="button-wrap">
                <button type="button" @click="json()">JSON出力</button>
            </div>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        domain: String,
        lastUpdate: String,
    },
    data: () => {
        return {
            countries: [],
            years: [],
            select: {
                year: null,
                country: null,
                limit: null,
            },
        }
    },
    methods: {
        changeValue($event) {
            this.select[$event.target.name] = $event.target.value;
            this.search();
        },
        search() {
            this.$emit('search', this.select);
        },
        json() {
            this.$emit('json', this.select);
        },
    },
    mounted() {
        // 所属国
        Promise.all([
            this.$http.get(`${this.domain}api/countries`),
            this.$http.get(`${this.domain}api/years`),
        ]).then(data => {
            data[0].body.response.forEach(obj => {
                this.countries.push({
                    value: obj.code,
                    text: `${obj.name}棋戦`,
                });
            });

            data[1].body.response.forEach(obj => {
                this.years.push({
                    value: obj,
                    text: `${obj}年度`,
                });
            });
        }).then(() => {
            this.select = {
                year: this.years[0].value,
                country: this.countries[0].value || '',
                limit: this.limits[0].value,
            };
            this.search();
        });
    },
    computed: {
        limits() {
            const limits = [];
            for (let l = 20; l <= 50; l = l + 10) {
                limits.push({
                    value: l,
                    text: `～${l}位`,
                });
            }
            return limits;
        },
    },
}
</script>
