<?php

class Uninstall
{
    public function uninstall()
    {
        $tableProductBadge = Db::getInstance()->execute(
            "DROP TABLE IF EXISTS " . _DB_PREFIX_ . "product_badge"
        );
        $tableBadgeLang = Db::getInstance()->execute(
            "DROP TABLE IF EXISTS " . _DB_PREFIX_ . "badge_lang"
        );
        $tableBadge = Db::getInstance()->execute(
            "DROP TABLE IF EXISTS " . _DB_PREFIX_ . "badge"
        );

        return $tableProductBadge && $tableBadgeLang && $tableBadge;
    }

    public function uninstallTab()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminProductBadges');

        if ($idTab) {
            $tab = new Tab($idTab);
            return $tab->delete();
        }

        return true;
    }
}