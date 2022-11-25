<?

namespace LongByte;

class Wakeup
{

    public static function doHits()
    {

        $arVisitedLinks = array();

        $obHttpClient = new \Bitrix\Main\Web\HttpClient();

        $rsSites = \Bitrix\Main\SiteTable::getList(array(
            'filter' => array('ACTIVE' => 'Y'),
            'select' => array('SERVER_NAME'),
        ));
        while ($arSite = $rsSites->fetch()) {

            $serverName = $arSite['SERVER_NAME'];

            $obHttpClient->get('https://' . $serverName);
            $arVisitedLinks[] = 'https://' . $serverName;
            $rawData = $obHttpClient->get('https://' . $serverName . '/sitemap.xml');

            $obListXml = new \SimpleXMLElement($rawData);

            foreach ($obListXml->sitemap as $obListElement) {

                $rawData = $obHttpClient->get($obListElement->loc->__toString());
                $obInnerXml = new \SimpleXMLElement($rawData);

                foreach ($obInnerXml->url as $obElement) {
                    $obHttpClient->get($obElement->loc->__toString());
                    $arVisitedLinks[] = $obElement->loc->__toString();
                }
            }
        }

        return '\LongByte\Wakeup::doHits();';

    }

}
