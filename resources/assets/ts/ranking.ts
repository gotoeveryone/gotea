import 'reflect-metadata';
import 'zone.js/dist/zone';

import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpModule } from '@angular/http';

import { Ranking, RankingHeader, RankingBody } from './ranking/ranking';
import { Colorbox } from './components/colorbox';

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
        Colorbox,
    ],
    bootstrap: [
        Ranking,
    ]
})
export class AppModule {}

platformBrowserDynamic().bootstrapModule(AppModule);
