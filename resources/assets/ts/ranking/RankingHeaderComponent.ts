import { Component, EventEmitter, Input, Output } from '@angular/core';
import { NgIf, NgFor, NgClass } from '@angular/common';
import { NgModel } from '@angular/forms';

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
    `
})
export class RankingHeaderComponent {
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