/**
 * モーダル管理クラス
 */
export default class Modal {
    constructor() {
        this.modal = {
            url: '',
            height: '',
            width: '',
        };
    }

    get options() {
        return this.modal;
    }

    open(_options) {
        this.modal = _options;
    }

    close() {
        this.modal = {
            url: '',
            width: '',
            height: '',
        };
    }
}
