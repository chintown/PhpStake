<?php
    function get_user_from_session() {
        session_start();
        return $_SESSION['ID'];
    }
    function keep_user_in_session($user) {
        session_start();
        session_regenerate_id();
        $_SESSION['ID'] = $user;
        session_write_close();
    }
    function remove_user_from_session() {
        session_start();
        unset($_SESSION['ID']);
        session_write_close();
    }

    function keep_user_in_cookie($user_id, $last_seconds) {
        setcookie("ID", $user_id, time() + $last_seconds, WEB_PATH);
    }
    function remove_user_from_cookie() {
        unset($_COOKIE['ID']);
        setcookie("ID", null, -1, WEB_PATH);
    }

    function keep_session_in_cookie($sessionToken, $last_seconds) {
        setcookie("TOKEN", $sessionToken, time() + $last_seconds, WEB_PATH);
    }
    function remove_session_from_cookie() {
        unset($_COOKIE['TOKEN']);
        setcookie("TOKEN", null, -1, WEB_PATH);
    }