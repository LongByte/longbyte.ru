<?php

namespace Api\Sensors;

/**
 * Class \Api\Sensors\Links
 */
class Links {

    private $_folder = '/sensors/';
    private $_edit = 'edit/';
    private $_stat = 'stat/';

    /*
     * @var \Api\Sensors\Links
     */
    private static $instance = null;

    protected function __construct() {
        
    }

    /**
     * 
     * @return $this
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Self();
        }
        return self::$instance;
    }

    /**
     * 
     * @return string
     */
    public function getFolder(): string {
        return $this->_folder;
    }

    /**
     * 
     * @param string $strDetail
     * @return string
     */
    public function getSystemRelativeUrl(string $strDetail): string {
        return $strDetail . '/';
    }

    /**
     * 
     * @return string
     */
    public function getEditRelativeUrl(): string {
        return $this->_edit;
    }

    /**
     * 
     * @return string
     */
    public function getStatRelativeUrl(): string {
        return $this->_stat;
    }

    /**
     * 
     * @param string $strDetail
     * @return string
     */
    public function getSystemUrl(string $strDetail): string {
        return $this->getFolder() . $this->getSystemRelativeUrl($strDetail);
    }

    /**
     * 
     * @param string $strDetail
     * @return string
     */
    public function getEditUrl(string $strDetail): string {
        return $this->getFolder() . $this->getSystemRelativeUrl($strDetail) . $this->getEditRelativeUrl();
    }

    /**
     * 
     * @param string $strDetail
     * @return string
     */
    public function getStatUrl(string $strDetail): string {
        return $this->getFolder() . $this->getSystemRelativeUrl($strDetail) . $this->getStatRelativeUrl();
    }

    /**
     * 
     * @param string $strDetail
     * @param string $strSince
     * @return string
     */
    public function getStatSinceUrl(string $strDetail, string $strSince): string {

        $obUri = new \Api\Core\Main\Uri($this->getFolder() . $this->getSystemRelativeUrl($strDetail) . $this->getStatRelativeUrl());
        $obUri->addParam('since', $strSince);

        return $obUri->getUri();
    }

}
