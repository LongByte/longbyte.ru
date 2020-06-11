<?php

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\SystemException as SystemException;
use Padaliyajay\PHPAutoprefixer\Autoprefixer;
use Bitrix\Main\IO;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

class LongbyteCSSCompilerComponent extends CBitrixComponent {

    private $arOCssFiles = array();

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
     * @param array $params
     * @return array
     */
    public function onPrepareComponentParams($params) {
        $params['USE_SET_ADDITIONAL_CSS'] = ($params['USE_SETADDITIONALCSS'] == 'Y');

        $params['ADD_CSS_TO_THE_END'] = isset($params['ADD_CSS_TO_THE_END']) && ($params['ADD_CSS_TO_THE_END'] == 'Y');

        $params['REMOVE_OLD_CSS_FILES'] = ($params['REMOVE_OLD_CSS_FILES'] == 'Y');

        $params['FILES'] = is_array($params['FILES']) ? $params['FILES'] : array();

        $params['PATH_TO_FILES'] = isset($params['PATH_TO_FILES']) && strlen(trim($params['PATH_TO_FILES'])) ? preg_replace(array('~^/~', '~/$~'), '/', trim($params['PATH_TO_FILES'])) : null;

        $params['PATH_TO_FILES_CSS'] = isset($params['PATH_CSS']) && strlen(trim($params['PATH_CSS'])) ? preg_replace(array('~^/~', '~/$~'), '/', trim($params['PATH_CSS'])) : SITE_TEMPLATE_PATH . '/';

        switch (strtoupper($params['COMPILER'])) {
            case 'LESS':
                $params['CLASS_COMPILER'] = '\Longbyte\Csscompiler\LessCompiler';
                break;
            case 'SASS':
            case 'SCSS':
            default:
                $params['CLASS_COMPILER'] = '\Longbyte\Csscompiler\SCSSCompiler';
                break;
        }

        $params['TARGET_FILE_MASK'] = trim($params['TARGET_FILE_MASK']);
        if (!strlen($params['TARGET_FILE_MASK'])) {
            $params['TARGET_FILE_MASK'] = 'styles-compiled-%s.css';
        }

        $params['SHOW_ERRORS_IN_DISPLAY'] = ($params['SHOW_ERRORS_IN_DISPLAY'] == 'Y');

        return $params;
    }

    /**
     * @throws SystemException
     */
    protected function checkCompilerClass() {
        if (!class_exists($this->arParams['CLASS_COMPILER'])) {
            throw new SystemException(sprintf('Class "%s" doesn\'t exist.', $this->arParams['CLASS_COMPILER']));
        }
    }

    /**
     * @return \Longbyte\Csscompiler\Compiler
     * @throws SystemException
     */
    protected function getCompiler() {
        if (!class_exists($this->arParams['CLASS_COMPILER'])) {
            throw new SystemException(sprintf('Class "%s" doesn\'t exist.', $this->arParams['CLASS_COMPILER']));
        }

        $compiler = new $this->arParams['CLASS_COMPILER'];

        if (!($compiler instanceof \Longbyte\Csscompiler\Compiler)) {
            throw new SystemException(sprintf('Class "%s" is not a subclass of \Longbyte\Csscompiler\Compiler', $this->arParams['CLASS_COMPILER']));
        }

        return $compiler;
    }

