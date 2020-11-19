<?php
/**
 * Отладочная функция исопльзуется при разработке и отладке
 * @param type $value
 * @param type $die
 */
function  d($value = null, $die = 1)
{
    echo 'Debug: <br /><pre>';
    print_r($value);
    echo '</pre>';
    
    if ($die) die;   
}

