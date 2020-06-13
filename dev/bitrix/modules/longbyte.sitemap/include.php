<?php

\Bitrix\Main\Loader::registerAutoLoadClasses(
    'longbyte.sitemap', array(
    '\Bitrix\Longbyte\SitemapFile' => 'lib/sitemapfile.php',
    '\Bitrix\Longbyte\SitemapRuntime' => 'lib/sitemapruntime.php',
    )
);
?>