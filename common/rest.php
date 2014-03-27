<?php
    function send_rest_w_curl_less($method, $url, $data) {
        $response = null;
        switch(strtoupper($method)) {
            case 'POST':
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    ),
                );
                $context  = stream_context_create($options);
                $response = file_get_contents($url, false, $context);
                break;
            default:
                die($method . ' not implement yet');
                break;
        }
        return array(
            'meta'=> array(
                'url' => $url,
                'payload' => $data,
            ),
            'result'=> $response
        );
    }
    function send_rest($method, $url, $data) {
        # headers and data (this is API dependent, some uses XML)
        $handle = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            );
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        switch(strtoupper($method)) {
            case 'GET':
                $data = json_encode($data);
                $url .= "/".urlencode($data);
                $data = '';
                break;
            case 'POST':
                $data = json_encode($data);
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':
                $url .= "/".urlencode($data['_id']);
                unset($data['_id']);
                $data = json_encode($data);
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                $url .= "/".urlencode($data['_id']);
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        $url = preg_replace('/[+]/', '%20', $url);
        $url = preg_replace('/%2B/', '%252B', $url); // hack: our server does not treat %2B as "+"
        curl_setopt($handle, CURLOPT_URL, $url);

        $stdout = fopen('php://stdout', 'w');
        curl_setopt($handle, CURLOPT_VERBOSE, true);
        curl_setopt($handle, CURLOPT_STDERR, $stdout);
        curl_setopt($handle, CURLOPT_REFERER, 'http://' . SERVER_HOST);

        // echo $url."\n";
        // echo $data;
        // echo "\n";
        $response = curl_exec($handle);

        fclose($stdout);
        //$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        //$request_header_info = curl_getinfo($handle, CURLINFO_HEADER_OUT);
        curl_close($handle);

        // echo $response."\n";
        return array(
            'meta'=> array(
                'url' => $url,
                'payload' => $data,
            ),
            'result'=> $response
        );
    }

    function parse_response($result, $is_inspect=false) {
        $result_json = $result['result'];
        $item = json_decode($result_json, true);
        if ($item === NULL) {
            handle_response($result);
        }
        if ($is_inspect) {
            de($result['meta']);
        }
        return $item;
    }
    function handle_response($result) {
        $msg = array();
        $msg[] = "can not request mongo:";
        $msg[] = var_export($result['meta'], true);
        $msg = join("\n", $msg);
        error_log("$msg");
        if (DEV_MODE) {
            de($msg);
            exit(1);
        } else {
            Header('Location: ');
        }
    }

    function commit_log($user, $action, $target, $stamp) {
        $url = 'http://'.MONGO_HOST.'/'.MONGO_PATH.'/log';
        $data = array(
            'user'=> $user,
            'action'=> $action,
            'target'=> $target,
            'stamp'=> $stamp,
        );
        $result = send_rest('post', $url, $data);
        // de($result['meta']);
        $result_json = $result['result'];
        $item = json_decode($result_json, true);
        if ($item === NULL) {
            if (DEV_MODE) {
                error_log("CRITICAL: can not log ");
                error_log(var_export($item, true));
            } else {
                Header('Location: ');
            }
        }


    }