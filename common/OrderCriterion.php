<?php

class OrderCriterion {
    private $field;
    private $order;

    function __construct($encoded, $symbol_asc='+', $symbol_desc='-') {
        $criterion = substr($encoded, 0, strlen($symbol_asc));
        $this->order = ($criterion === $symbol_asc) ? SORT_ASC : SORT_DESC;
        $this->field = substr($encoded, strlen($symbol_asc));
    }

    public function getField() {
        return $this->field;
    }

    public function getOrder() {
        return $this->order;
    }
}