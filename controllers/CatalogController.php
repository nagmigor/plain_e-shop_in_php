<?php
namespace controllers;

use models\Category;
use models\Product;

use components\Pagination;

/**
 * Контроллер CatalogController
 * Каталог товаров
 */
class CatalogController
{

    /**
     * Action для страницы "Каталог товаров"
     */
    public function actionIndex()
    {
        // Список категорий для левого меню
        $categories = Category::getCategoriesList();

        // Список последних товаров
        $latestProducts = Product::getLatestProducts(12);
        // Подключаем вид
        require_once ('views/catalog/index.php');

        return true;
    }
    
    /**
     * Action для страницы "Категория товаров"
     * 
     * @param type $getId <p>id отображаемой категории</p>
     */
    public function actionCategory($getParam)
    {
        // Инициализация категории и страницы
        $categoryId = (is_array($getParam)) ? array_shift($getParam) : $getParam;
        $page = array_shift($getParam);
        $page = $page ?? 1;

       // Список категорий для левого меню
        $categories = Category::getCategoriesList();
  
        // Список товаров в категории
        $categoryProducts = Product::getProductsListByCategory($categoryId, $page);

        // Общее количетсво товаров (необходимо для постраничной навигации)
        $total = Product::getTotalProductsInCategory($categoryId);

        // Создаем объект Pagination - постраничная навигация
        $pagination = new Pagination($total, $page, Product::SHOW_BY_DEFAULT, 'page-');
  
        // Подключаем вид
        require_once ('views/catalog/category.php');

        return true;
    }

}
