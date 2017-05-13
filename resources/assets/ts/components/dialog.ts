import { Component, Input, Output, EventEmitter } from '@angular/core';

/**
 * ダイアログを表示するためのコンポーネント
 */
@Component({
    selector: 'dialog',
    template: `
        <div class="dialog-content" [class.hide]="!this.text">
            <div class="dialog-content-header" *ngIf="title">
                <div class="dialog-content-title" [innerText]="title"></div>
            </div>
            <div class="dialog-content-body">
                <div class="dialog-content-body-text" [innerHTML]="text"></div>
            </div>
            <div class="dialog-content-footer">
                <button (click)="close()">閉じる</button>
            </div>
        </div>
    `
})
export class Dialog {
    title = 'タイトル';
    @Input() text: string;
    @Output() onClose = new EventEmitter<any>();

    close() {
        this.onClose.emit();
    }
}