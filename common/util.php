<?php
    /**
    * http://daringfireball.net/2009/11/liberal_regex_for_matching_urls
    * Replace links in text with html links
    *
    * @param  string $text
    * @return string
    */
   function auto_link_text($text) {
      $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
      $callback = create_function('$matches', '
          $url       = array_shift($matches);
          $url_parts = parse_url($url);

          $text = parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);
          $text = preg_replace("/^www./", "", $text);

          $last = -(strlen(strrchr($text, "/"))) + 1;
          if ($last < 0) {
              $text = substr($text, 0, $last) . "&hellip;";
          }

          return sprintf(\'<a href="%s" target="_blank">%s</a>\', $url, $text);
      ');

      return preg_replace_callback($pattern, $callback, $text);
    }
    function auto_link_text_simple($text) {
        return preg_replace(
          '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>',
          $text
        );
    }

    /**
    *
    *  PHP validate email
    *  http://www.webtoolkit.info/
    *
    **/
    function is_valid_email($email){
        return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
    }

    function bolding($haystack, $needle) {
        if (is_array($haystack)) {
            $bolded = array();
            foreach ($haystack as $sub_haystack) {
                $bolded[] = bolding($sub_haystack, $needle);
            }
        } else if (is_string($haystack)) {
            $bolded = preg_replace("/($needle)/", '<strong>$1</strong>', $haystack, $limit=1);
        }
        return $bolded;
    }

    function purify_int_value($input, $default) {
        $matches = array();
        preg_match('/^\d+$/', $input, $matches);
        return (empty($matches)) ? $default : intval(($input));
    }
