<template>
    <ul class="search-header">
        <li class="search-row">
            <label>対象国：</label>
            <select class="country" v-model="select.country" @change="changeValue($event)">
                <option v-for="(country, idx) in countries" :key="idx"
                    :value="country.value" v-text="country.text"></option>
            </select>
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
    },
    mounted() {
        this.$http.get(`${this.domain}api/countries/`, { params: { 'has_title': '1' } })
            .then(res => {
                const countries = [];
                const json = res.body.response;
                json.forEach(obj => {
                    countries.push({
                        value: obj.id,
                        text: obj.name,
                    });
                });
                return countries;
            }).then(countries => {
                this.countries = countries;
                this.select = {
                    country: this.countries[0].value || '',
                };
            });
    },
}
</script>
