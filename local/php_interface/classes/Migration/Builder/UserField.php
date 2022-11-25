<?php

namespace Migration\Builder;

class UserField extends \WS\ReduceMigrations\Builder\Entity\UserField
{

    public function __construct($code, $entity)
    {
        $this->code(strtoupper($code));
        $this->enumVariants = array();
        $this->entity = $entity;

        $rsData = \CUserTypeEntity::GetList(array($by => $order), array(
            "ENTITY_ID" => $entity,
            'FIELD_NAME' => $code,
        ));

        if ($arRes = $rsData->Fetch()) {
            $this->setId($arRes['ID']);
        }
    }

    public function save()
    {
        $this->commit();
    }

    public function delete()
    {
        $gw = new \CUserTypeEntity();
        $res = true;
        if ($this->getId() > 0) {
            $res = $gw->Delete($this->getId());
        }
    }

    private function commit()
    {
        try {
            global $APPLICATION;

            $gw = new \CUserTypeEntity();
            $res = true;

            if ($this->getId() > 0) {

                $res = $gw->Update($this->getId(), $this->getData());
            } else {

                $res = $gw->Add(array_merge($this->getData(), array(
                    'ENTITY_ID' => $this->entity,
                )));

                if ($res) {
                    $this->setId($res);
                }
            }

            if (!$res) {
                throw new \WS\ReduceMigrations\Builder\BuilderException($APPLICATION->GetException()->GetString());
            }

            $this->commitEnum();
        } catch (\WS\ReduceMigrations\Builder\BuilderException $e) {
            throw new \WS\ReduceMigrations\Builder\BuilderException($e->getMessage());
        }
    }

    private function commitEnum()
    {
        global $APPLICATION;
        $obEnum = new \CUserFieldEnum;
        $values = array();
        foreach ($this->getEnumVariants() as $key => $variant) {
            $key = 'n' . $key;
            if ($variant->getId() > 0) {
                $key = $variant->getId();
            }
            $values[$key] = $variant->getData();
        }
        if (empty($values)) {
            return;
        }
        if (!$obEnum->SetEnumValues($this->getId(), $values)) {
            throw new \WS\ReduceMigrations\Builder\BuilderException($APPLICATION->GetException()->GetString());
        }
    }

    public function GetEntity()
    {
        return $this->entity;
    }

    public function SetEntity($entity)
    {
        $this->entity = $entity;
    }

}
