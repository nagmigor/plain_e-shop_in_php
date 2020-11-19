<?php

namespace components;

use exceptions\RouteException;
/**
 * Класс Router
 * Компонент для работы с маршрутами
 */
class Router
{
    static private $_instace;
    
    /**
     * Свойство для хранения массива роутов
     * @var array 
     */ 
    private $routes; 
    /**
     * Имя контроллера
     * @var type string
     */
    private $controller = 'index';
    /**
     * Имя метода
     * @var type 
     */
    private $action = 'index';
    /**
     * Передаваемые данные
     * @var type 
     */
    
    private $parameters;

    private function __clone() {
        
    }
    
    static public function getInstance() {
       
       if(self::$_instace instanceof self) {
           return self::$_instace;
       }
       
       return self::$_instace = new self;
    }

    /**
     * Конструктор
     */
    private function __construct()
    {
       // получаем массив правил получения внутренних путей из внешних
       $this->routes = routes();
       
       // получение адресной сторки
       $uri = self::getURI();
        
        // Проверяем наличие такого запроса в массиве маршрутов
        foreach ($this->routes as $uriPattern => $path) {
            // Сравниваем $uriPattern и $uri
            if (preg_match("~$uriPattern~", $uri)) { 
                // preg_match - проверка на соответсвие регулярному выражению
                 
                // Получаем внутренний путь из внешнего согласно правилу.
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);
                // preg_replace - поиск и замена по регулярному выражению
                
                // Определить контроллер, action, параметры
               
                // Разбивает строку с помощью разделителя
                // Возвращает массив строк полученных разбиением
                $segments = explode('/', $internalRoute);
                // определение контроллера
                // Извлекает первый элемент массива
                $controllerName = array_shift($segments) . 'Controller';
                // Преобразует первый символ строки в верхний регистр
                $this->controller = ucfirst($controllerName);
                // определение метода
                $this->action = 'action' . ucfirst(array_shift($segments));
                // определение параметров
                $this->parameters = $segments;
                return;
            }  
        }
    }

    /**
     * Возвращает строку запроса
     */
    private function getURI()
    {
         // получение адресной сторки
       $uri = $_SERVER['REQUEST_URI'];
       // если адресная строка заканчивается слешем и не является корнем сайта
        if(strrpos($uri, '/') === strlen($uri) - 1 && strrpos($uri, '/') !==0) {
            // strrpos - позиция последнего вхождения отсчитывается от 0
            // strlen - длина строки считается с 1 позиции
            // удаляем слэш из конца строки
            //return rtrim($uri, '/')
            // то перенаправляем на адрес без слеша
            $uri = rtrim($uri, '/');
            header ("Location: {$uri}");     
        }
        return trim($uri, '/');
    }
    
    /**
     * Метод для вызова методов маршрутов
     */
    public function run() {

        $controller = str_replace('/', '\\', PATH_PREFIX . $this->controller);

        try{
           
            $page = new \ReflectionMethod($controller, $this->action); // первый параметр имя класса
                                                                        // второй имя метода
            // Ввызов метода $this->action с параметрами $this->parameters
            $page->invoke(new $controller, $this->parameters);
            
        } catch (\ReflectionException $e) {
            throw new RouteException('Страница не существует');
            // throw new RouteException($e);
        }                     
    }
}

