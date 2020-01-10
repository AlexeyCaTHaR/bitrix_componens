<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    "ID" => "cathar_list",
    "NAME" => Loc::getMessage('CTHR_LIST_COMPONENT_NAME'),
    "DESCRIPTION" => Loc::getMessage('CTHR_LIST_COMPONENT_DESC'),
    "SORT" => 10,
    "PATH" => [
        "ID" => "cthr",
        "NAME" => Loc::getMessage('CTHR_LIST_SECTION'),
        "SORT" => 1,
    ],
];

?>
