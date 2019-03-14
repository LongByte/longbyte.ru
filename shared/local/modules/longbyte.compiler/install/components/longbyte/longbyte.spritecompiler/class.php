<?php

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\SystemException as SystemException;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

class LongbyteSpriteCompilerComponent extends CBitrixComponent {

    private $arSVGFiles = array();

    /**
     * Check Required Modules
     * @throws Exception
     */
    protected function checkModules() {
        if (!Main\Loader::includeModule('longbyte.compiler')) {
            throw new SystemException(Loc::getMessage('CVP_LONGBYTE_COMPILER_MODULE_NOT_INSTALLED'));
        }
    }

    /**
     * Load language file
     */
    public function onIncludeComponentLang() {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * Prepare Component Params
     */
    public function onPrepareComponentParams($params) {
        $params['FILES'] = is_array($params['FILES']) ? $params['FILES'] : array();

        $params['REMOVE_OLD_SPRITE_FILES'] = ($params['REMOVE_OLD_SPRITE_FILES'] == 'Y');

        $params['PATH_TO_FILES'] = isset($params['PATH_TO_FILES']) && strlen(trim($params['PATH_TO_FILES'])) ? preg_replace(array('~^/~', '~/$~'), '/', trim($params['PATH_TO_FILES'])) : null;

        $params['PATH_TO_FILES_SPRITE'] = isset($params['PATH_TO_FILES_SPRITE']) && strlen(trim($params['PATH_TO_FILES_SPRITE'])) ? preg_replace(array('~^/~', '~/$~'), '/', trim($params['PATH_TO_FILES_SPRITE'])) : SITE_TEMPLATE_PATH . '/';

        $params['TARGET_FILE_MASK'] = trim($params['TARGET_FILE_MASK']);
        if (!strlen($params['TARGET_FILE_MASK']) || (strpos($params['TARGET_FILE_MASK'], '%s')) === false) {
            $params['TARGET_FILE_MASK'] = 'sprite-compiled-%s.svg';
        }

        $params['SHOW_ERRORS_IN_DISPLAY'] = ($params['SHOW_ERRORS_IN_DISPLAY'] == 'Y');

        return $params;
    }

    /*
     * Check the directory needed for component
     */

    protected function checkDirs() {
        if (!is_readable($_SERVER['DOCUMENT_ROOT'] . $this->arParams['PATH_TO_FILES'])) {
            throw new SystemException(Loc::getMessage('SPRITE_ERROR_DIR_NOT_AVAILABLE', array('#DIR#' => $this->arParams['PATH_TO_FILES'])));
        }

        if (!file_exists($_SERVER['PATH_TO_FILES_SPRITE'] . $this->arParams['PATH_TO_FILES_SPRITE'])) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . $this->arParams['PATH_TO_FILES_SPRITE'], BX_DIR_PERMISSIONS, true);
        }

        if (!is_readable($_SERVER['DOCUMENT_ROOT'] . $this->arParams['PATH_TO_FILES_SPRITE'])) {
            throw new SystemException(Loc::getMessage('SPRITE_ERROR_DIR_NOT_AVAILABLE', array('#DIR#' => $this->arParams['PATH_TO_FILES_CSS'])));
        } elseif (!is_writable($_SERVER['DOCUMENT_ROOT'] . $this->arParams['PATH_TO_FILES_SPRITE'])) {
            throw new SystemException(Loc::getMessage('SPRITE_ERROR_DIR_NOT_WRITABLE', array('#DIR#' => $this->arParams['PATH_TO_FILES_CSS'])));
        }
    }

    private function getFilesFromPath($strPath, $strSearchFileName) {

        $arGlob = glob($strPath . '*');
        foreach ($arGlob as $strFilePath) {
            $strFileName = preg_replace('/^.*\//', '', $strFilePath);
            if ($strFileName == $strSearchFileName || $strSearchFileName == '*') {
                $this->arSVGFiles[] = $strFilePath;
            } elseif (is_dir($strFilePath)) {
                $this->getFilesFromPath($strFilePath . '/', $strSearchFileName);
            }
        }
    }

    /**
     * Start Component
     */
    public function executeComponent() {

        try {

            $this->checkModules();

            $this->checkDirs();

            $lastModified = time();

            $modified = 0;

            foreach ($this->arParams['FILES'] as $strSVGFile) {
                $this->arSVGFiles[] = $_SERVER['DOCUMENT_ROOT'] . $this->arParams['PATH_TO_FILES'] . $strSVGFile;
            }
            foreach ($this->arParams['FILES_MASK'] as $strSVGFile) {
                $this->getFilesFromPath($_SERVER['DOCUMENT_ROOT'] . $this->arParams['PATH_TO_FILES'], $strSVGFile);
            }

            foreach ($this->arSVGFiles as $file) {

                $file = new SplFileInfo($file);
                /** @var \SplFileInfo $file */
                if ($file->isFile() && $file->isReadable() && $file->getExtension() === 'svg' && ($lastModified = $file->getMTime()) > $modified) {
                    $modified = $lastModified;
                }
            }

            if ($modified)
                $lastModified = $modified;

            $target = $this->arParams['PATH_TO_FILES_SPRITE'] . sprintf($this->arParams['TARGET_FILE_MASK'], $lastModified);

            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $target)) {

                $sprire = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="0" height="0" style="position:absolute">' . "\n\n";

                foreach ($this->arSVGFiles as $file) {
                    $strFileName = preg_replace('/^.*\/(.+).svg$/', '$1', $file);
                    $svg = file_get_contents($file);
                    //получаем viewbox
                    preg_match('/<svg[^>]*(viewBox="[^"]+")[^>]*>/', $svg, $arMathes);
                    $strViewBox = $arMathes[1];

                    $svg = preg_replace('/<svg[^>]*>(.*)<\/svg>/', '$1', $svg);  //удаляем тег svg
                    $svg = preg_replace('/<title[^>]*>[^<]+<\/title>/', '', $svg);  //удаляем тег title
                    //оборачиваем в symbol
                    $svg = '<symbol id="' . $this->arParams['ID_PREFIX'] . $strFileName . '" ' . $strViewBox . '>' . "\n"
                        . $svg . "\n"
                        . '</symbol>' . "\n\n";
                    $sprire .= $svg;
                }

                $sprire .= '</svg>';
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . $target, $sprire);

                if ($this->arParams['REMOVE_OLD_SPRITE_FILES']) {

                    foreach (glob($_SERVER['DOCUMENT_ROOT'] . $this->arParams['PATH_TO_FILES_SPRITE'] . sprintf($this->arParams['TARGET_FILE_MASK'], '*')) as $filename) {
                        if (is_file($filename) && $filename != $_SERVER['DOCUMENT_ROOT'] . $target) {
                            @ unlink($filename);
                        }
                    }
                }
            }

            echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . $target);
        } catch (SystemException $e) {
            if ($this->arParams['SHOW_ERRORS_IN_DISPLAY']) {
                ShowError($e->getMessage());
            } else {
                AddMessage2Log($e->getMessage(), 'longbyte.compiler');
            }
        }
    }

}
