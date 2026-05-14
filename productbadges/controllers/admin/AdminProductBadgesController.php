<?php

class AdminProductBadgesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'badge';
        $this->className = 'Badge';
        $this->bootstrap = true;
        $this->lang = false;

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