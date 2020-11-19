<?php
namespace components;

/**
 * Подключение к БД
 *
 */
class Db
{
	private static $connection = null;


	/**
  * Устанавливает соединение с базой данных
  * @return \PDO <p>Объект класса PDO для работы с БД</p>
  */
	public static function getConnection()
	{
		if (!static::$connection) {
			try { 
                            static::$connection = new \PDO(
						"mysql:host=" . HOST . ";dbname=" . DB_NAME, 
						USER,
						PASS
					);
			} catch(Exception $e) {
				die($e->getMessage());
			}
		}

		return static::$connection;
	}

	private function __construct(){
            
        }
        
        private function __clone() {
        
        }
        
        

}
