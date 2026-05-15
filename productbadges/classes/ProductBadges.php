<?php

class Badge extends ObjectModel
{
    public $id_badge;
    public $name;
    public $background_color;
    public $text_color;
    public $position;
    public $active;

    public static $definition = [
        'table' => 'badge',
        'primary' => 'id_badge',
        'multilang' => true,
        'fields' => [
            'name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 50
            ],
            'background_color' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 10
            ],
            'text_color' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 10
            ],
            'position' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 20
            ],
            'active' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true
            ]
        ]
    ];
}