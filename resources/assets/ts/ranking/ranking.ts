import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Http } from '@angular/http';
import { NgIf, NgFor, NgClass } from '@angular/common';
import { NgModel } from '@angular/forms';

/**
 * ランキング出力用コンポーネント
 */
@Component({
    selector: '[ranking]',
    template: `
        <ul ranking-header class="search-header"
            [years]="years" [countries]="countries" [limits]="limits" [lastUpdate]="lastUpdate"
            (onSearch)="onSearch($event)" (outputJson)="outputJson($event)">
        </ul>
        <div ranking-results class="search-results" [rows]="rows"></div>
    `,
})
export class Ranking {
    years: any[];
    countries: any[];
    limits: any[];
    lastUpdate = '';
    rows = new Array();

    constructor(private http: Http) {
        this.years = [
            {
                value: 2016,
                text: "2016年度",
            },
            {
                value: 2017,
                text: "2017年度",
            },
        ];
        this.countries = [
            {
                value: '日本',
                text: '日本棋戦',
            },
            {
                value: '韓国',
                text: '韓国棋戦',
            },
            {
                value: '中国',
                text: '中国棋戦',
            },
            {
                value: '台湾',
                text: '台湾',
            },
            {
                value: '国際',
                text: '国際',
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

    onSearch(_params: any) {
        document.querySelector('.block-ui').classList.add('blocked');
        this.http.get(`/igoapp/api/rankings/${_params.country}/${_params.year}/${_params.limit}?jp=true`)
            .forEach((res) => {
                const json = res.json().response;
                const dateObj = new Date(json.lastUpdate);
                this.lastUpdate = `${dateObj.getFullYear()}年${(dateObj.getMonth() + 1)}月${dateObj.getDate()}日`;
                this.rows = json.ranking;
                document.querySelector('.block-ui').classList.remove('blocked');
            });
    }

    outputJson(_params: any) {
        document.querySelector('.block-ui').classList.add('blocked');
        this.http.get(`/igoapp/api/rankings/${_params.country}/${_params.year}/${_params.limit}?jp=true&make=true`)
            .forEach((res) => {
                alert('JSONを出力しました。');
                document.querySelector('.block-ui').classList.remove('blocked');
            });
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
            limit: this.selectLimit
        });
    }

    json() {
        this.outputJson.emit({
            year: this.selectYear,
            country: this.selectCountry,
            limit: this.selectLimit
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
                    <a class="colorbox" [href]="'/igoapp/players/detail/' + row.playerId">
                        <span [ngClass]="{'female': row.sex === '女性'}" [innerText]="row.playerNameJp"></span>
                    </a>
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
}
