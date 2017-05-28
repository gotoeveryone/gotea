import 'reflect-metadata';
import 'zone.js/dist/zone';
import 'rxjs/Rx';

import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpModule, Http, XHRBackend, RequestOptions } from '@angular/http';

import { Titles, TitlesHeader, TitlesBody } from './titles/titles';
import { Colorbox } from './components/colorbox';
import { Dialog } from './components/dialog';
import { CustomHttp } from './components/customhttp';

import { BaseModule, isProdMode } from './base';

@NgModule({
    imports: [
        BrowserModule,
        HttpModule,
        FormsModule,
    ],
    providers: [
        {
            provide: Http,
            useFactory: (backend: XHRBackend, options: RequestOptions) => {
                return new CustomHttp(backend, options);
            },
            deps: [XHRBackend, RequestOptions],
        },
    ],
    declarations: [
        Titles,
        TitlesHeader,
        TitlesBody,
        Dialog,
        Colorbox,
    ],
    bootstrap: [
        Titles,
    ]
})
export class AppModule {}

isProdMode();

platformBrowserDynamic().bootstrapModule(AppModule);