    /**
     * Check the directory needed for component
     * @throws SystemException
     */
    protected function checkDirs() {
        if (!is_readable(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES'])) {
            throw new SystemException(Loc::getMessage('OCSS_ERROR_DIR_NOT_AVAILABLE', array('#DIR#' => $this->arParams['PATH_TO_FILES'])));
        }

        $obTargetDir = new IO\Directory(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_CSS']);
        if (!$obTargetDir->isExists()) {
            $obTargetDir->create();
        }

        if (!is_readable(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_CSS'])) {
            throw new SystemException(Loc::getMessage('OCSS_ERROR_DIR_NOT_AVAILABLE', array('#DIR#' => $this->arParams['PATH_TO_FILES_CSS'])));
        } elseif (!is_writable(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_CSS'])) {
            throw new SystemException(Loc::getMessage('OCSS_ERROR_DIR_NOT_WRITABLE', array('#DIR#' => $this->arParams['PATH_TO_FILES_CSS'])));
        }
    }

    /**
     * Сбор файлов рекурсивно
     * @param string $strPath
     * @param string $strSearchFileName
     */
    private function getFilesFromPath($strPath, $strSearchFileName) {

        $obDir = new IO\Directory($strPath);
        $arFiles = $obDir->getChildren();
        foreach ($arFiles as $obChildren) {
            if ($obChildren->isFile()) {
                $strFileName = $obChildren->getName();
                $bMatch = false;
                if (strpos($strSearchFileName, '*') !== false) {
                    $regex = '/' . str_replace(array('.', '*', '/'), array('\.', '.*', '\/'), $strSearchFileName) . '/i';
                    $bMatch = preg_match($regex, $strFileName);
                } else {
                    $bMatch = strpos($strFileName, $strSearchFileName) !== false;
                }
                if ($bMatch) {
                    $this->arOCssFiles[] = $obChildren->getPath();
                }
            } elseif ($obChildren->isDirectory()) {
                $this->getFilesFromPath($obChildren->getPath() . '/', $strSearchFileName);
            }
        }
    }

    /**
     * Start Component
     * @global \CMain $APPLICATION
     */
    public function executeComponent() {

        global $APPLICATION;

        try {

            $this->checkModules();

            $this->checkCompilerClass();

            $this->checkDirs();

            /** @var \Longbyte\Csscompiler\Compiler $compilerClass */
            $compilerClass = $this->arParams['CLASS_COMPILER'];

            $extCompiler = $compilerClass::getExtension();

            $lastModified = time();

            $modified = 0;

            foreach ($this->arParams['FILES'] as $strOcssFile) {
                $this->arOCssFiles[] = Application::getDocumentRoot() . (strpos($strOcssFile, '/') === 0 ? '' : $this->arParams['PATH_TO_FILES']) . $strOcssFile;
            }
            foreach ($this->arParams['FILES_MASK'] as $strOcssFile) {
                $this->getFilesFromPath(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES'], $strOcssFile);
            }

            $jsonStylesToCompile = $APPLICATION->GetProperty('STYLE_TO_COMPILER', '');
            if ($jsonStylesToCompile == '') {
                $arStylesToCompile = array();
            } else {
                $arStylesToCompile = json_decode($jsonStylesToCompile, true);
            }

            foreach ($arStylesToCompile as $strAdditionalFile) {
                $this->arOCssFiles[] = Application::getDocumentRoot() . $strAdditionalFile;
            }

            foreach ($this->arOCssFiles as $file) {

                $obFile = new IO\File($file);
                if ($obFile->isFile() && $obFile->isReadable() && ($lastModified = $obFile->getModificationTime()) > $modified) {
                    $modified = $lastModified;
                }
            }

            if ($modified)
                $lastModified = $modified;

            $obTargetFile = new IO\File(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_CSS'] . sprintf($this->arParams['TARGET_FILE_MASK'], $lastModified));
            if (!$obTargetFile->isExists() || $obTargetFile->getModificationTime() < $lastModified) {

                /** @var \Longbyte\Csscompiler\Compiler $compiler */
                $compiler = $this->getCompiler();

                $ocss = Loc::getMessage('OCSS_FILE_AUTO_GENERATED', array('#PATH#' => $this->arParams['PATH_TO_FILES']));
                foreach ($this->arOCssFiles as $file) {
                    $obFile = new IO\File($file);
                    $ocss .= $obFile->getContents() . "\n";
                }

                $obTmpFile = new IO\File(Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_CSS'] . sprintf($this->arParams['TMP_FILE_MASK'], $lastModified));
                $obTmpFile->putContents($ocss);
                //компиляция препроцессором
                $css = $compiler->toCss($obTmpFile->getPath());
                $obTmpFile->delete();

                //постпроцессор автопрефиксер
                $obAutoprefixer = new Autoprefixer($css);
                $css = $obAutoprefixer->compile();

                //минимизация
                $css = preg_replace('/(\n|\r\|\s{2,}|\t)/', '', $css);

                if (!empty($css)) {
                    $compiler->saveToFile($obTargetFile->getPath(), $css);
                }

                if ($this->arParams['REMOVE_OLD_CSS_FILES']) {
                    $compiler->removeOldCss(
                        Application::getDocumentRoot() . $this->arParams['PATH_TO_FILES_CSS'] . sprintf($this->arParams['TARGET_FILE_MASK'], '*'), sprintf($this->arParams['TARGET_FILE_MASK'], $lastModified)
                    );
                }

                if (\CHTMLPagesCache::IsCompositeEnabled()) {
                    $compiler->clearAllCHTMLPagesCache();
                }
            }

            $target = str_replace(Application::getDocumentRoot(), '', $obTargetFile->getPath());
            if ($this->arParams['USE_SETADDITIONALCSS']) {
                Main\Page\Asset::getInstance()->addCss($target, $this->arParams['ADD_CSS_TO_THE_END']);
            } else {
                echo sprintf('<link rel="stylesheet" href="%s" type="text/css">', $target);
            }
        } catch (SystemException $e) {
            if ($this->arParams['SHOW_ERRORS_IN_DISPLAY']) {
                ShowError($e->getMessage());
            } else {
                AddMessage2Log($e->getMessage(), 'longbyte.compiler');
            }
        }
    }

}
