<?php

namespace Realweb\Category;

\Bitrix\Main\Loader::includeModule('realweb.api');

class Entity
{

    protected $_data;

    public function __construct($arData)
    {
        $this->_data = $arData;
    }

    public function getId()
    {
        return $this->_data['ID'];
    }

}
