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
            && $this->registerHook('displayAfterProductThumbs')
            && $this->registerHook('displayProductListReviews')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('PRODUCTBADGES_ENABLED', 1)
            && Configuration::updateValue('PRODUCTBADGES_SHOW_LIST', 1)
            && Configuration::updateValue('PRODUCTBADGES_SHOW_PRODUCT', 1)
            && Configuration::updateValue('PRODUCTBADGES_MAX_BADGES', 3);
    }

    public function uninstall()
    {
        $uninstall = new Uninstall();
        return parent::uninstall() && $uninstall->uninstall()
            && $uninstall->uninstallTab()
            && $this->unregisterHook('displayAdminProductsMainStepRightColumnBottom')
            && $this->unregisterHook('displayAfterProductThumbs')
            && $this->unregisterHook('displayProductListReviews')
            && $this->unregisterHook('actionFrontControllerSetMedia')
            && Configuration::deleteByName('PRODUCTBADGES_ENABLED')
            && Configuration::deleteByName('PRODUCTBADGES_SHOW_LIST')
            && Configuration::deleteByName('PRODUCTBADGES_SHOW_PRODUCT')
            && Configuration::deleteByName('PRODUCTBADGES_MAX_BADGES');
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitProductBadgesConfig')) {
            Configuration::updateValue('PRODUCTBADGES_ENABLED', (int) Tools::getValue('PRODUCTBADGES_ENABLED'));
            Configuration::updateValue('PRODUCTBADGES_SHOW_LIST', (int) Tools::getValue('PRODUCTBADGES_SHOW_LIST'));
            Configuration::updateValue('PRODUCTBADGES_SHOW_PRODUCT', (int) Tools::getValue('PRODUCTBADGES_SHOW_PRODUCT'));
            Configuration::updateValue('PRODUCTBADGES_MAX_BADGES', (int) Tools::getValue('PRODUCTBADGES_MAX_BADGES'));

            $output .= $this->displayConfirmation($this->trans('Settings updated.', [], 'Modules.Productbadges.Admin'));
        }

        return $output . $this->renderConfigForm();
    }

    private function renderConfigForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'submitProductBadgesConfig';

        $helper->fields_value = [
            'PRODUCTBADGES_ENABLED' => Configuration::get('PRODUCTBADGES_ENABLED'),
            'PRODUCTBADGES_SHOW_LIST' => Configuration::get('PRODUCTBADGES_SHOW_LIST'),
            'PRODUCTBADGES_SHOW_PRODUCT' => Configuration::get('PRODUCTBADGES_SHOW_PRODUCT'),
            'PRODUCTBADGES_MAX_BADGES' => Configuration::get('PRODUCTBADGES_MAX_BADGES'),
        ];

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Modules.Productbadges.Admin'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enable badges globally', [], 'Modules.Productbadges.Admin'),
                        'name' => 'PRODUCTBADGES_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'enabled_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                            ['id' => 'enabled_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Show in product listings', [], 'Modules.Productbadges.Admin'),
                        'name' => 'PRODUCTBADGES_SHOW_LIST',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'list_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                            ['id' => 'list_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Show in product detail page', [], 'Modules.Productbadges.Admin'),
                        'name' => 'PRODUCTBADGES_SHOW_PRODUCT',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'product_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                            ['id' => 'product_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Maximum badges per product', [], 'Modules.Productbadges.Admin'),
                        'name' => 'PRODUCTBADGES_MAX_BADGES',
                        'class' => 'fixed-width-sm',
                        'validation' => 'isUnsignedInt',
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        return $helper->generateForm([$form]);
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

 


}
