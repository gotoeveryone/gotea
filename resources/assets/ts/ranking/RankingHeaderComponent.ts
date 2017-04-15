import { Component, Input } from '@angular/core';
import { Http } from '@angular/http';
import { NgIf, NgFor, NgClass } from '@angular/common';

/**
 * ランキング出力用コンポーネント
 */
@Component({
    selector: '[ranking-header]',
    template: `
        <li class="search-row">
            <label>抽出対象：</label>
            <select>
                <option *ngFor="let year of years" value="year.value" [innerText]="year.text"></option>
            </select>
            <select>
                <option *ngFor="let country of countries" value="country.value" [innerText]="country.text"></option>
            </select>
            <select>
                <option *ngFor="let limit of limits" value="limit.value" [innerText]="limit.text"></option>
            </select>
        </li>
        <li class="search-row">
            <label>最終更新日：</label>
            <span class="lastUpdate">{{lastUpdate}}</span>
            <div class="button-column">
                <button type="submit">検索</button>
                <button type="button" disabled>JSON出力</button>
            </div>
        </li>
    `
})
export class RankingHeaderComponent {
    @Input() lastUpdate: string;
    years = new Array(
        {
            value: 2016,
            text: "2016年度",
        },
        {
            value: 2017,
            text: "2017年度",
        },
    );
    countries = new Array(
        {
            value: 1,
            text: '日本',
        },
    )
    limits = new Array(
        {
            value: 20,
            text: '～20位',
        },
        {
            value: 30,
            text: '～30位',
        },
    )
    constructor(private http: Http) {
    }
}