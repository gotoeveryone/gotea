import 'reflect-metadata';
import 'zone.js/dist/zone';

import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpModule, Http, XHRBackend, RequestOptions } from '@angular/http';

import { Ranking, RankingHeader, RankingBody } from './ranking/ranking';
import { Colorbox } from './components/colorbox';
import { Dialog } from './components/dialog';
import { CustomHttp } from './components/customhttp';

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
        Ranking,
        RankingHeader,
        RankingBody,
        Dialog,
        Colorbox,
    ],
    bootstrap: [
        Ranking,
    ]
})
export class AppModule {}

platformBrowserDynamic().bootstrapModule(AppModule);
