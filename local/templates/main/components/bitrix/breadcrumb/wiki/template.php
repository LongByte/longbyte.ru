<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/**
 * @global CMain $APPLICATION
 */
global $APPLICATION;

//delayed function must return a string
if (empty($arResult))
    return "";

$strReturn = '';

$strReturn .= '<div class="bx-breadcrumb" itemprop="http://schema.org/breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';

$itemSize = count($arResult);
for ($index = 0; $index < $itemSize; $index++) {
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    $arrow = ($index > 0 ? '<span class="arrow"></span>' : '');

    if ($arResult[$index]["LINK"] <> "" && $index != $itemSize - 1) {
        $strReturn .= '
			<div class="bx-breadcrumb-item" id="bx_breadcrumb_' . $index . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				' . $arrow . '
				<a href="' . $arResult[$index]["LINK"] . '" title="' . $title . '" itemprop="url" class="link">
					<span itemprop="name">' . $title . '</span>
				</a>
				<meta itemprop="position" content="' . ($index + 1) . '" />
			</div>';
    } else {
        $strReturn .= '
			<div class="bx-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				' . $arrow . '
				<span itemprop="name">' . $title . '</span>
				<meta itemprop="position" content="' . ($index + 1) . '" />
			</div>';
    }
}

$strReturn .= '</div>';

return $strReturn;
