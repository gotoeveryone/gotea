<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\I18n\Date;
use Cake\I18n\Time;

/**
 * 日付・時刻の表示周りの共通処理
 */
class DateHelper extends Helper {

    // タイムゾーン
    const LOCALE = 'Asia/Tokyo';

    // 日付フォーマット
    const FORMAT_DATE = 'YYYY年MM月dd日';
    const FORMAT_TIME = 'HH時mm分ss秒';
    const FORMAT_DATETIME = 'YYYY年MM月dd日 HH時mm分ss秒';

    /**
     * 「YYYYMMdd」文字列を「YYYY年MM月dd日」表記に変換して表示します。
     * 文字列が5桁以下の場合は年まで、文字列が7桁以下の場合は月まで表示します。
     *
     * @param string 変換対象文字列
     * @return string 整形した日付表記
     */
    public function formatJpValue($value) {
        $len = strlen($value);
        if (empty($value) || $len > 8) {
            return '';
        }
        $year = $len < 4 ? '' : substr($value, 0, 4).'年';
        $month = $len < 6 ? '' : substr($value, 4, 2).'月';
        $day = $len < 8 ? '' : substr($value, 6, 2).'日';

        return $year.$month.$day;
    }

    /**
     * 「YYYYMMdd」文字列を指定した区切り文字を付与して表示します。
     * 文字列が5桁以下の場合は年まで、文字列が7桁以下の場合は月まで表示します。
     *
     * @param string 変換対象文字列
     * @param string デリミタ
     * @return string 整形した日付表記
     */
    public function formatJoinDelimiterValue($value, $delimiter) {
        $len = strlen($value);
        if (empty($value) || $len > 8) {
            return '';
        }
        $year = $len < 4 ? '' : substr($value, 0, 4);
        $month = $len < 6 ? '' : $delimiter.substr($value, 4, 2);
        $day = $len < 8 ? '' : $delimiter.substr($value, 6, 2);

        return $year.$month.$day;
    }

    /**
     * 日付型オブジェクトを特定フォーマットの表記に変換して表示します。
     *
     * @param Date|Time $timeObj 変換するDate or Time型オブジェクト
     * @param string $format 指定したフォーマット
     * @return string 整形した日付表記
     */
    public function format($timeObj, $format = null) {
        if (empty($timeObj)) {
            return '';
        }
        if (empty($format)) {
            $format = self::FORMAT_DATETIME;
        }
        return h($timeObj->i18nFormat($format, self::LOCALE));
    }

    /**
     * 日付型オブジェクトを特定フォーマットの表記に変換して表示します。
     * ※YYYY年MM月dd日 HH時mm分ss秒
     *
     * @param Time $timeObj 変換するTime型オブジェクト
     * @return string 整形した日付表記
     */
    public function formatToDateTime(Time $timeObj) {
        if (empty($timeObj)) {
            return '';
        }
        return h($timeObj->i18nFormat(self::FORMAT_DATETIME, self::LOCALE));
    }

    /**
     * 日付型オブジェクトを特定フォーマットの表記に変換して表示します。
     * ※YYYY年MM月dd日
     *
     * @param Date $dateObj 変換するDate型オブジェクト
     * @return string 整形した日付表記
     */
    public function formatToDate(Date $dateObj) {
        if (empty($dateObj)) {
            return '';
        }
        return h($dateObj->i18nFormat(self::FORMAT_DATE, self::LOCALE));
    }

    /**
     * 日付型オブジェクトを特定フォーマットの表記に変換して表示します。
     * ※HH時mm分ss秒
     *
     * @param Time $timeObj 変換するTime型オブジェクト
     * @return string 整形した日付表記
     */
    public function formatToTime($timeObj) {
        if (empty($timeObj)) {
            return '';
        }
        return h($timeObj->i18nFormat(self::FORMAT_TIME, self::LOCALE));
    }
}
