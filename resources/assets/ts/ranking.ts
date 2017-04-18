import 'reflect-metadata';
import 'zone.js/dist/zone';

import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpModule } from '@angular/http';

import { Ranking, RankingHeader, RankingBody } from './ranking/ranking';

@NgModule({
    imports: [
        BrowserModule,
        HttpModule,
        FormsModule,
    ],
    declarations: [
        Ranking,
        RankingHeader,
        RankingBody,
    ],
    bootstrap: [
        Ranking,
    ]
})
export class AppModule {}

platformBrowserDynamic().bootstrapModule(AppModule);
