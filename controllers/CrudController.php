<?php
class CrudController extends Zend_Controller_Action {
    /**
     * @var Crud_Model_Abstract
     */
    private $_model = null;

    public function init() {
        $crud = $this->getInvokeArg('bootstrap')->getOption('crud');
        if($crud['hash'] != $this->_getParam('crud_hash')) {
            throw new UnexpectedValueException('Invalid crud hash');
        }
        $this->_model = $this->_getParam('model');
    }

    public function indexAction() {
        $cols = $this->_model->getDbTable()->info(Zend_Db_Table_Abstract::COLS);
        $crud = $this->getInvokeArg('bootstrap')->getOption('crud');
        $controller = $this->_getParam('controller');

        if(isset($crud['page'][$controller]) && isset($crud['page'][$controller]['skipCols'])) {
            $cols = array_diff($cols, $crud['page'][$controller]['skipCols']);
        }

        $this->view->cols = $cols;
        $this->view->items = $this->_model->getItems();
        $this->_renderView();
    }

    public function addAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $id = (int) $this->_getParam('id', 0);
        $item = $this->_model->getById($id);
        $form = $this->_model->getForm();

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();
            if($form->isValid($post)) {
                $this->_model->save($form->getValues(), $item);
                $this->_helper->redirector(null, $this->_getParam('original_controller'));
            }
        } else {
            $form->populate($item !== null ? $item->toArray() : array());
        }

        $this->view->form = $form;
        $this->_renderView();
    }

    public function deleteAction() {
        $id = (int)$this->_getParam('id', 0);
        $this->_model->delete(array('id = ?' => $id));
        $this->_helper->redirector(null, $this->_getParam('original_controller'));
    }

    private function _renderView() {
        $controller = $this->_getParam('controller');
        $action = $this->_getParam('action');

        $universal = $this->getInvokeArg('bootstrap')->getOption('crud');
        if(isset($universal['page'][$controller]['view'][$action])) {
            $this->renderScript($universal['page'][$controller]['view'][$action]);
        }
    }
}