<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\SystemException as SystemException;
use Bitrix\Main\IO;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

class LongbyteSpriteCompilerComponent extends CBitrixComponent
{

    private $arSVGFiles = array();

    /**
     * Check Required Modules
     * @throws Exception
     */
    protected function checkModules()
    {
        if (!Loader::includeModule('longbyte.compiler')) {
            throw new SystemException(Loc::getMessage('CVP_LONGBYTE_COMPILER_MODULE_NOT_INSTALLED'));
        }
    }

    /**
     * Load language file
     */
    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * Prepare Component Params
     * @param array $params
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $params['FILES'] = is_array($params['FILES']) ? $params['FILES'] : array();

        $params['REMOVE_OLD_SPRITE_FILES'] = ($params['REMOVE_OLD_SPRITE_FILES'] == 'Y');

        $params['PATH_TO_FILES'] = isset($params['PATH_TO_FILES']) && strlen(trim($params['PATH_TO_FILES'])) ? preg_replace(array('~^/~', '~/$~'), '/', trim($params['PATH_TO_FILES'])) : null;

        $params['PATH_TO_FILES_SPRITE'] = isset($params['PATH_TO_FILES_SPRITE']) && strlen(trim($params['PATH_TO_FILES_SPRITE'])) ? preg_replace(array('~^/~', '~/$~'), '/', trim($params['PATH_TO_FILES_SPRITE'])) : SITE_TEMPLATE_PATH . '/';

        $params['TARGET_FILE_MASK'] = trim($params['TARGET_FILE_MASK']);
        if (!strlen($params['TARGET_FILE_MASK'])) {
            $params['TARGET_FILE_MASK'] = 'sprite-%s-compiled.svg';
        }

        $params['SHOW_ERRORS_IN_DISPLAY'] = ($params['SHOW_ERRORS_IN_DISPLAY'] == 'Y');

        return $params;
    }

    /**
     * Check the directory needed for component
     * @throws SystemException
     */
    protected function checkDirs()
    {
        if (!is_readable(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES'])) {
            throw new SystemException(Loc::getMessage('SPRITE_ERROR_DIR_NOT_AVAILABLE', array('#DIR#' => $this->arParams['PATH_TO_FILES'])));
        }

        $obTargetDir = new IO\Directory(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_SPRITE']);
        if (!$obTargetDir->isExists()) {
            $obTargetDir->create();
        }

        if (!is_readable(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_SPRITE'])) {
            throw new SystemException(Loc::getMessage('SPRITE_ERROR_DIR_NOT_AVAILABLE', array('#DIR#' => $this->arParams['PATH_TO_FILES_CSS'])));
        } elseif (!is_writable(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_SPRITE'])) {
            throw new SystemException(Loc::getMessage('SPRITE_ERROR_DIR_NOT_WRITABLE', array('#DIR#' => $this->arParams['PATH_TO_FILES_CSS'])));
        }
    }

    /**
     * Сбор файлов рекурсивно
     * @param string $strPath
     */
    private function getFilesFromPath($strPath)
    {

        $obDir = new IO\Directory($strPath);
        $arFiles = $obDir->getChildren();
        foreach ($arFiles as $obChildren) {
            if ($obChildren->isFile()) {
                if ($obChildren->getExtension() == 'svg') {
                    $this->arSVGFiles[] = $obChildren->getPath();
                }
            } elseif ($obChildren->isDirectory()) {
                $this->getFilesFromPath($obChildren->getPath() . '/');
            }
        }
    }

    /**
     * Start Component
     */
    public function executeComponent()
    {

        try {

            $this->checkModules();

            $this->checkDirs();

            $lastModified = time();

            $modified = 0;

            foreach ($this->arParams['FILES'] as $strSVGFile) {
                $this->arSVGFiles[] = Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES'] . $strSVGFile;
            }
            foreach ($this->arParams['FILES_MASK'] as $strSVGFile) {
                $this->getFilesFromPath(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES']);
            }

            foreach ($this->arSVGFiles as $file) {

                $obFile = new IO\File($file);
                if ($obFile->isFile() && $obFile->isReadable() && $obFile->getExtension() == 'svg' && $obFile->getModificationTime() > $modified) {
                    $modified = $lastModified;
                }
            }

            if ($modified)
                $lastModified = $modified;

            $target = $this->arParams['PATH_TO_FILES_SPRITE'] . sprintf($this->arParams['TARGET_FILE_MASK'], $lastModified);

            $obTargetFile = new IO\File(Application::getDocumentRoot() . $target);
            if (!$obTargetFile->isExists() || $obTargetFile->getModificationTime() < $lastModified) {

                $sprire = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="0" height="0" style="position:absolute">' . "\n\n";

                foreach ($this->arSVGFiles as $file) {
                    $strFileName = preg_replace('/^.*\/(.+).svg$/', '$1', $file);
                    $obSvgFile = new IO\File($file);
                    $svg = $obSvgFile->getContents();
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
                $obTargetFile->putContents($sprire);

                if ($this->arParams['REMOVE_OLD_SPRITE_FILES']) {
                    foreach (glob(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_SPRITE'] . sprintf($this->arParams['TARGET_FILE_MASK'], '*')) as $filename) {
                        $obOldFile = new IO\File($filename);
                        if ($obOldFile->isFile() && $obOldFile->getPath() != $obTargetFile->getPath()) {
                            $obOldFile->delete();
                        }
                    }
                }
            }

            echo $obTargetFile->getContents();
        } catch (SystemException $e) {
            if ($this->arParams['SHOW_ERRORS_IN_DISPLAY']) {
                ShowError($e->getMessage());
            } else {
                AddMessage2Log($e->getMessage(), 'longbyte.compiler');
            }
        }
    }

}
