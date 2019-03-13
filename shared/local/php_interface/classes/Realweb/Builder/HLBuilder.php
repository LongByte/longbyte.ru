<?php

namespace Realweb\Builder;

class HLBuilder extends \WS\ReduceMigrations\Builder\HighLoadBlockBuilder {

    public function GetIblock($CODE) {
        $row = \Bitrix\Highloadblock\HighloadBlockTable::getRow(array(
                    'filter' => array(
                        'NAME' => $CODE
                    )
        ));
        return $row;
    }

}
