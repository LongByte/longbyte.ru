<?php

$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen('/include.php'));

CModule::AddAutoloadClasses('realweb.main.include', array(
    '\\Realweb\\MainInclude' => 'classes/general/module.php',
    '\\Realweb\\RealwebMainIncludeTable' => 'lib/realweb_main_include.php',
    '\\Realweb\\RealwebMainIncludeCategoryTable' => 'lib/realweb_main_include_category.php',
    '\\Realweb\\Category\\Entity' => 'lib/category/Entity.php',
        )
);
