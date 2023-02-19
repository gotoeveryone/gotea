<?php
declare(strict_types=1);

namespace Gotea\Form;

use Cake\Form\Form;
use Gotea\Validation\Validator;

/**
 * 基底フォーム
 */
class AppForm extends Form
{
    /**
     * Validator class.
     *
     * @var string
     */
    // phpcs:ignore
    protected $_validatorClass = Validator::class;
}
