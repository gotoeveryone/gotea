import 'reflect-metadata';
import 'zone.js/dist/zone';
import 'rxjs/Rx';

import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';

import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { BrowserModule } from '@angular/platform-browser';
import { HttpModule, Http, XHRBackend, RequestOptions } from '@angular/http';

import { Categorize, CategorizeHeader, CategorizeBody } from './categorize/categorize';
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
