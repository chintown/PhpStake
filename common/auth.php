<?php
    function get_user_id_from_session() {
        session_start();
        return $_SESSION['ID'];
    }
    function get_user_disp_name_from_session() {
        session_start();
        return $_SESSION['NAME'];
    }
    function keep_user_in_session($user, $disp_name=null) {
        session_start();
        session_regenerate_id();
        $_SESSION['ID'] = $user;
        if (isset($disp_name)) {
            $_SESSION['NAME'] = $disp_name;
        }
        reset_session_rotation_count();
        session_write_close();
    }
    function reset_session_rotation_count() {
        $_SESSION['COUNT'] = 5;
    }
    function consume_session_rotation_count() {
        // http://stackoverflow.com/questions/1221447/what-do-i-need-to-store-in-the-php-session-when-user-logged-in
        if(($_SESSION['COUNT'] -= 1) == 0) {
            session_regenerate_id();
            reset_session_rotation_count();
        }
    }
    function remove_user_from_session() {
        session_start();
        unset($_SESSION['ID']);
        unset($_SESSION['NAME']);
        unset($_SESSION['COUNT']);
        session_write_close();
    }

    function evaluate_domain() {
        return SERVER_HOST == 'localhost' ? '' : SERVER_HOST;
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

    function keep_session_expiration_in_session($seconds_to_be_expired) {
        session_start();
        $_SESSION['SESSION_EXPIRATION'] = time() + $seconds_to_be_expired;
        session_write_close();
    }
    function remove_session_expiration_from_session() {
        session_start();
        unset($_SESSION['SESSION_EXPIRATION']);
        session_write_close();
    }
    function is_session_expired() {
        session_start();
        return time() > $_SESSION['SESSION_EXPIRATION'];
    }