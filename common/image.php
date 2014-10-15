<?php
    function get_serials($files) {
        $serials = array();
        foreach ($files as $pid => $fn) {
            $content = file_get_contents($fn);
            $base64 = base64_encode($content);
            $serials[] = array('pid' => $pid, 'base64' => $base64);
        }
        return $serials;
    }
    function compose_serials($serials) {
        $result = array();
        foreach ($serials as $serial) {
            // store detail data in json which could be extract from client side
            $serial = array($serial); // a array wrapping to fix json eval 'bug'
            $payload = json_encode($serial);
            //$payload = $serial['base64']; // alternative

            $token = "--".MXHR_SEP."\nContent-Type: image/jpeg\n$payload";
            $result[] = $token;
        }
        $result[] = '--'.MXHR_SEP.'--';
        return join('', $result);
    }


    function get_image_type_name($file) {
        $IMAGE_TYPE = array(
            1 => "gif", 2 => "jpeg",    3 => "png", 4 => "swf", 5 => "psd", 6 => "bmp",
            7 => "tiff_ii", 8 => "tiff_mm", 9 => "jpc", 10=> "jp2", 11=> "jpx",
            12=> "jb2", 13=> "swc", 14=> "iff",     15=> "wbmp",    16=> "xbm", 17=> "ico"
        );
        $type_code = exif_imagetype($file);
        $type_name = (isset($IMAGE_TYPE[$type_code]))
            ? $IMAGE_TYPE[$type_code]
            : 'N/A'
        ;
        return $type_name;
    }

    function imagecreatefrom_by_type($path_image) {
        $type = get_image_type_name($path_image);
        return call_user_func('imagecreatefrom'.$type, $path_image);
    }
    function image_by_type($type, $resource, $path_image) {
        call_user_func_array('image'.$type, array($resource, $path_image));
    }
    /*
    * 1. if destination size is smaller
    *    keep ratio, and resize until one edge fit
    * 2. larger
    *    use original size
    */
    function gen_thumbnail($path_in, $path_out, $max_w, $max_h, $method='ImageMagick') {
        $return = array('success'=> true, 'msg'=> '', 'output_w'=> $maxW, 'output_h'=> $maxH);

        $type = get_image_type_name($path_in);
        $src = imagecreatefrom_by_type($path_in);
        // get the source image's width and height
        $src_w = imagesx($src);
        $src_h = imagesy($src);

        $new_size = fit_size($src_w, $src_h, $max_w, $max_h);
        $thumb_w = $new_size['width'];
        $thumb_h = $new_size['height'];

    //    $thumb_w = $maxW;
    //    $thumb_h = $maxH;

        $return['output_w'] = $thumb_w;
        $return['output_h'] = $thumb_h;

        if ('GD' == $method) {
            $thumb = imagecreatetruecolor($thumb_w, $thumb_h);
            // start resize
            imagecopyresized($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);
            // save thumbnail
            image_by_type($type, $thumb, $path_out);
        } else if ('ImageMagick' == $method) {
            $exports = array(
                'PATH'=> '$PATH:/opt/ImageMagick/bin/'
            );
            $exec_info = execute_external(
                '/opt/ImageMagick/bin/convert', array("-resize {$thumb_w}x{$thumb_h} '$path_in' '$path_out'"),
                '', $exports, '.'
            );
            if ($exec_info['code'] !== 0) {
                $return = array('success'=> false, 'msg'=> $exec_info['out']);
                return $return;
            }

    //        $cmd = "convert -strip -interlace plane '$path_out' '$path_out'";
    //        shell_exec($cmd);
    //        $cmd = "convert -resize {$thumb_w}x{$thumb_h} '$path_in' '$path_out' 2>&1";
    //        shell_exec($cmd);
        }
        return $return;
    }
    function gen_fill_thumbnail($path_in, $path_out, $dest_w, $dest_h, $method = 'GD') {
        //$return = array('success'=> true, 'msg'=> '', 'output_w'=> $maxW, 'output_h'=> $maxH);

        $type = get_image_type_name($path_in);
        $src = imagecreatefrom_by_type($path_in);
        // get the source image's width and height
        $src_w = imagesx($src);
        $src_h = imagesy($src);

        $src_r = 1.0 * $src_w / (1.0 * $src_h);
        $dest_r = 1.0 * $dest_w / (1.0 * $dest_h);
        if ($dest_r > $src_r) {
            // grow width to fill
            $new_w = $dest_w;
            $new_h = resize_h_by_w($src_h, $dest_w, $src_w);
        } else {
            // grow height to fill
            $new_h = $dest_h;
            $new_w = resize_w_by_h($src_w, $dest_h, $src_h);
        }

        $new_src_w = $new_w;
        $new_src_h = $new_h;

        if ('GD' == $method) {
            $new_src = imagecreatetruecolor($new_src_w, $new_src_h);
            // start resize
            imagecopyresized($new_src, $src, 0, 0, 0, 0, $new_src_w, $new_src_h, $src_w, $src_h);

        } else if ('ImageMagick' == $method) {
            $exports = array(
                'PATH'=> '$PATH:/opt/ImageMagick/bin/'
            );
            $exec_info = execute_external(
                '/opt/ImageMagick/bin/convert', array("-resize {$new_src_w}x{$new_src_h} '$path_in' '$path_out'"),
                '', $exports, '.'
            );

            $new_src = imagecreatefrom_by_type($path_out);
        }

        $crop_info = crop_size($new_src_w, $new_src_h, $dest_w, $dest_h);
        // http://farm8.staticflickr.com/7155/6772422375_74450dcbf7.jpg
        $thumb = imagecreatetruecolor($dest_w, $dest_h);
        $ret = imagecopyresampled(
            $thumb, $new_src,
            0, 0,
            $crop_info[0], $crop_info[1],
            $dest_w, $dest_h,
            $crop_info[2], $crop_info[3]
        );
        bde($ret);
        image_by_type($type, $thumb, $path_out);

        //return $return;
    }

    function thumb_hd (&$src, $x, $y) {
        // http://inspire.twgg.org/c/programming/php/using-php-function-imagecopyresized-gd-the-establishment-of-high-precision-thumbnail.html
        $dst=imagecreatetruecolor($x, $y);
        $pals=ImageColorsTotal ($src);

        for ($i=0; $i<$pals; $i++) {
            $colors=ImageColorsForIndex ($src, $i);
            ImageColorAllocate ($dst, $colors['red'], $colors['green'], $colors['blue']);
        }
        $scX =(imagesx ($src)-1)/$x;
        $scY =(imagesy ($src)-1)/$y;
        $scX2 =intval($scX/2);
        $scY2 =intval($scY/2);

        for ($j = 0; $j < ($y); $j++) {
            $sY = intval($j * $scY);
            $y13 = $sY + $scY2;
            for ($i = 0; $i < ($x); $i++) {
                $sX = intval($i * $scX);
                $x34 = $sX + $scX2;
                $c1 = ImageColorsForIndex ($src, ImageColorAt ($src, $sX, $y13));
                $c2 = ImageColorsForIndex ($src, ImageColorAt ($src, $sX, $sY));
                $c3 = ImageColorsForIndex ($src, ImageColorAt ($src, $x34, $y13));
                $c4 = ImageColorsForIndex ($src, ImageColorAt ($src, $x34, $sY));
                $r = ($c1['red']+$c2['red']+$c3['red']+$c4['red'])/4;
                $g = ($c1['green']+$c2['green']+$c3['green']+$c4['green'])/4;
                $b = ($c1['blue']+$c2['blue']+$c3['blue']+$c4['blue'])/4;
                ImageSetPixel ($dst, $i, $j, ImageColorClosest ($dst, $r, $g, $b));
            }
        }
        return ($dst);
    }

    function fit_size($origW, $origH, $maxW, $maxH) {
        // resize the given dimension into $maxW x $maxH rectangle
        // but keep the original ratio of dimension
        $newH = $origH;
        $newW = $origW;
        $landscape = ($origW > $origH) ? true : false;

        if ($landscape) {
            if ($origW > $maxW) {
                $newW = $maxW;
                $newH = resize_h_by_w($origH, $maxW, $origW);
            }
        } else {
            // portrait
            if ($origH > $maxH) {
                $newH = $maxH;
                $newW = resize_w_by_h($origW, $maxH, $origH);
            }
        }
        return array('width' => round($newW), 'height' => round($newH));
    }
    function resize_h_by_w($origH, $newW, $origW) {
        return $origH * ($newW / $origW);
    }
    function resize_w_by_h($origW, $newH, $origH) {
        return $origW * ($newH / $origH);
    }

    function crop_size($srcW, $srcH, $destW, $destH) {
        // crop the given dimension from the center to fill destW x destH rectangle
        // but keep the original ratio of dimension
        if ($destW > $srcW || $destH > $srcH) {
            // can not crop
            return array('width' => $srcW, 'height' => $srcH);
        }
        // http://php.net/manual/en/function.imagecrop.php
        return array(
            round(($srcW - $destW) / 2.0), // x
            round(($srcH - $destH) / 2.0), // y
            round($destW), // w
            round($destH), // h

        );
    }


    function purify_image($path_image, $file_info) {
        $return = array('success'=> true, 'msg'=> '');
        try {
            switch ($file_info['_image_type']) {
                case 'jpeg':
                case 'png':
                case 'gif':
                    $res = imagecreatefrom_by_type($path_image);
                    image_by_type($file_info['_image_type'], $res, $path_image);
                    break;
                default:
                    break;
            }
        } catch (Exception $e) {
            $return['success'] = false;
            $return['msg'] = $e->getMessage();
        }
        return $return;
    }