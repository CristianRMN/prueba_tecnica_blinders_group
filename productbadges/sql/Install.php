<?php
class Install
{
    public function install()
    {
        $tableBadge = Db::getInstance()->execute("
            CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "badge (
                id_badge INT AUTO_INCREMENT,
                background_color VARCHAR(10) NOT NULL,
                text_color VARCHAR(10) NOT NULL,
                position VARCHAR(20) NOT NULL,
                active TINYINT(1) NOT NULL DEFAULT 1,
                PRIMARY KEY (id_badge)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;
        ");

        $tableBadgeLang = Db::getInstance()->execute("
            CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "badge_lang (
                id_badge INT NOT NULL,
                id_lang INT NOT NULL,
                name VARCHAR(50) NOT NULL,
                PRIMARY KEY (id_badge, id_lang)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;
        ");

        $tableProductBadge = Db::getInstance()->execute("
            CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "product_badge (
                id_product INT NOT NULL,
                id_badge INT NOT NULL,
                PRIMARY KEY (id_product, id_badge)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4;
        ");

        return $tableBadge && $tableBadgeLang && $tableProductBadge;
    }

    public function installTab(Module $module)
    {
        $tab = new Tab();

        $tab->active = 1;
        $tab->class_name = 'AdminProductBadges';
        $tab->name = [];

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Product Badge';
        }

        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');

        $tab->module = $module->name;

        return $tab->add();
    }


}