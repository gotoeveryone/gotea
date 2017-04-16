import 'reflect-metadata';
import 'zone.js/dist/zone';

import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpModule } from '@angular/http';

import { RankingComponent } from './ranking/RankingComponent';
import { RankingHeaderComponent } from './ranking/RankingHeaderComponent';
import { RankingDataComponent } from './ranking/RankingDataComponent';

@NgModule({
    imports: [
        BrowserModule,
        HttpModule,
        FormsModule,
    ],
    declarations: [
        RankingComponent,
        RankingHeaderComponent,
        RankingDataComponent,
    ],
    bootstrap: [
        RankingComponent,
    ]
})
export class AppModule {}

platformBrowserDynamic().bootstrapModule(AppModule);
