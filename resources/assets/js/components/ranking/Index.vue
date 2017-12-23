<template>
    <section class="ranking">
        <ranking-header :lastUpdate="lastUpdate" @search="onSearch" @json="outputJson"></ranking-header>
        <ranking-items :items="items"></ranking-items>
    </section>
</template>

<script>
import Header from './Header.vue';
import Items from './Items.vue';

export default {
    data: () => {
        return {
            lastUpdate: null,
            items: [],
        };
    },
    components: {
        rankingHeader: Header,
        rankingItems: Items,
    },
    methods: {
        onSearch(_params) {
            const params = {
                from: _params.from || '',
                to: _params.to || '',
            };

            this.$http.get(this.createUrl(_params), { params: params }).then(res => {
                const json = res.body.response;
                const dateObj = new Date(json.lastUpdate);
                this.lastUpdate = `${dateObj.getFullYear()}年${dateObj.getMonth() +
                    1}月${dateObj.getDate()}日`;
                this.items = json.ranking;
            });
        },
        outputJson(_params) {
            const params = {
                from: _params.from || '',
                to: _params.to || '',
            };

            this.$http
                .post(this.createUrl(_params), params)
                .then(() =>
                    this.$store.dispatch('openDialog', {
                        messages: 'JSONを出力しました。',
                    })
                )
                .catch(() =>
                    this.$store.dispatch('openDialog', {
                        messages: 'JSON出力に失敗しました…。',
                        type: 'error',
                    })
                );
        },
        createUrl(_params) {
            return `/api/players/ranking/${_params.country}/${_params.year}/${_params.limit}`;
        },
    },
};
</script>
