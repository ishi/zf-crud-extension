<?php
class Crud_Model extends Crud_Model_Abstract {
    
	public function __construct($modelName) {
        $this->_modelName = $modelName;
        $this->setDbTable(new Zend_Db_Table($modelName));
    }
}