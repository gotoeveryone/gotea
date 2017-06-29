import Vue from 'vue/dist/vue.common';
import Modal from './components/Modal.vue';

new Vue({
    data: {
        modal: {
            url: '',
            height: '0',
            width: '0',
        },
    },
    components: {
        modal: Modal,
    },
    methods: {
        openModal(href, name) {
            // 指定があればパラメータ追加
            if (name) {
                const param = document.querySelector(`[name=${name}]`).value;
                href = `${href}?${name}=${param}`;
            }
            this.modal = {
                url: href,
                width: '60%',
                height: '90%',
            };
        },
        closeModal() {
            this.modal = {
                url: '',
                width: '0',
                height: '0',
            };
        },
    },
}).$mount('.players');
