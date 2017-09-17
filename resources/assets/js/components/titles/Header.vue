<template>
    <ul class="search-header">
        <li class="search-row">
            <label>対象国：</label>
            <select v-model="select.country" @change="changeValue($event)">
                <option v-for="(country, idx) in countries" :key="idx"
                    :value="country.value" v-text="country.text"></option>
            </select>
            <label>終了棋戦：</label>
            <select v-model="select.type" @change="changeValue($event)">
                <option v-for="(type, idx) in types" :key="idx"
                    :value="type.value" v-text="type.text"></option>
            </select>
            <div class="button-wrap">
                <button type="button" @click="add()">行追加</button>
                <button type="button" @click="json()">JSON出力</button>
            </div>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        domain: String,
    },
    data: () => {
        return {
            countries: [],
            select: {
                country: null,
                type: null,
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
        add() {
            this.$emit('add');
        },
        json() {
            this.$emit('json', this.select);
        },
    },
    mounted() {
        this.$http.get(`${this.domain}api/countries/`)
            .then(res => {
                const countries = [];
                const json = res.body.response;
                json.forEach(obj => {
                    countries.push({
                        value: obj.id,
                        text: `${obj.name}棋戦`,
                    });
                });
                return countries;
            }).then(countries => {
                this.countries = countries;
                this.select = {
                    country: this.countries[0].value || '',
                    type: this.types[0].value,
                };
                this.search();
            });
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
            ];
        },
    },
}
</script>
