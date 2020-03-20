<?php

namespace Api\Core\Entity;

/**
 * Class \Api\Core\Entity\Base
 *
 */
abstract class Base {

    /**
     * @var array|int
     */
    protected $_primary;

    /**
     * @var bool
     */
    protected $_exist = false;

    /**
     * @var bool
     */
    protected $_changed = false;

    /**
     * @var array
     */
    protected $_data;

    /**
     *
     * @var array
     */
    protected static $arFields = array('ID');

    /**
     * @return \Api\Core\Model\Base
     */
    abstract protected static function getModel();

    /**
     * DataEntity constructor.
     * @param null $primary
     * @param array $data
     */
    public function __construct($primary = null, $data = array()) {
        if ($data) {
            $this->_data = $data;
            if ($primary === null) {
                $primaryField = static::getTable()::getEntity()->getPrimary();
                if (is_array($primaryField)) {
                    foreach ($primaryField as $strField) {
                        $primary[$strField] = array_key_exists($strField, $data) ? $data[$strField] : null;
                    }
                } else {
                    $primary = $data[$primaryField];
                }
            }
            if ($primary !== null) {
                $this->_primary = $primary;
                $this->_exist = true;
            }
        } elseif ($primary !== null) {
            $this->_primary = $primary;
            $this->getData();
        }
    }

    /**
     * @return array|false
     */
    public function getData() {
        if (is_null($this->_data)) {
            $this->_data = array_fill_keys(static::getFields(), '');

            $primaryField = static::getTable()::getEntity()->getPrimary();
            if (is_array($primaryField)) {
                $arPrimaryFilter = $this->_primary;
            } else {
                $arPrimaryFilter = array($primaryField => $this->_primary);
            }

            if ($arPrimaryFilter !== null) {
                $_arData = static::getTable()::getRow(array(
                        'filter' => $arPrimaryFilter,
                ));
                if ($_arData) {
                    $this->_data = $_arData;
                    $this->_exist = true;
                }
            }
        }

        return $this->_data;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|mixed
     */
    public function __call($name, $arguments) {
        if ((strpos($name, "get") === 0)) {

            $strKey = substr_replace($name, "", 0, 3);
            preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
            $arField = array();
            foreach ($res as $arRes) {
                $arField[] = $arRes[0];
            }
            $strField = self::toUpper(implode('_', $arField));
            $arData = $this->_data;
            if (array_key_exists($strField, $arData)) {
                return $arData[$strField];
            } else {
                throw new \Exception("Call to undefined method {$name}");
            }
        } elseif ((strpos($name, "has") === 0)) {
            $strKey = substr_replace($name, "", 0, 3);
            preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
            $arField = array();
            foreach ($res as $arRes) {
                $arField[] = $arRes[0];
            }
            $strField = self::toUpper(implode('_', $arField));
            $arData = $this->_data;
            if (array_key_exists($strField, $arData)) {
                return true;
            } else {
                return false;
            }
        } elseif ((strpos($name, "set") === 0)) {
            $strKey = substr_replace($name, "", 0, 3);
            preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
            $arField = array();
            foreach ($res as $arRes) {
                $arField[] = $arRes[0];
            }
            $strField = self::toUpper(implode('_', $arField));
            $arData = $$this->_data;
            if (array_key_exists($strField, $arData)) {
                if ($this->checkChanges($this->_data[$strField], $arguments[0])) {
                    $this->_changed = true;
                }
                $this->_data[$strField] = $arguments[0];
                return $this;
            } else {
                throw new \Exception("Call to undefined method {$name}");
            }
        } else {
            throw new \Exception("Call to undefined method {$name}");
        }
    }

    /**
     * @return bool
     */
    public function isExist() {
        return $this->_exist;
    }

    /**
     * @return bool
     */
    public function isChanged() {
        return $this->_changed;
    }

    /**
     * @param null $data
     *
     * @return array
     */
    public function toArray($data = null) {
        $arArray = array();
        if (is_null($data)) {
            $data = $this->_data;
        }
        foreach ($data as $strKey => $value) {
            if (strpos($strKey, '~') === 0) {
                continue;
            }
            $strLowerKey = self::toLower($strKey);
            if (is_array($value)) {
                $arArray[$strLowerKey] = $this->toArray($value);
            } else {
                $arArray[$strLowerKey] = $value;
            }
        }

        return $arArray;
    }

    /**
     * @param $strString
     * @return string
     */
    public static function toLower($strString) {
        return ToLower($strString);
    }

    /**
     * @param $strString
     *
     * @return string
     */
    public static function toUpper($strString) {
        return ToUpper($strString);
    }

    public function getFields() {
        return static::$arFields;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function save() {
        $arFields = array();
        $arData = $this->getData();
        foreach (static::getTable()::getTableFields() as $strAliasField => $strTableField) {
            if (is_numeric($strAliasField)) {
                $strAliasField = $strTableField;
            }
            if (array_key_exists($strAliasField, $arData)) {
                $arFields[$strTableField] = $arData[$strAliasField];
            }
        }
        if ($this->isExist()) {
            $rsResult = static::getTable()::update($this->_primary, $arFields);
        } else {
            $rsResult = static::getTable()::add($arFields);
            if (intval($rsResult->getId()) > 0) {
                $this->_primary = $rsResult->getId();
                $this->_exist = true;
            }
        }
        $this->_changed = false;

        return $rsResult->isSuccess();
    }

    public function delete() {
        if ($this->isExist()) {
            $rsResult = static::getTable()::delete($this->_primary);
            $this->_exist = false;
            $this->_primary = null;
            $this->_changed = true;
        }

        return $rsResult->isSuccess();
    }

}
