import 'reflect-metadata';
import 'zone.js/dist/zone';
import 'rxjs/Rx';

import { enableProdMode } from '@angular/core';

declare const PRODUCTION: boolean;

export function isProdMode() {
    if (PRODUCTION) {
        enableProdMode();
    }
}

export abstract class BaseModule {
    constructor() {
    }
}