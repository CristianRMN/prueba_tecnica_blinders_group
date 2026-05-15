<?php

class AdminProductBadgesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'badge';
        $this->className = 'Badge';
        $this->bootstrap = true;
        $this->lang = true;

        parent::__construct();

        $this->fields_list = [
            'id_badge' => [
                'title' => 'ID',
            ],
            'name' => [
                'title' => 'Name',
            ],
            'background_color' => [
                'title' => 'Background color',
            ],
            'text_color' => [
                'title' => 'Text color',
            ],
            'position' => [
                'title' => 'Position',
            ],
            'active' => [
                'title' => 'Active',
                'type' => 'bool',
                'active' => 'status',
            ],
        ];
    }

    private function getProductBadges($idProduct)
    {
        $idLang = (int) $this->context->language->id;

        $sql = 'SELECT b.*, bl.name, pb.id_product
                FROM ' . _DB_PREFIX_ . 'badge b
                INNER JOIN ' . _DB_PREFIX_ . 'badge_lang bl ON bl.id_badge = b.id_badge AND bl.id_lang = ' . $idLang . '
                INNER JOIN ' . _DB_PREFIX_ . 'product_badge pb ON pb.id_badge = b.id_badge
                WHERE pb.id_product = ' . (int) $idProduct;

        return Db::getInstance()->executeS($sql);
    }

    public function ajaxProcessAddBadges()
    {
        $idProduct = (int) Tools::getValue('id_product');
        $badges = Tools::getValue('badges');

        if ($idProduct <= 0 || !is_array($badges)) {
            die(json_encode(['success' => false]));
        }

        foreach ($badges as $idBadge) {
            $idBadge = (int) $idBadge;
            if ($idBadge <= 0) {
                continue;
            }

            $exists = Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'product_badge
                 WHERE id_product = ' . $idProduct . ' AND id_badge = ' . $idBadge
            );

            if (!$exists) {
                Db::getInstance()->insert('product_badge', [
                    'id_product' => $idProduct,
                    'id_badge' => $idBadge,
                ]);
            }
        }

        die(json_encode([
            'success' => true,
            'badges' => $this->getProductBadges($idProduct),
        ]));
    }

    public function ajaxProcessRemoveBadge()
    {
        $idProduct = (int) Tools::getValue('id_product');
        $idBadge = (int) Tools::getValue('id_badge');

        if ($idProduct <= 0 || $idBadge <= 0) {
            die(json_encode(['success' => false]));
        }

        Db::getInstance()->delete('product_badge',
            'id_product = ' . $idProduct . ' AND id_badge = ' . $idBadge
        );

        die(json_encode([
            'success' => true,
            'badges' => $this->getProductBadges($idProduct),
        ]));
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => 'Badge',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => 'Name',
                    'name' => 'name',
                    'required' => true,
                ],
                [
                    'type' => 'color',
                    'label' => 'Background color',
                    'name' => 'background_color',
                    'required' => true,
                ],
                [
                    'type' => 'color',
                    'label' => 'Text color',
                    'name' => 'text_color',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => 'Position',
                    'name' => 'position',
                    'options' => [
                        'query' => [
                            [
                                'id' => 'left',
                                'name' => 'Left',
                            ],
                            [
                                'id' => 'right',
                                'name' => 'Right',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => 'Active',
                    'name' => 'active',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => 'Yes',
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => 'No',
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => 'Save',
            ],
        ];

        return parent::renderForm();
    }
}