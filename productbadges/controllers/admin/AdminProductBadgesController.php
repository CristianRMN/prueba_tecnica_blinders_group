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
}