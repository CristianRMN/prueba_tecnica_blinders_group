<?php

class Uninstall
{
    public function uninstall()
    {
        $column_one = Db::getInstance()->execute("
                DROP TABLE IF EXISTS " . _DB_PREFIX_ . "product_badge");
        $column_two = Db::getInstance()->execute("
                DROP TABLE IF EXISTS " . _DB_PREFIX_ . "badge ");
        
        return $column_one && $column_two;
    }
}