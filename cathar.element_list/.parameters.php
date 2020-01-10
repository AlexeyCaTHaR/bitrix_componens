<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\TypeTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\IblockSiteTable;


Loc::loadMessages(__FILE__);

if (!Loader::includeModule('iblock')) {
    return;
}

//load ib types
$arTypesEx = ['-' => ' '];
$types = TypeTable::getList(
    [
        'select' => ['*', 'NAME' => 'LANG_MESSAGE.NAME'],
        'filter' => ['=LANG_MESSAGE.LANGUAGE_ID' => 'ru']
    ]
)->FetchAll();

foreach ($types as $type) {
    $arTypesEx[$type["ID"]] = '[' . $type["ID"] . '] ' . $type["NAME"];
}

//load IB id's for this site_id
$site_ibs = [];
$site_ibs_res = IblockSiteTable::getList([
    'select' => ['*'],
    'filter' => ['SITE_ID' => 's1']
])->fetchAll();
foreach ($site_ibs_res as $ib) {
    $site_ibs[] = intval($ib['IBLOCK_ID']);
}

//load IB's
$arIBlocks = [];
$db_iblock = IblockTable::getList(
    [
        'select' => ['ID', 'NAME'],
        'filter' => [
            'ID' => $site_ibs,
            'IBLOCK_TYPE_ID' => (isset($arCurrentValues["IBLOCK_TYPE"]) ? $arCurrentValues["IBLOCK_TYPE"] : "")
        ]
    ]
);

while ($arRes = $db_iblock->Fetch()) {
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

//load property list
$arProperty_LNS = [];
$arProps = PropertyTable::getList(
    array(
        'select' => ['*'],
        'filter' => ['=IBLOCK_ID' => (isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"])]
    )
)->FetchAll();

foreach ($arProps as $arr) {
    $arProperty[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
    if (in_array($arr["PROPERTY_TYPE"], ["L", "N", "S"])) {
        $arProperty_LNS[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
    }
}

$arSorts = [
    "ASC" => Loc::getMessage("CTHR_LIST_SORT_ASC"),
    "DESC" => Loc::getMessage("CTHR_LIST_SORT_DESC")
];
$arSortFields = [
    "ID" => Loc::getMessage("CTHR_LIST_SORT_ID"),
    "NAME" => Loc::getMessage("CTHR_LIST_SORT_NAME"),
    "ACTIVE_FROM" => Loc::getMessage("CTHR_LIST_SORT_ACT"),
    "SORT" => Loc::getMessage("CTHR_LIST_SORT_SORT")
];

$arComponentParameters = [
    "GROUPS" => [],
    "PARAMETERS" => [
        "IBLOCK_TYPE" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage('CTHR_IBLOCK_TYPE'),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "DEFAULT" => "news",
            "REFRESH" => "Y",
        ],
        "IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage('CTHR_IBLOCK_ID'),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "DEFAULT" => '={$_REQUEST["ID"]}',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ],
        "NAV_SHOW" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage('CTHR_LIST_NAV_SHOW'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        "NAV_COUNT" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage('CTHR_LIST_NAV_COUNT'),
            "TYPE" => "STRING",
            "DEFAULT" => "20",
        ],
        "SORT_BY1" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::getMessage("CTHR_LIST_SORT_BY1"),
            "TYPE" => "LIST",
            "DEFAULT" => "ACTIVE_FROM",
            "VALUES" => $arSortFields
        ],
        "SORT_ORDER1" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::getMessage("CTHR_LIST_SORT_ORDER1"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts
        ],
        "SORT_BY2" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::getMessage("CTHR_LIST_SORT_BY2"),
            "TYPE" => "LIST",
            "DEFAULT" => "SORT",
            "VALUES" => $arSortFields
        ],
        "SORT_ORDER2" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => Loc::getMessage("CTHR_LIST_SORT_ORDER2"),
            "TYPE" => "LIST",
            "DEFAULT" => "ASC",
            "VALUES" => $arSorts
        ],
        "CACHE_TIME" => ["DEFAULT" => 36000000],
    ],
];
