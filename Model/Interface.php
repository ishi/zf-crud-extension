<?php
interface Crud_Model_Interface {
    function getAll($where = null, $order = null);
    function getById($id);
    function save(array $data, Zend_Db_Table_Row_Abstract $currentItem = null);
    function delete($id);
    function getForm();
}