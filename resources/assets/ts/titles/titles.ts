import { Component, Input, Output, EventEmitter } from '@angular/core';
import { Http, Headers, RequestOptions, URLSearchParams } from '@angular/http';
import { NgIf, NgFor, NgClass } from '@angular/common';
import { NgModel } from '@angular/forms';
import { WEB_ROOT } from '../base';
import { Colorbox } from '../components/colorbox';

/**
 * タイトル情報検索コンポーネント
 */
@Component({
    selector: '[titles]',
    template: `
        <ul titles-header class="search-header" [countries]="countries" [types]="types"
            (onSearch)="search($event)" (addRow)="addRow($event)" (outputJson)="outputJson($event)">
        </ul>
        <div titles-results class="search-results" [rows]="rows" (onSelect)="select($event)" (openDialog)="openDialog($event)"></div>
        <colorbox class="iframe-modal" [url]="modal.url" [class.hide]="!modal.url"
            (click)="closeModal($event)" (onClose)="closeModal($event)"
            [height]="modal.height" [width]="modal.width"></colorbox>
        <dialog class="dialog" [class.hide]="!text" [text]="text" (onClose)="closeDialog($event)"></dialog>
    `,
})
export class Titles {
    countries = this.getCountries();
    types = this.getTypes();
    rows = new Array();

    country: number;

    text = '';
    modal = {
        url: '',
        width: '0',
        height: '0',
    };

    constructor(private http: Http) { }

    private getTypes() {
        return [
            {
                value: 0,
                text: '検索しない',
            },
            {
                value: 1,
                text: '検索する',
            },
        ];
    }

    private async getCountries() {
        const countries = new Array();
        await this.http.get(`${WEB_ROOT}api/countries/`)
            .forEach((res) => {
                const json = res.json().response;
                json.forEach((obj: any) => {
                    countries.push({
                        value: obj.id,
                        text: `${obj.name}棋戦`,
                    });
                });
            });
        return countries;
    }

    search(_params: any) {
        this.country = _params.country;

        const options = new URLSearchParams();
        options.set('admin', '1');
        options.set('withJa', '1');
        options.set('country_id', _params.country);
        options.set('is_closed', _params.type);
        this.http.get(`${WEB_ROOT}api/news/`, new RequestOptions({ search: options }))
            .forEach((res) => {
                this.rows = res.json().response;
            });
    }

    addRow() {
        this.rows.push({
            countryId: this.country,
            sortOrder: this.rows.length,
        });
    }

    outputJson() {
        const options = new URLSearchParams();
        options.set('make', '1');
        this.http.get(`${WEB_ROOT}api/news/`, new RequestOptions({ search: options }))
            .forEach((res) => {
                this.openDialog('JSONを出力しました。');
            });
    }

    openDialog(_text: string) {
        this.text = _text;
    }

    closeDialog() {
        this.text = '';
    }

    select(_id: number) {
        this.modal.width = '60%';
        this.modal.height = '90%';
        this.modal.url = `${WEB_ROOT}titles/detail/${_id}`;
    }

    closeModal() {
        this.modal.width = '0';
        this.modal.height = '0';
        this.modal.url = '';
    }
}

/**
 * ヘッダ出力コンポーネント
 */
@Component({
    selector: '[titles-header]',
    template: `
        <li class="search-row">
            <label>対象国：</label>
            <select ([ngModel])="selectCountry" (change)="changeCountry($event.target.value)">
                <option *ngFor="let country of showCountries" [value]="country.value" [innerText]="country.text"></option>
            </select>
            <label>終了棋戦：</label>
            <select ([ngModel])="searchType" (change)="changeType($event.target.value)">
                <option *ngFor="let type of types" [value]="type.value" [innerText]="type.text"></option>
            </select>
            <div class="button-wrap">
                <button type="button" (click)="add()">行追加</button>
                <button type="button" (click)="json()">JSON出力</button>
            </div>
        </li>
    `,
})
export class TitlesHeader {
    @Input() countries: Promise<any[]>;
    @Input() types: any[];
    @Output() onSearch = new EventEmitter<any>();
    @Output() addRow = new EventEmitter<any>();
    @Output() outputJson = new EventEmitter<any>();
    @Output() openDialog = new EventEmitter<any>();

    showCountries: any[];
    selectCountry: string;
    selectType: number;

    changeCountry(country: string) {
        this.selectCountry = country;
        this.search();
    }

    changeType(type: number) {
        this.selectType = type;
        this.search();
    }

