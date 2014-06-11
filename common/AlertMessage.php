<?php

class AlertMessage {
    public $type;
    public $msg;

    function __construct($type, $msg) {
        $this->type = $type;
        $this->msg = $msg;
    }
}