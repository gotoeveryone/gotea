import { Observable } from 'rxjs/Observable';
import { Injectable } from '@angular/core';
import { Http, ConnectionBackend, Request, Response, RequestOptions, RequestOptionsArgs } from '@angular/http';

/**
 * カスタムHTTPモジュール
 */
@Injectable()
export class CustomHttp extends Http {
    constructor(backend: ConnectionBackend, defaultOptions: RequestOptions) {
        super(backend, defaultOptions);
    }

    request(url: string | Request, options?: RequestOptionsArgs): Observable<Response> {
        document.querySelector('.block-ui').classList.add('blocked');
        return super.request(url, options).finally(() => {
            document.querySelector('.block-ui').classList.remove('blocked');
        });
    }
}