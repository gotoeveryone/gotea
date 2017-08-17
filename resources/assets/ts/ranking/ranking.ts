import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Http } from '@angular/http';
import { NgIf, NgFor, NgClass } from '@angular/common';
import { NgModel } from '@angular/forms';
import { WEB_ROOT } from '../base';
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
    countries = this.getCountries();
    limits = this.getLimits();
    lastUpdate = '';
    rows = new Array();
    text = '';
    modal = {
        url: '',
        width: '0',
        height: '0',
    };

    constructor(private http: Http) { }

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

    private getLimits() {
        const limits = [];
        for (let l = 20; l <= 50; l = l + 10) {
            limits.push({
                value: l,
                text: `～${l}位`,
            });
        }
        return limits;
    }

    private async getCountries() {
        const countries = new Array();
        await this.http.get(`${WEB_ROOT}api/countries/`)
            .forEach((res) => {
                const json = res.json().response;
                json.forEach((obj: any) => {
                    countries.push({
                        value: obj.code,
                        text: `${obj.name}棋戦`,
                    });
                });
            });
        return countries;
    }

    onSearch(_params: any) {
        this.http.get(`${WEB_ROOT}api/rankings/${_params.country}/${_params.year}/${_params.limit}?withJa=1`)
            .forEach((res) => {
                const json = res.json().response;
                const dateObj = new Date(json.lastUpdate);
                this.lastUpdate = `${dateObj.getFullYear()}年${(dateObj.getMonth() + 1)}月${dateObj.getDate()}日`;
                this.rows = json.ranking;
            });
    }

    outputJson(_params: any) {
        this.http.get(`${WEB_ROOT}api/rankings/${_params.country}/${_params.year}/${_params.limit}?make=1`)
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
        this.modal.width = '60%';
        this.modal.height = '90%';
        this.modal.url = `${WEB_ROOT}/players/detail/${_id}`;
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
                <option *ngFor="let country of showCountries" [value]="country.value" [innerText]="country.text"></option>
            </select>
            <select ([ngModel])="selectLimit" (change)="changeLimit($event.target.value)">
                <option *ngFor="let limit of limits" [value]="limit.value" [innerText]="limit.text"></option>
            </select>
        </li>
        <li class="search-row">
            <label>最終更新日：</label>
            <span class="lastUpdate" [innerText]="lastUpdate"></span>
            <div class="button-wrap">
                <button type="button" (click)="json()">JSON出力</button>
            </div>
        </li>
    `,
})
export class RankingHeader {
    @Input() lastUpdate: string;
    @Input() years: any[];
    @Input() countries: Promise<any[]>;
    @Input() limits: any[];
    @Output() onSearch = new EventEmitter<any>();
    @Output() outputJson = new EventEmitter<any>();
    @Output() openDialog = new EventEmitter<any>();

    showCountries: any[];
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
        this.countries.then(obj => {
            this.showCountries = obj;
            this.selectYear = this.years[0].value;
            this.selectCountry = obj.length ? obj[0].value : '';
            this.selectLimit = this.limits[0].value;
            this.search();
        })
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
