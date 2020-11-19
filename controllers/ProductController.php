<?php
namespace controllers;

use models\Category;
use models\Product;

/**
 * Контроллер ProductController
 * Товар
 */
class ProductController
{

    /**
     * Action для страницы просмотра товара
     *
     * @param integer $productId <p>id просматриваемого товара</p>
     */
    public function actionView($getProdId)
    {
        $productId = (is_array($getProdId)) ? array_shift($getProdId) : $getProdId;
        // Список категорий для левого меню
        $categories = Category::getCategoriesList();
 
        // Получаем инфомрацию о товаре
        $product = Product::getProductById($productId);

        // Подключаем вид
        require_once('views/product/view.php');

        return true;
    }

}
