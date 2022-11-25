<?php
$arUrlRewrite = array(
    0 =>
        array(
            'CONDITION' => '#^/api/([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/?.*$#',
            'RULE' => '/api/index.php?module=\\1&controller=\\2',
            'ID' => '',
            'PATH' => '',
            'SORT' => 10,
        ),
    2 =>
        array(
            'CONDITION' => '#^/chart/admin/#',
            'RULE' => '',
            'ID' => 'longbyte:blank.route',
            'PATH' => '/chart/admin/index.php',
            'SORT' => 100,
        ),
    1 =>
        array(
            'CONDITION' => '#^/sensors/#',
            'RULE' => '',
            'ID' => 'longbyte:blank.route',
            'PATH' => '/sensors/index.php',
            'SORT' => 100,
        ),
    3 =>
        array(
            'CONDITION' => '#^/files/#',
            'RULE' => '',
            'ID' => 'longbyte:blank.route',
            'PATH' => '/files/index.php',
            'SORT' => 100,
        ),
    4 =>
        array(
            'CONDITION' => '#^/wiki/#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/wiki/index.php',
            'SORT' => 100,
        ),
    999 =>
        array(
            'CONDITION' => '#^/#',
            'RULE' => '',
            'ID' => 'bitrix:catalog',
            'PATH' => '/pages/index.php',
            'SORT' => 1000,
        ),
);
