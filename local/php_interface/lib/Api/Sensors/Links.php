<?php

namespace Api\Sensors;

/**
 * Class \Api\Sensors\Links
 */
class Links
{

    private string $_folder = '/sensors/';
    private string $_edit = 'edit/';
    private string $_stat = 'stat/';

    private static ?\Api\Sensors\Links $instance = null;

    protected function __construct()
    {

    }

    public static function getInstance(): Links
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getFolder(): string
    {
        return $this->_folder;
    }

    public function getSystemRelativeUrl(string $strDetail): string
    {
        return $strDetail . '/';
    }

    public function getEditRelativeUrl(): string
    {
        return $this->_edit;
    }

    public function getStatRelativeUrl(): string
    {
        return $this->_stat;
    }

    public function getSystemUrl(string $strDetail): string
    {
        return $this->getFolder() . $this->getSystemRelativeUrl($strDetail);
    }

    public function getEditUrl(string $strDetail): string
    {
        return $this->getFolder() . $this->getSystemRelativeUrl($strDetail) . $this->getEditRelativeUrl();
    }

    public function getStatUrl(string $strDetail): string
    {
        return $this->getFolder() . $this->getSystemRelativeUrl($strDetail) . $this->getStatRelativeUrl();
    }

    public function getStatSinceUrl(string $strDetail, string $strSince): string
    {
        $obUri = new \Api\Core\Main\Uri($this->getFolder() . $this->getSystemRelativeUrl($strDetail) . $this->getStatRelativeUrl());
        $obUri->addParam('since', $strSince);

        return $obUri->getUri();
    }

}
