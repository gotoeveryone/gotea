import { Component, Input, Output, EventEmitter, ViewChild } from '@angular/core';
import { Http } from '@angular/http';
import { NgIf, NgFor, NgClass } from '@angular/common';
import { NgModel } from '@angular/forms';
import { WEB_ROOT } from '../base';

/**
 * 段位別棋士数出力用コンポーネント
 */
@Component({
    selector: '[ranks]',
    template: `
        <ul ranks-header class="search-header" [countries]="countries" (onSearch)="onSearch($event)"></ul>
        <div ranks-body class="search-results" [rows]="rows"></div>
    `,
})
export class Ranks {
    countries = this.getCountries();
    rows = new Array();

    constructor(private http: Http) { }

    private async getCountries() {
        const countries = new Array();
        await this.http.get(`${WEB_ROOT}api/countries/?has_title=true`)
            .forEach((res) => {
                const json = res.json().response;
                json.forEach((obj: any) => {
                    countries.push({
                        value: obj.id,
                        text: obj.name,
                    });
                });
            });
        return countries;
    }

    onSearch(_params: any) {
        this.http.get(`${WEB_ROOT}api/ranks/${_params.country}/`)
            .forEach((res) => {
                this.rows = res.json().response;
            });
    }
}

@Component({
    selector: '[ranks-header]',
    template: `
        <li class="search-row">
            <label>対象国：</label>
            <select class="country" ([ngModel])="selectCountry" (change)="changeCountry($event.target.value)">
                <option *ngFor="let country of showCountries" [value]="country.value" [innerText]="country.text"></option>
            </select>
        </li>
    `,
})
export class RanksHeader {
    @Input() countries: Promise<any[]>;
    @Output() onSearch = new EventEmitter<any>();

    showCountries: any[];
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
        this.countries.then(obj => {
            this.showCountries = obj;
            this.selectCountry = obj.length ? obj[0].value : '';
            this.search();
        })
    }
}

@Component({
    selector: '[ranks-body]',
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
export class RanksBody {
    @Input() rows: any[];

    count(_row: any): string {
        return `${_row.count}人`;
    }
}