    search() {
        this.onSearch.emit({
            country: this.selectCountry,
            type: this.selectType,
        });
    }

    add() {
        this.addRow.emit();
    }

    json() {
        this.outputJson.emit();
    }

    ngOnInit() {
        this.countries.then(obj => {
            this.showCountries = obj;
            this.selectCountry = obj.length ? obj[0].value : '';
            this.selectType = this.types[0].value;
            this.search();
        })
    }
}

/**
 * データ出力コンポーネント
 */
@Component({
    selector: '[titles-results]',
    template: `
        <ul class="table-header">
            <li class="table-row">
                <span class="name">タイトル名</span>
                <span class="name">タイトル名（英語）</span>
                <span class="holding">期</span>
                <span class="winner">優勝者</span>
                <span class="order">並び<br>順</span>
                <span class="team">団体</span>
                <span class="filename">HTML<br>ファイル名</span>
                <span class="modified">修正日</span>
                <span class="closed">終了<br>棋戦</span>
                <span>詳細</span>
            </li>
        </ul>
        <ul class="table-body" *ngIf="rows.length">
            <li class="table-row" *ngFor="let row of rows" [ngClass]="getRowClass(row)">
                <span class="name">
                    <input type="text" (change)="save(row)" [(ngModel)]="row.titleNameJp">
                </span>
                <span class="name">
                    <input type="text" (change)="save(row)" [(ngModel)]="row.titleName">
                </span>
                <span class="holding">
                    <input type="text" (change)="save(row)" [(ngModel)]="row.holding">
                </span>
                <span class="winner" [innerText]="getWinnerName(row)"></span>
                <span class="order">
                    <input type="text" (change)="save(row)" [(ngModel)]="row.sortOrder">
                </span>
                <span class="team">
                    <input type="checkbox" (change)="save(row)" [(ngModel)]="row.isTeam">
                </span>
                <span class="filename">
                    <input type="text" (change)="save(row)" [(ngModel)]="row.htmlFileName">
                </span>
                <span class="modified">
                    <input type="text" (change)="saveDatepicker($event, row)" class="datepicker" [(ngModel)]="row.htmlFileModified">
                </span>
                <span class="closed">
                    <input type="checkbox" (change)="save(row)" [(ngModel)]="row.isClosed">
                </span>
                <span>
                    <a (click)="add(row)" *ngIf="!row.titleId">登録</a>
                    <a (click)="select(row)" *ngIf="row.titleId">開く</a>
                </span>
            </li>
        </ul>
    `,
})
export class TitlesBody {
    @Input() rows: any[];
    @Output() onSelect = new EventEmitter<any>();
    @Output() openDialog = new EventEmitter<any>();

    constructor(private http: Http) { }

    getWinnerName(_row: any) {
        return _row.winnerName || '';
    }

    add(_row: any) {
        const headers = new Headers({ 'Content-Type': 'application/json' });
        const options = new RequestOptions({ headers: headers });
        // 登録処理
        this.http.post(`${WEB_ROOT}api/titles/`, JSON.stringify(_row), options)
            .forEach((res) => {
                _row.titleId = res.json().response.titleId;
                this.openDialog.emit(`タイトル【${_row.titleNameJp}】を登録しました。`);
            }).catch(res => {
                const message = res.json().response.message;
                if (message) {
                    this.openDialog.emit(`<ul class="message error"><li>${message.join('</li><li>')}</li></ul>`);
                } else {
                    this.openDialog.emit('登録に失敗しました…。');
                }
            });
    }

    select(_row: any) {
        this.onSelect.emit(_row.titleId);
    }

    saveDatepicker($event: any, _row: any) {
        _row.htmlFileModified = $event.target.value;
        this.save(_row);
    }

    save(_row: any) {
        if (!_row.titleId) {
            return;
        }
        const headers = new Headers({ 'Content-Type': 'application/json' });
        const options = new RequestOptions({ headers: headers });
        // 更新処理
        this.http.put(`${WEB_ROOT}api/titles/${_row.titleId}`, JSON.stringify(_row), options)
            .forEach(() => {
            }).catch(res => {
                const message = res.json().response.message;
                if (message) {
                    this.openDialog.emit(`<ul class="message error"><li>${message.join('</li><li>')}</li></ul>`);
                } else {
                    this.openDialog.emit('更新に失敗しました…。');
                }
            });
    }

    getRowClass(_row: any) {
        return _row.isClosed ? 'closed' : '';
    }
}
