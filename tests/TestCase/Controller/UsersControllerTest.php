<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * UsersControllerTest class
 */
class UsersControllerTest extends IntegrationTestCase
{
    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->get('/');
        $this->assertResponseOk();
        $this->assertResponseContains('ログイン');
        $this->assertResponseContains('<html>');
    }
}
