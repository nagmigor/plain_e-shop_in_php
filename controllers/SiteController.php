<?php
namespace controllers;

use models\Category;
use models\Product;
use models\User;

class SiteController
{
    /**
     * Action для главной страницы
     */
    public function actionIndex()
    {

        // Список категорий для левого меню
        $categories = Category::getCategoriesList();

        // Список последних товаров
        $latestProducts = Product::getLatestProducts(6);
        
        // Список товаров для слайдера
        $sliderProducts = Product::getRecommendedProducts();
        
        // Подключаем вид
        require_once('views/site/index.php');
        return true;
    }
    
    /**
     * Action для страницы "Контакты"
     *
     */
    
    public function actionContact() {
        
        // Переменные для формы
        $userEmail = '';
        $userText = '';
        $result = false;
        
        // Обработка формы
        if (isset($_POST['submit'])) {
            // Если форма отправлена 
            // Получаем данные из формы
            $userEmail = $_POST['userEmail'];
            $userText = $_POST['userText'];
    
            // Флаг ошибок
            $errors = false;
                        
            // Валидация полей
            if (!User::checkEmail($userEmail)) {
                $errors[] = 'Неправильный email';
            }
            // Если ошибок нет
            // Отправляем письмо администратору
            if ($errors == false) {
                $adminEmail = 'igornagm@gmail.com';
                $message = "Текст: {$userText}. От {$userEmail}";
                $subject = 'Тема письма';    
                $result = mail($adminEmail, $subject, $message);
                $result = true;
            }

        }
        
        // Подключаем вид
        require_once('views/site/contact.php');
        
        return true;
    }
    
    /**
     * Action для страницы "О магазине"
     */
    public function actionAbout()
    {
        // Подключаем вид
        require_once('views/site/about.php');
        return true;
    }
}
