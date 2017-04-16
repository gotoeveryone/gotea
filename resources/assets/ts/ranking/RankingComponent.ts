import { Component, ViewChildren, QueryList } from '@angular/core';
import { Http } from '@angular/http';
import { NgIf, NgFor, NgClass } from '@angular/common';
import { RankingHeaderComponent } from './RankingHeaderComponent';
import { RankingDataComponent } from './RankingDataComponent';
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
export class RankingComponent {
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
                text: '日本',
            },
            {
                value: '韓国',
                text: '韓国',
            },
            {
                value: '中国',
                text: '中国',
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
                alert('JSONつくった！');
                document.querySelector('.block-ui').classList.remove('blocked');
            });
    }
}