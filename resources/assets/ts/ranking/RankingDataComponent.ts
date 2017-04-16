import { Component, Input } from '@angular/core';
import { Http } from '@angular/http';
import { NgIf, NgFor, NgClass } from '@angular/common';

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
                <span [innerHtml]="getRank(idx, row)"></span>
            </span>
            <span class="left player">
                <a class="colorbox" [href]="'/igoapp/players/detail/' + row.playerId">
                    <span [ngClass]="{'female': row.sex === '女性'}" [innerHtml]="row.playerNameJp"></span>
                </a>
            </span>
            <span class="point" [innerHtml]="row.winPoint"></span>
            <span class="point" [innerHtml]="row.losePoint"></span>
            <span class="point" [innerHtml]="row.drawPoint"></span>
            <span class="percent" [innerHtml]="getWinPercentage(row)"></span>
        </li>
    </ul>
    `
})
export class RankingDataComponent {
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