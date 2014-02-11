<?php
    $cwd = dirname(__FILE__).'/';
    require $cwd . '../lib/spyc.php';

    class Translator {
        private $dict;
        private $sep_section = '.';
        private $sep_option = '|';
        private $errors = array();

        public function Translator($lang) {
            global $cwd;

            $fn_lang = $cwd . '../i18n/' . $lang . '.yaml';
            if (!file_exists($fn_lang)) {
                if (DEV_MODE) {
                    de('can not find translation file, ' . $fn_lang);
                }
                exit(1);
            }
            $this->dict = spyc_load_file($fn_lang);
        }
        public function k($key, $options='') {
            $value = $this->get_value($key);
            $cooked_value = $value;
            $callbacks = $this->prepare_option_callbacks($options);

            foreach($callbacks as $callback) {
                array_unshift($callback['params'], $cooked_value);
                $cooked_value = call_user_func_array($callback['name'], $callback['params']);
            }
            return $cooked_value;
        }
        private function get_value($key) {
            $sections = explode($this->sep_section, $key);
            $curr_obj = $this->dict;
            foreach ($sections as $section) {
                if (!array_key_exists($section, $curr_obj)) {
                    $this->errors[] = 'can not find given translation key (' . $key . ') at section, ' . $section;
                    $curr_obj = null;
                    break;
                }
                $curr_obj = $curr_obj[$section];
            }
            if (empty($this->errors)) {
                return $curr_obj;
            } else {
                foreach ($this->errors as $error) {
                    de($error);
                }
                return '';
            }
        }
        private function prepare_option_callbacks($options) {
            if (empty($options)) {
                return array();
            }
            $options = explode($this->sep_option, $options);
            $callbacks = array();
            foreach ($options as $option) {
                if ($option === 'upper') {
                    $callbacks[] = array('name'=> 'strtoupper', 'params'=> array());
                } else if ($option === 'lower') {
                    $callbacks[] = array('name'=> 'strtolower', 'params'=> array());
                } else if ($option === 'capital') {
                    $callbacks[] = array('name'=> 'ucfirst', 'params'=> array());
                } else if ($option === 'word') {
                    $callbacks[] = array('name'=> 'ucwords', 'params'=> array());
                } else if ($option === 'html') {
                    $callbacks[] = array('name'=> 'purify', 'params'=> array('html'));
                } else if ($option === 'url') {
                    $callbacks[] = array('name'=> 'purify', 'params'=> array('url'));
                } else {
                    $this->errors[] = 'can not find callback for option, ' . $option;
                    continue;
                }
            }
            return $callbacks;
        }
    }
//    $translator = new Translator('en');
//    $r = $translator->k('common.download', 'capital|html');
//    var_dump($r);