<?php
declare(strict_types=1);

namespace Gotea\View\Helper;

use Cake\View\Helper\PaginatorHelper as BaseHelper;

/**
 * アプリ独自のページネーションヘルパー
 *
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Cake\View\Helper\NumberHelper $Number
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\FormHelper $Form
 */
class PaginatorHelper extends BaseHelper
{
    /**
     * @inheritDoc
     */
    // phpcs:disable Generic.Files.LineLength.TooLong
    protected array $_defaultConfig = [
        'params' => [],
        'options' => [
            'sortFormat' => 'separate',
        ],
        'templates' => [
            'nextActive' => '<li class="pager-item next"><a class="pager-item-link" rel="next" href="{{url}}">{{text}}</a></li>',
            'nextDisabled' => '<li class="pager-item next disabled"><a class="pager-item-link" href="" onclick="return false;">{{text}}</a></li>',
            'prevActive' => '<li class="pager-item prev"><a class="pager-item-link" rel="prev" href="{{url}}">{{text}}</a></li>',
            'prevDisabled' => '<li class="pager-item prev disabled"><a class="pager-item-link" href="" onclick="return false;">{{text}}</a></li>',
            'counterRange' => '{{start}} - {{end}} of {{count}}',
            'counterPages' => '{{page}} of {{pages}}',
            'first' => '<li class="pager-item first"><a class="pager-item-link" href="{{url}}">{{text}}</a></li>',
            'last' => '<li class="pager-item last"><a class="pager-item-link" href="{{url}}">{{text}}</a></li>',
            'number' => '<li class="pager-item"><a class="pager-item-link" href="{{url}}">{{text}}</a></li>',
            'current' => '<li class="pager-item active"><a class="pager-item-link">{{text}}</a></li>',
            'ellipsis' => '<li class="pager-item ellipsis">&hellip;</li>',
            'sort' => '<a class="sort" href="{{url}}"><span>{{text}}</span></span></a>',
            'sortAsc' => '<a class="sort asc" href="{{url}}"><span class="material-icons">arrow_drop_up</span><span>{{text}}</span></a>',
            'sortDesc' => '<a class="sort desc" href="{{url}}"><span class="material-icons">arrow_drop_down</span><span>{{text}}</span></a>',
            'sortAscLocked' => '<a class="sort asc locked" href="{{url}}"><span class="material-icons">arrow_drop_up</span><span>{{text}}</span></a>',
            'sortDescLocked' => '<a class="sort desc locked" href="{{url}}"><span class="material-icons">arrow_drop_down</span><span>{{text}}</span></a>',
        ],
    ];
    // phpcs:enable Generic.Files.LineLength.TooLong
}
