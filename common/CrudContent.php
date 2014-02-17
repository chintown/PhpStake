<?php

require_once 'common/std.php';

class CrudContent {
    private $fields;
    private $rows;

    function __construct() {
        $this->fields = array();
        $this->rows = array();
    }

    function setFields($fields) {
        foreach ($fields as $identifier => $title) {
            $this->setField($identifier, $title);
        }
    }
    function setField($identifier, $title) {
        $this->fields[$identifier] = purify($title, 'html');
    }
    function addRows($rows) {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }
    function addRow($row) {
        $norm = array();
        foreach ($row as $identifier => $value) {
            $norm[$identifier] = purify($value, 'html');
        }
        $this->rows[] = $norm;
    }
    function exportFields() {
        return $this->fields;
    }
    function exportRows() {
        return $this->rows;
    }
}