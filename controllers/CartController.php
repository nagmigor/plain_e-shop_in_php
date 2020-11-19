<?php

namespace controllers;

use components\Cart;
use models\Product;
use models\Category;
use models\User;
use models\Order;

/**
 * Контроллер CartController
 * Корзина
 */
class CartController {
    
    /**
     * Action для добавления товара в корзину синхронным запросом<br/>
     * (для примера, не используется)
     * @param integer $id <p>id товара</p>
     */
    public function actionAdd($getId)
    {
        $id = (is_array($getId)) ? array_shift($getId) : $getId;
        // Добавляем товар в корзину
        Cart::addProduct($id);

        // Возвращаем пользователя на страницу с которой он пришел
        $referrer = $_SERVER['HTTP_REFERER'];
        header("Location: $referrer");
    }

    /**
     * Action для удаления товара из корзины
     * @param integer $id <p>id товара удаляемого из корзины</p>
     */
    public function actionDelete($getId)
    {
        $id = (is_array($getId)) ? array_shift($getId) : $getId;
        
        // Удалить товар из корзины
        Cart::deleteProduct($id);
        // Возвращаем пользователя на страницу
        header("Location: /cart");
    }
    
    /**
     * Action для добавления товара в корзину при помощи асинхронного запроса (ajax)
     * @param integer $id <p>id товара</p>
     */
    public function actionAddAjax($getId)
    {
        // Добавляем товар в корзину и печатаем результат: количество товаров в корзине
        $id = (is_array($getId)) ? array_shift($getId) : $getId;
        echo Cart::addProduct($id);
        return true;
    }

    /**
     * Action для отображения страницы "Корзина"
     */
    public function actionIndex()
    {
        // Список категорий для левого меню
        $categories = [];
        $categories = Category::getCategoriesList();

        $productsInCart = false;

        // Получим идентификаторы и количество товаров в корзине
        $productsInCart = Cart::getProducts();

        if ($productsInCart) {
            // Если в корзине есть товары, получаем полную информацию о товарах для списка
            // Получаем массив только с идентификаторами товаров
            $productsIds = array_keys($productsInCart);
            
            // Получаем массив с полной информацией о необходимых товарах
            $products = Product::getProductsByIds($productsIds);

            // Получаем общую стоимость товаров
            $totalPrice = Cart::getTotalPrice($products);
        }

        // Подключаем вид
        require_once('views/cart/index.php');

        return true;
    }
    
    /**
     * Action для страницы "Оформление покупки"
     */
    public function actionCheckout()
    {
         // Получием данные из корзины      
        $productsInCart = Cart::getProducts();

        // Если товаров нет, отправляем пользователи искать товары на главную
        if ($productsInCart == false) {
            header("Location: /");
        }

        // Список категорий для левого меню
        $categories = [];
        $categories = Category::getCategoriesList();

        // Находим общую стоимость
        $productsIds = array_keys($productsInCart);
        $products = Product::getProductsByIds($productsIds);
        $totalPrice = Cart::getTotalPrice($products);

        // Количество товаров
        $totalQuantity = Cart::countItems();

        // Поля для формы
        $userName = false;
        $userPhone = false;
        $userComment = false;
        
        // Статус успешного оформления заказа
        $result = false;

        // Проверяем является ли пользователь гостем
        if (!User::isGuest()) {
            // Если пользователь не гость
            // Получаем информацию о пользователе из БД
            $userId = User::checkLogged();
            $user = User::getUserById($userId);
            $userName = $user['name'];
        } else {
            // Если гость, поля формы останутся пустыми
            $userId = false;
        }

        // Обработка формы
        if (isset($_POST['submit'])) {
            // Если форма отправлена
            // Получаем данные из формы
            $userName = $_POST['userName'];
            $userPhone = $_POST['userPhone'];
            $userComment = $_POST['userComment'];

            // Флаг ошибок
            $errors = false;
            
            // Валидация полей
            if (!User::checkName($userName))
                $errors[] = 'Неправильное имя';
            if (!User::checkPhone($userPhone))
                $errors[] = 'Неправильный телефон';

            // Форма заполнена корректно?
            if ($errors == false) {
                // Если ошибок нет                
                // Сохраняем заказ в БД
                $result = Order::save($userName, $userPhone, $userComment, $userId, $productsInCart);

                // Если заказ успешно сохранен
                if ($result) {
                    // Оповещаем администратора о новом заказе                
                    $adminEmail = 'php.start@mail.ru';
                    $message = 'http://digital-mafia.net/admin/orders';
                    $subject = 'Новый заказ!';
                    mail($adminEmail, $subject, $message);

                    // Очищаем корзину
                    Cart::clear();
                }        
            }
        }

        // Подключаем вид
        require_once('views/cart/checkout.php');

        return true;
    }
}
