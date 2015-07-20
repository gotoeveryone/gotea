<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

class SessionComponent extends Component {

    public $controller = null;
    public $session = null;

    public function initialize(array $config)
    {
        parent::initialize($config);
        // ....

        /**
         * Get current controller
        */
        $this->controller = $this->_registry->getController();

        $this->session = $this->controller->request->session();

        // You can then use $this->session in any other methods
        // If debug = true else use print_r() to test
        debug($this->session->read('Auth.User.username'));
    }
}
