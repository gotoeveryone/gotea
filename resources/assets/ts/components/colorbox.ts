import { Component, Input, Output, EventEmitter } from '@angular/core';
import { DomSanitizer, SafeResourceUrl, SafeUrl } from '@angular/platform-browser';

/**
 * iframeでモーダルウィンドウを表示するためのコンポーネント
 */
@Component({
    selector: 'colorbox',
    template: `
        <div class="modal-parent" [style.width]="width" [style.height]="height" [class.hide]="!this.url">
            <iframe class="modal-body" [src]="getSrc()"></iframe>
            <div class="modal-close" (click)="close()"></div>
        </div>
    `
})
export class Colorbox {
    @Input() url: string;
    @Input() height: string;
    @Input() width: string;
    @Output() onClose = new EventEmitter<any>();

    constructor(private sanitizer: DomSanitizer) {}

    getSrc() {
        return this.sanitizer.bypassSecurityTrustResourceUrl(this.url);
    }

    close() {
        this.onClose.emit();
    }
}