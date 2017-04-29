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
    countries = this.getCountries();
    rows = new Array();

    constructor(private http: Http) {}

    private async getCountries() {
        const countries = new Array();
        await this.http.get('/igoapp/api/countries/?has_title=true')
            .forEach((res) => {
                const json = res.json().response;
                json.forEach((obj: any) => {
                    countries.push({
                        value: obj.name,
                        text: obj.name,
                    });
                });
            });
        return countries;
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
                <option *ngFor="let country of showCountries" [value]="country.value" [innerText]="country.text"></option>
            </select>
        </li>
    `,
})
export class CategorizeHeader {
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