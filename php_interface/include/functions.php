<?php
/**
 * Удаляет из массива дубли по нужному ключу
 *
 *
 * @param array $array
 * @param key $key
 *
 * @return array
 */
function array_unique_key($array, $key) {
    $tmp = $key_array = array();
    $i = 0;

    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $tmp[$i] = $val;
        }
        $i++;
    }
    return $tmp;
}

/**
 * Отдает нужную позицию в url
 *
 * @param $url
 * @param $i
 * @return false|mixed|string
 */
function getFirstSleshURl($url, $i)
{
    if (strlen($url) > 0) {
        $pieces = explode("/", $url);
        if (count($pieces > $i)) {
            return $pieces[$i];
        }
    }
    return false;
}

/**
 * Сортировка работает так  uasort($arPoints, 'cmp_function_desc');
 *
 * @param $a
 * @param $b
 * @return bool
 */
function cmp_function_desc($a, $b){
    return ($a['ID'] < $b['ID']);
}

