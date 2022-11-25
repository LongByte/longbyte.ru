<?php

namespace Migration\Builder;

class EventsBuilder extends \WS\ReduceMigrations\Builder\EventsBuilder
{

    public function GetEventType($type, $lid)
    {
        $data = \CEventType::GetList(array(
            'TYPE_ID' => $type,
            'LID' => $lid,
        ))->Fetch();
        if (empty($data)) {
            return false;
        }
        return $data;
    }

}
