import 'reflect-metadata';
import 'zone.js/dist/zone';

import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpModule } from '@angular/http';

import { Categorize, CategorizeHeader, CategorizeBody } from './categorize/categorize';

@NgModule({
    imports: [
        BrowserModule,
        HttpModule,
        FormsModule,
    ],
    declarations: [
        Categorize,
        CategorizeHeader,
        CategorizeBody,
    ],
    bootstrap: [
        Categorize,
    ]
})
export class AppModule {}

platformBrowserDynamic().bootstrapModule(AppModule);
