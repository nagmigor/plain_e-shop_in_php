<?php

define('VG_ACCESS', true);  // Определение константы безопасности

// FRONT CONTROLLER

// Общие настройки
ini_set('display_errors',1); // Отображение ошибок
error_reporting(E_ALL);			 // при разработке проекта;
														 // на готовом проекте отключается
session_start();

// Подключение файлов системы
require_once 'components/functions.php'; //подключение отладочной функции
require_once 'config/config.php'; // Подключение констант конфигурации системы
require_once 'config/startup_settings.php'; // Подключение сервисных функций и настроек системы

use components\Router;
use exceptions\RouteException;

define('ROOT', dirname(__FILE__));

// Вызов Router
try{
   Router::getInstance()->run();
 
} catch (RouteException $e) {
    exit($e->getMessage());
}
