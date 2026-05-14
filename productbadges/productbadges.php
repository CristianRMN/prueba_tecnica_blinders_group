<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


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
    
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }


}
