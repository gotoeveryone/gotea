<template>
    <section class="titles">
        <title-header :domain="domain" @add="addRow" @search="onSearch" @json="outputJson"></title-header>
        <title-items :domain="domain" :detail-url="detailUrl" :items="items"></title-items>
    </section>
</template>

<script>
import Header from './Header.vue';
import Items from './Items.vue';

export default {
    props: {
        domain: String,
        detailUrl: String,
    },
    data: () => {
        return {
            items: [],
            countries: [],
            select: {
                year: null,
                country: null,
                limit: null,
            },
        }
    },
    components: {
        titleHeader: Header,
        titleItems: Items,
    },
    methods: {
        onSearch(_params) {
            const params = {
                'admin': '1',
                'withJa': '1',
                'country_id': _params.country,
                'is_closed': _params.type,
            };

            this.$http.get(this.getUrl(), { params: params })
                .then(res => {
                    this.items = res.body.response;
                });
        },
        addRow() {
            this.items.push({
                countryId: this.country,
                sortOrder: this.items.length,
            });
        },
        outputJson() {
            this.$http.get(this.getUrl(), { params: {'make': '1'} })
                .then(res => this.$store.dispatch('openDialog', {
                    messages: 'JSONを出力しました。',
                })).catch(res => this.$store.dispatch('openDialog', {
                    messages: 'JSON出力に失敗しました…。',
                    error: true,
                }));
        },
        changeValue($event) {
            this.select[$event.target.name] = $event.target.value;
            this.search();
        },
        getUrl() {
            return `${this.domain}api/news/`;
        },
    },
}
</script>
