import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Http } from '@angular/http';
import { NgIf, NgFor, NgClass } from '@angular/common';
import { NgModel } from '@angular/forms';
import { Colorbox } from '../components/colorbox';

/**
 * ランキング出力用コンポーネント
 */
@Component({
    selector: '[ranking]',
    template: `
        <ul ranking-header class="search-header"
            [years]="years" [countries]="countries" [limits]="limits" [lastUpdate]="lastUpdate"
            (onSearch)="onSearch($event)" (outputJson)="outputJson($event)" (openDialog)="openDialog($event)">
        </ul>
        <div ranking-results class="search-results" [rows]="rows" (onSelect)="onSelect($event)"></div>
        <colorbox class="iframe-modal" [url]="modal.url" [class.hide]="!modal.url"
            (click)="onModalClose($event)" (onClose)="onModalClose($event)"
            [height]="modal.height" [width]="modal.width"></colorbox>
        <dialog class="dialog" [class.hide]="!text" [text]="text" (onClose)="onDialogClose($event)"></dialog>
    `,
})
export class Ranking {
    years = this.getYears();
    countries: any[];
    limits: any[];
    lastUpdate = '';
    rows = new Array();
    text = '';
    modal = {
        url: '',
        width: '0',
        height: '0',
    };

    constructor(private http: Http) {
        this.countries = [
            {
                value: '日本',
                text: '日本棋士',
            },
            {
                value: '韓国',
                text: '韓国棋士',
            },
            {
                value: '中国',
                text: '中国棋士',
            },
            {
                value: '台湾',
                text: '台湾棋士',
            },
            {
                value: '国際',
                text: '国際棋戦',
            },
        ];
        this.limits = [
            {
                value: 20,
                text: '～20位',
            },
            {
                value: 30,
                text: '～30位',
            },
            {
                value: 40,
                text: '～40位',
            },
            {
                value: 50,
                text: '～50位',
            },
            {
                value: 60,
                text: '～60位',
            },
        ];
    }

    private getYears() {
        const nowYear = new Date().getFullYear();
        const years = [];
        for (let y = nowYear; y >= 2013; y--) {
            years.push({
                value: y,
                text: `${y}年度`,
            });
        }
        return years;
    }

    private getCountries() {
        this.http.get(`/igoapp/api/countries/`)
            .forEach((res) => {
                const json = res.json().response;
                this.rows = json.ranking;
            });
    }

    onSearch(_params: any) {
        this.http.get(`/igoapp/api/rankings/${_params.country}/${_params.year}/${_params.limit}?jp=true`)
            .forEach((res) => {
                const json = res.json().response;
                const dateObj = new Date(json.lastUpdate);
                this.lastUpdate = `${dateObj.getFullYear()}年${(dateObj.getMonth() + 1)}月${dateObj.getDate()}日`;
                this.rows = json.ranking;
            });
    }

    outputJson(_params: any) {
        this.http.get(`/igoapp/api/rankings/${_params.country}/${_params.year}/${_params.limit}?jp=true&make=true`)
            .forEach((res) => {
                this.openDialog('JSONを出力しました。');
            });
    }

    openDialog(_text: string) {
        this.text = _text;
    }

    onDialogClose() {
        this.text = '';
    }

    onSelect(_id: number) {
        this.modal.width = '80%';
        this.modal.height = '90%';
        this.modal.url = `/igoapp/players/detail/${_id}`;
    }

    onModalClose() {
        this.modal.width = '0';
        this.modal.height = '0';
        this.modal.url = '';
    }
}

/**
 * ランキングヘッダ出力用コンポーネント
 */
@Component({
    selector: '[ranking-header]',
    template: `
        <li class="search-row">
            <label>抽出対象：</label>
            <select ([ngModel])="selectYear" (change)="changeYear($event.target.value)">
                <option *ngFor="let year of years" [value]="year.value" [innerText]="year.text"></option>
            </select>
            <select ([ngModel])="selectCountry" (change)="changeCountry($event.target.value)">
                <option *ngFor="let country of countries" [value]="country.value" [innerText]="country.text"></option>
            </select>
            <select ([ngModel])="selectLimit" (change)="changeLimit($event.target.value)">
                <option *ngFor="let limit of limits" [value]="limit.value" [innerText]="limit.text"></option>
            </select>
        </li>
        <li class="search-row">
            <label>最終更新日：</label>
            <span class="lastUpdate" [innerText]="lastUpdate"></span>
            <div class="button-column">
                <button type="button" (click)="json()">JSON出力</button>
            </div>
        </li>
    `,
})
export class RankingHeader {
    @Input() lastUpdate: string;
    @Input() years: any[];
    @Input() countries: any[];
    @Input() limits: any[];
    @Output() onSearch = new EventEmitter<any>();
    @Output() outputJson = new EventEmitter<any>();
    @Output() openDialog = new EventEmitter<any>();

    selectCountry: string;
    selectYear: number;
    selectLimit: number;

    changeCountry(country: string) {
        this.selectCountry = country;
        this.search();
    }

    changeYear(year: number) {
        this.selectYear = year;
        this.search();
    }

    changeLimit(limit: number) {
        this.selectLimit = limit;
        this.search();
    }

    search() {
        this.onSearch.emit({
            year: this.selectYear,
            country: this.selectCountry,
            limit: this.selectLimit,
        });
    }

    json() {
        this.outputJson.emit({
            year: this.selectYear,
            country: this.selectCountry,
            limit: this.selectLimit,
        });
    }

    ngOnInit() {
        this.selectYear = this.years[0].value;
        this.selectCountry = this.countries[0].value;
        this.selectLimit = this.limits[0].value;
        this.search();
    }
}

/**
 * ランキングデータ出力用コンポーネント
 */
@Component({
    selector: '[ranking-results]',
    template: `
        <ul class="table-header">
            <li class="table-row">
                <span class="no">No.</span>
                <span class="player">棋士名</span>
                <span class="point">勝</span>
                <span class="point">敗</span>
                <span class="point">分</span>
                <span class="percent">勝率</span>
            </li>
        </ul>
        <ul class="table-body" *ngIf="rows.length">
            <li class="table-row" *ngFor="let row of rows; let idx = index">
                <span class="right no">
                    <span [innerText]="getRank(idx, row)"></span>
                </span>
                <span class="left player">
                    <a class="player-link" [ngClass]="getSexClass(row)" (click)="select(row)" [innerText]="row.playerNameJp"></a>
                </span>
                <span class="point" [innerText]="row.winPoint"></span>
                <span class="point" [innerText]="row.losePoint"></span>
                <span class="point" [innerText]="row.drawPoint"></span>
                <span class="percent" [innerText]="getWinPercentage(row)"></span>
            </li>
        </ul>
    `,
})
export class RankingBody {
    @Input() rows: any[];
    @Output() onSelect = new EventEmitter<any>();
    getRank(_idx: number, _row: any): string {
        if (this.rows[_idx - 1]) {
            const beforeRank = this.rows[_idx - 1].rank;
            return (_row.rank === beforeRank) ? '' : `${_row.rank}`;
        }
        return _row.rank;
    }
    getWinPercentage(_row: any): string {
        return `${Math.round(_row.winPercentage * 100)}%`;
    }
    getSexClass(_row: any): string {
        return (_row.sex === '女性' ? 'female' : 'male');
    }
    select(_row: any) {
        this.onSelect.emit(_row.playerId);
    }
}
