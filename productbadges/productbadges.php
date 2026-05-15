<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/sql/Install.php';
require_once __DIR__ . '/sql/Uninstall.php';
require_once __DIR__ . '/classes/ProductBadges.php';

class productbadges extends Module
{

    public function __construct()
    {
        $this->name = 'productbadges';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Cristian Regueiro Martínez';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.8.11',
            'max' => '9.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Product Badges', [], 'Modules.Productbadges.Admin');
        $this->description = $this->trans('Technical test by the company Blinders Group for carrying out the establishment of labels for products', [], 'Modules.Productbadges.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Productbadges.Admin');

        if (!Configuration::get('product_badges_module')) {
            $this->warning = $this->trans('No name provided', [], 'Modules.Productbadges.Admin');
        }

    }

    public function install()
    {
        $install = new Install();
        return parent::install() && $install->install()
            && $install->installTab($this)
            && $this->registerHook('displayAdminProductsMainStepRightColumnBottom')
            && $this->registerHook('actionProductSave')
            && $this->registerHook('displayAfterProductThumbs')
            && $this->registerHook('displayProductListReviews')
            && $this->registerHook('actionFrontControllerSetMedia');
    }

    public function uninstall()
    {
        $uninstall = new Uninstall();
        return parent::uninstall() && $uninstall->uninstall()
            && $uninstall->uninstallTab()
            && $this->unregisterHook('displayAdminProductsMainStepRightColumnBottom')
            && $this->unregisterHook('actionProductSave')
            && $this->unregisterHook('displayAfterProductThumbs')
            && $this->unregisterHook('displayProductListReviews')
            && $this->unregisterHook('actionFrontControllerSetMedia');
    }

    private function getBadgesByProduct($idProduct)
    {
        $sql = 'SELECT b.*
                FROM ' . _DB_PREFIX_ . 'badge b
                INNER JOIN ' . _DB_PREFIX_ . 'product_badge pb ON pb.id_badge = b.id_badge
                WHERE pb.id_product = ' . (int) $idProduct . '
                AND b.active = 1';

        return Db::getInstance()->executeS($sql);
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-productbadges',
            'modules/' . $this->name . '/views/css/product_badges.css',
            ['media' => 'all', 'priority' => 150]
        );
    }

    public function hookDisplayAfterProductThumbs($params)
    {
        $idProduct = (int) $params['product']['id_product'];
        if ($idProduct <= 0) {
            return '';
        }

        $badges = $this->getBadgesByProduct($idProduct);
        if (empty($badges)) {
            return '';
        }

        $this->context->smarty->assign([
            'product_badges' => $badges,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/product_badges_detail.tpl');
    }

    public function hookDisplayProductListReviews($params)
    {
        $idProduct = (int) $params['product']['id_product'];
        if ($idProduct <= 0) {
            return '';
        }

        $badges = $this->getBadgesByProduct($idProduct);
        if (empty($badges)) {
            return '';
        }

        $this->context->smarty->assign([
            'product_badges' => $badges,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/product_badges_list.tpl');
    }

    public function hookDisplayAdminProductsMainStepRightColumnBottom($params)
    {
        $idProduct = (int) $params['id_product'];
        if ($idProduct <= 0) {
            return '';
        }

        $sql = 'SELECT b.*
                FROM ' . _DB_PREFIX_ . 'badge b
                INNER JOIN ' . _DB_PREFIX_ . 'product_badge pb ON pb.id_badge = b.id_badge
                WHERE pb.id_product = ' . (int) $idProduct;

        $badges = Db::getInstance()->executeS($sql);
        $all_badges = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'badge');
        $adminUrl = $this->context->link->getAdminLink('AdminProductBadges');

        $this->context->smarty->assign([
            'badges' => $badges,
            'all_badges' => $all_badges,
            'id_product' => $idProduct,
            'admin_product_badges_url' => $adminUrl,
        ]);

        $this->context->smarty->assign([
            'product_badges_js_url' => $this->getPathUri() . 'views/js/product_badges.js',
        ]);

        return $this->display(__FILE__, 'views/templates/admin/product_badges.tpl');


    }

    public function hookActionProductSave($params)
    {

    }


}
