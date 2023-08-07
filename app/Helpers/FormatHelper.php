<?php

use Illuminate\Validation\Rules\Exists;

if (!function_exists('rupiah_format')) {
    function rupiah_format($nominal){
        return number_format($nominal, 0, ",", ".");
    }
}

if (!function_exists('decimal_format')) {
    function decimal_format($int){
        return number_format($int, 0, ".", ".");
    }
}

if (!function_exists('file_size_format')) {
    function file_size_format($bytes, $decimals = 2){
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}

if(!function_exists('split_name')) {
    function split_name($name) {
        $name = trim($name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim( preg_replace('#'.preg_quote($last_name,'#').'#', '', $name ) );
        return array($first_name, $last_name);
    }
}

if (!function_exists('grading_map')) {
    function grading_map($number)
    {
        $letterMap = [
            1 => 'A',
            2 => 'B',
            3 => 'C',
            // Add more mappings as needed.
        ];

        return isset($letterMap[$number]) ? $letterMap[$number] : null;
    }
}

if (!function_exists('get_string_between')) {
    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
