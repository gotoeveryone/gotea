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
        <form>
        <ul ranking-header class="search-header" [lastUpdate]="lastUpdate"></ul>
        <input type="text" ([ngModel])="hoge">
        {{hoge}}
        <div ranking-results class="search-results"></div>
        </form>
    `,
})
export class RankingComponent {
    lastUpdate = 'aaaaaaa';
    rows = new Array();
    hoge = 'hoge!!';

    // @ViewChildren(RankingHeaderComponent) header: QueryList<RankingHeaderComponent>
    // @ViewChildren(RankingHeaderComponent) results: QueryList<RankingDataComponent>

    constructor(private http: Http) {
        // this.lastUpdate = 'aaa';
        // this.http.get('/igoapp/api/rankings/日本/2017/20?jp=true')
        //     .forEach((res) => {
        //         const json = res.json().response;
        //         this.lastUpdate = json.lastUpdate;
        //         this.rows = json.ranking;
        //     });
    }
}