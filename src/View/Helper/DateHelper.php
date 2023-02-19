<?php
declare(strict_types=1);

namespace Gotea\View\Helper;

use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\View\Helper;

/**
 * 日付・時刻の表示周りの共通処理
 */
class DateHelper extends Helper
{
    // 日付フォーマット
    public const FORMAT_DATE = 'yyyy年MM月dd日';
    public const FORMAT_TIME = 'HH時mm分ss秒';
    public const FORMAT_DATETIME = self::FORMAT_DATE . ' ' . self::FORMAT_TIME;

    /**
     * 日付型オブジェクトを特定フォーマットの表記に変換して表示します。
     *
     * @param \Cake\I18n\FrozenDate|\Cake\I18n\FrozenTime|null $object 変換するDate or Time型オブジェクト
     * @param string $format 指定したフォーマット
     * @return string 整形した日付表記
     */
    public function format(FrozenDate|FrozenTime|null $object, string $format = self::FORMAT_DATETIME): string
    {
        if (empty($object)) {
            return '';
        }

        return $object->i18nFormat($format);
    }

    /**
     * 日付型オブジェクトを特定フォーマットの表記に変換して表示します。
     * ※yyyy年MM月dd日 HH時mm分ss秒
     *
     * @param \Cake\I18n\FrozenTime|null $timeObj 変換するTime型オブジェクト
     * @return string 整形した日付表記
     */
    public function formatToDateTime(?FrozenTime $timeObj = null): string
    {
        return $this->format($timeObj, self::FORMAT_DATETIME);
    }

    /**
     * 日付型オブジェクトを特定フォーマットの表記に変換して表示します。
     * ※yyyy年MM月dd日
     *
     * @param \Cake\I18n\FrozenDate|null $dateObj 変換するDate型オブジェクト
     * @return string 整形した日付表記
     */
    public function formatToDate(?FrozenDate $dateObj = null): string
    {
        return $this->format($dateObj, self::FORMAT_DATE);
    }

    /**
     * 日付型オブジェクトを特定フォーマットの表記に変換して表示します。
     * ※HH時mm分ss秒
     *
     * @param \Cake\I18n\FrozenTime|null $timeObj 変換するTime型オブジェクト
     * @return string 整形した日付表記
     */
    public function formatToTime(?FrozenTime $timeObj = null): string
    {
        return $this->format($timeObj, self::FORMAT_TIME);
    }
}
