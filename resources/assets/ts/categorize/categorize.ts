import { Component, Input, Output, EventEmitter, ViewChild } from '@angular/core';
import { Http } from '@angular/http';
import { NgIf, NgFor, NgClass } from '@angular/common';
import { NgModel } from '@angular/forms';

/**
 * 段位別棋士数出力用コンポーネント
 */
@Component({
    selector: '[categorize]',
    template: `
        <ul categorize-header class="search-header" [countries]="countries" (onSearch)="onSearch($event)"></ul>
        <div categorize-body class="search-results" [rows]="rows"></div>
    `,
})
export class Categorize {
    countries: any[];
    rows = new Array();

    constructor(private http: Http) {
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
        ];
    }

    onSearch(_params: any) {
        this.http.get(`/igoapp/api/categorize/${_params.country}/`)
            .forEach((res) => {
                const json = res.json().response;
                this.rows = json.categories;
            });
    }
}

@Component({
    selector: '[categorize-header]',
    template: `
        <li class="search-row">
            <label>対象国：</label>
            <select class="country" ([ngModel])="selectCountry" (change)="changeCountry($event.target.value)">
                <option *ngFor="let country of countries" [value]="country.value" [innerText]="country.text"></option>
            </select>
        </li>
    `,
})
export class CategorizeHeader {
    @Input() countries: any[];
    @Output() onSearch = new EventEmitter<any>();

    selectCountry: string;

    changeCountry(country: string) {
        this.selectCountry = country;
        this.search();
    }

    search() {
        this.onSearch.emit({
            country: this.selectCountry,
        });
    }

    ngOnInit() {
        this.selectCountry = this.countries[0].value;
        this.search();
    }
}

@Component({
    selector: '[categorize-body]',
    template: `    
        <ul class="table-header">
            <li class="table-row">
                <span class="rank">段位</span>
                <span class="count">人数</span>
            </li>
        </ul>
        <ul class="table-body" *ngIf="rows.length">
            <li class="table-row" *ngFor="let row of rows">
                <span class="rank" [innerText]="row.rank.name"></span>
                <span class="count" [innerText]="count(row)"></span>
            </li>
        </ul>
    `,
})
export class CategorizeBody {
    @Input() rows: any[];

    count(_row: any): string {
        return `${_row.count}人`;
    }
}