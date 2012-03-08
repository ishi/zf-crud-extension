<?php
class Crud_Plugin extends Zend_Controller_Plugin_Abstract {
	
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        $front = Zend_Controller_Front::getInstance();
        /** @var $bootstrap Zend_Application_Bootstrap_Bootstrap */
        $bootstrap = $front->getParam('bootstrap');
        
        $crud = $bootstrap->getOption('crud');
        if (!$this->_inArrayKeys($crud['page'], $controllerName)
                || !$this->_inArray($crud['page'][$controllerName]['actions'], $actionName)) {
            return;
        }

        $front->addControllerDirectory(dirname(__FILE__). PATH_SEPARATOR . 'controllers', 'crud');
        $bootstrap->getResource('view')->addScriptPath(dirname(__FILE__). PATH_SEPARATOR . 'views');
        
        if(isset($crud['page'][$controllerName]['model'])) {
            $model = new $crud['page'][$controllerName]['model'];
        } else {
            $model = new Crud_Model(strtolower($controllerName));
        }

        $request->setControllerName('crud');
        $request->setParams(
            array(
                'crud_hash' => $crud['hash'],
                'original_controller' => $controllerName,
                'model' => $model
            )
        );
    }

    private function _inArrayKeys($arr, $val) {
        return $this->_isArray($arr) && in_array($val, array_keys($arr));
    }

    private function _inArray($arr, $val) {
        return $this->_isArray($arr) && in_array($val, $arr);
    }

    private function _isArray($arr) {
        return isset($arr) && is_array($arr);
    }
}