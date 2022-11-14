<?php
declare(strict_types=1);

namespace Gotea\Validation;

use Cake\Validation\Validation as BaseValidation;

/**
 * カスタムのバリデーションクラス
 */
class Validation extends BaseValidation
{
    /**
     * Invalid multibyte value too.
     *
     * @static
     * @param mixed $check check value
     * @return bool check result
     */
    public static function password(mixed $check): bool
    {
        return (bool)preg_match('/^[a-zA-Z0-9\(\)\'\-_@]+$/', $check);
    }

    /**
     * Invalid multibyte value too.
     *
     * @static
     * @param mixed $check check value
     * @return bool check result
     */
    public static function nameEnglish(mixed $check): bool
    {
        return (bool)preg_match('/^[a-zA-Z0-9\(\)\'\-\s]+$/', $check);
    }
}
