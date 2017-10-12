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
namespace Gotea\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

/**
 * PlayersControllerTest class
 */
class PlayersControllerTest extends IntegrationTestCase
{
    /**
     * 画面が見えるか
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->get('/players/');
        $this->assertResponseOk();
        $this->assertResponseContains('CakePHP');
        $this->assertResponseContains('<html>');
    }

    /**
     * テンプレートが存在しない
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        Configure::write('debug', false);
        $this->get('/players/search');

        $this->assertResponseError();
        $this->assertResponseContains('Error');
    }

    /**
     * GETでアクセスできない
     *
     * @return void
     */
    public function testNoGetMethod()
    {
        Configure::write('debug', false);
        $this->get('/players/search');

        $this->assertResponseError();
        $this->assertResponseContains('Error');
    }

    /**
     * Test that missing template renders 404 page in production
     *
     * @return void
     */
    public function testDetail()
    {
        $this->get('/players/detail/1');
        $this->assertResponseOk();
        $this->assertResponseContains('<html>');
    }
}
