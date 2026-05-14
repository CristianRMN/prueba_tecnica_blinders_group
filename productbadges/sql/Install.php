<?php
class Install
{
    public function install()
    {
        $column_one = Db::getInstance()->execute("
            CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "badge (
                id_badge INT AUTO_INCREMENT,
                name VARCHAR(50) NOT NULL,
                background_color VARCHAR(10) NOT NULL,
                text_color VARCHAR(10) NOT NULL,
                position VARCHAR(20) NOT NULL,
                active TINYINT(1) NOT NULL DEFAULT 1,
                PRIMARY KEY (id_badge)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;
        ");

        $column_two = Db::getInstance()->execute("
            CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "product_badge (
                id_product INT NOT NULL,
                id_badge INT NOT NULL,
                PRIMARY KEY (id_product, id_badge)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4;
        ");

        return $column_one && $column_two;
    }
}