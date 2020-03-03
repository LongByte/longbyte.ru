<?
$arUrlRewrite = array(
4 => array(
            'CONDITION' => '#^/wiki/#',
            'RULE' => '',
            'ID' => 'bitrix:news',
            'PATH' => '/wiki/index.php',
            'SORT' => 100,
        ),
5 => array(
            'CONDITION' => '#^/#',
            'RULE' => '',
            'ID' => 'bitrix:catalog',
            'PATH' => '/pages/index.php',
            'SORT' => 1000,
        ),
);
?>