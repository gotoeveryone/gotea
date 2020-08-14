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
    protected $_defaultConfig = [
        'options' => [],
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
            'sort' => '<a class="pager-item-link" href="{{url}}">{{text}}</a>',
            'sortAsc' => '<a class="pager-item-link" class="asc" href="{{url}}">{{text}}</a>',
            'sortDesc' => '<a class="pager-item-link" class="desc" href="{{url}}">{{text}}</a>',
            'sortAscLocked' => '<a class="pager-item-link" class="asc locked" href="{{url}}">{{text}}</a>',
            'sortDescLocked' => '<a class="pager-item-link" class="desc locked" href="{{url}}">{{text}}</a>',
        ],
    ];
    // phpcs:enable Generic.Files.LineLength.TooLong
}
