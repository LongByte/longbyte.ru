<?php

/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage seo
 * @copyright 2001-2013 Bitrix
 */

namespace Bitrix\Longbyte;

use Bitrix\Main\Text\Converter;

/**
 * Base class for sitemapfile
 * Class SitemapFile
 * @package Bitrix\Seo
 */
class SitemapFile extends \Bitrix\Seo\SitemapFile {

    /**
     * Adds new entry to the current sitemap file
     *
     * Entry array keys
     * XML_LOC - loc field value
     * XML_LASTMOD - lastmod field value
     *
     * @param array $entry Entry array.
     *
     * @return void
     */
    public function addEntry($entry) {
        if ($this->isSplitNeeded()) {
            $this->split();
            $this->addEntry($entry);
        } else {
            if (!$this->partChanged) {
                $this->addHeader();
            }

            $strUrlTemplate = self::ENTRY_TPL;
            $arParams = array(
                $entry['XML_LOC'],
                $entry['XML_LASTMOD']
            );

            if (!empty($entry['XML_PRIORITY'])) {
                $strUrlTemplate = str_replace('</url>', '<priority>%s</priority></url>', $strUrlTemplate);
                $arParams[] = $entry['XML_PRIORITY'];
            }
            if (!empty($entry['XML_CHANGEFREQ'])) {
                $strUrlTemplate = str_replace('</url>', '<changefreq>%s</changefreq></url>', $strUrlTemplate);
                $arParams[] = $entry['XML_CHANGEFREQ'];
            }

            $this->putContents(
                sprintf(
                    $strUrlTemplate, //
                    Converter::getXmlConverter()->encode($arParams[0]), //
                    Converter::getXmlConverter()->encode($arParams[1]), //
                    Converter::getXmlConverter()->encode($arParams[2]), //
                    Converter::getXmlConverter()->encode($arParams[3])//
                ), self::APPEND
            );
        }
    }

    /**
     * Adds new IBlock entry to the current sitemap
     *
     * @param string $strUrl IBlock entry URL.
     * @param string $strModifiedDate IBlock entry modify timestamp.
     * @param type $strPriority
     * @param type $strChangeFreq
     *
     * @return void
     */
    public function addIBlockEntry($strUrl, $strModifiedDate, $strPriority = '', $strChangeFreq = '') {
        $this->addEntry(array(
            'XML_LOC' => $this->settings['PROTOCOL'] . '://' . \CBXPunycode::toASCII($this->settings['DOMAIN'], $e = null) . $strUrl,
            'XML_LASTMOD' => date('c', $strModifiedDate - \CTimeZone::getOffset()),
            'XML_PRIORITY' => $strPriority,
            'XML_LASTMOD' => $strChangeFreq,
        ));
    }

}
