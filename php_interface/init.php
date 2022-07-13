<?php
function includeFile($relPath)
{
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $relPath;
    if (is_file($filePath)) {
        require $filePath;
    }
}

//constants
includeFile('/local/php_interface/include/constants.php');

//classes loader
includeFile('/local/php_interface/include/classLoader.php');

//functions
includeFile('/local/php_interface/include/functions.php');
