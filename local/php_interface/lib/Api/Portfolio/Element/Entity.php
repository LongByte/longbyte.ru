<?php

namespace Api\Portfolio\Element;

/**
 * Class \Api\Portfolio\Element\Entity
 * 
 * @method mixed getId()
 * @method $this setId(mixed $mixedId)
 * @method bool hasId()
 * @method mixed getName()
 * @method $this setName(mixed $mixedName)
 * @method bool hasName()
 * @method mixed getDetailText()
 * @method $this setDetailText(mixed $mixedDetailText)
 * @method bool hasDetailText()
 * @method mixed getPreviewText()
 * @method $this setPreviewText(mixed $mixedPreviewText)
 * @method bool hasPreviewText()
 * @method mixed getPreviewPicture()
 * @method $this setPreviewPicture(mixed $mixedPreviewPicture)
 * @method bool hasPreviewPicture()
 * @method mixed getDetailPicture()
 * @method $this setDetailPicture(mixed $mixedDetailPicture)
 * @method bool hasDetailPicture()
 * @method mixed getTags()
 * @method $this setTags(mixed $mixedTags)
 * @method bool hasTags()
 * @method mixed getUrl()
 * @method $this setUrl(mixed $mixedUrl)
 * @method bool hasUrl()
 * @method mixed getYearStart()
 * @method $this setYearStart(mixed $mixedYearStart)
 * @method bool hasYearStart()
 * @method mixed getYearFinish()
 * @method $this setYearFinish(mixed $mixedYearFinish)
 * @method bool hasYearFinish()
 */
class Entity extends \Api\Core\Iblock\Element\Entity {

    /**
     *
     * @var array
     */
    protected static $arFields = array(
        'ID',
        'NAME',
        'DETAIL_TEXT',
        'PREVIEW_TEXT',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
    );

    /**
     * @var array
     */
    protected static $arProps = array(
        'URL',
        'YEAR_START',
        'YEAR_FINISH',
    );

    /**
     *
     * @var string
     */
    protected $_strPrintYear = '';

    /**
     * 
     * @return string
     */
    public static function getModel(): string {
        return Model::class;
    }

    /**
     * 
     * @return string
     */
    public function getPrintYear(): string {
        return $this->_strPrintYear;
    }

    /**
     * 
     * @param string $strPrintYear
     * @return $this
     */
    public function setPrintYear(string $strPrintYear): self {
        $this->_strPrintYear = $strPrintYear;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function toArray(): array {
        $arData = parent::toArray();

        $arData['preview_src'] = $this->getPreviewPictureFile()->setResize(100, 10000)->convertToWebp()->getSrc();
        $arData['detail_src'] = $this->getDetailPictureFile()->convertToWebp()->getSrc();
        $arData['print_year'] = $this->getPrintYear();
        $arData['url'] = $this->getUrl();
        unset($arData['preview_picture']);
        unset($arData['detail_picture']);

        return $arData;
    }

}
