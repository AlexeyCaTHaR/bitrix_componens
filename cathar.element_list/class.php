<?php
/**
 * Created by PhpStorm.
 * User: CaTHaR
 * Date: 06.04.2016
 * Time: 21:13
 */
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

class CTHR_Element_list extends CBitrixComponent
{
    protected $navParams = array();


    /**
     * Load lang files
     */
    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * Prepare component params
     */
    public function onPrepareComponentParams($params)
    {
        $result = array(
            'IBLOCK_TYPE' => trim($params['IBLOCK_TYPE']),
            'IBLOCK_ID' => intval($params['IBLOCK_ID']),

            'NAV_SHOW' => ($params['NAV_SHOW'] == 'Y' ? 'Y' : 'N'),
            'NAV_COUNT' => intval($params['NAV_COUNT']),

            'SORT_BY1' => strlen($params['SORT_BY1']) ? $params['SORT_BY1'] : 'ID',
            'SORT_ORDER1' => $params['SORT_ORDER1'] == 'ASC' ? 'ASC' : 'DESC',
            'SORT_BY2' => strlen($params['SORT_BY2']) ? $params['SORT_BY2'] : 'ID',
            'SORT_ORDER2' => $params['SORT_ORDER2'] == 'ASC' ? 'ASC' : 'DESC',

            'CACHE_TYPE' => trim($params['CACHE_TYPE']),
            'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 36000000
        );
        return $result;
    }

    /**
     * Get element list
     */
    protected function getList()
    {
        $arFilter = array(
            'IBLOCK_TYPE' => $this->arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'ACTIVE' => 'Y'
        );
        $arSort = array(
            $this->arParams['SORT_BY1'] => $this->arParams['SORT_ORDER1'],
            $this->arParams['SORT_BY2'] => $this->arParams['SORT_ORDER2']
        );
        $arSelect = array(
            'ID',
            'NAME',
            'DATE_ACTIVE_FROM',
            'DETAIL_PAGE_URL',
            'PREVIEW_TEXT',
            'PREVIEW_PICTURE'
        );

        $rsElement = CIBlockElement::GetList($arSort, $arFilter, false, $this->navParams, $arSelect);
        while ($arElement = $rsElement->GetNext()) {
            $arTmp = array(
                'ID'                    => $arElement['ID'],
                'NAME'                  => $arElement['NAME'],
                'DATE_ACTIVE_FROM'      => $arElement['DATE_ACTIVE_FROM'],
                'DETAIL_PAGE_URL'       => $arElement['DETAIL_PAGE_URL'],
                'PREVIEW_TEXT'          => $arElement['PREVIEW_TEXT']
            );
            if($arElement['PREVIEW_PICTURE']){
                $arTmp['PREVIEW_PICTURE'] = CFile::GetPath($arElement["PREVIEW_PICTURE"]);
            }

            $this->arResult['ITEMS'][] = $arTmp;
        }
        if ($this->arParams['NAV_SHOW'] == 'Y' && $this->arParams['NAV_COUNT'] > 0) {
            $this->arResult['NAV_STRING'] = $rsElement->GetPageNavString('');
        }
    }

    /**
     * Main func
     */
    public function executeComponent()
    {
        try {
            //If iblock not installed
            if (!Loader::includeModule('iblock'))
                throw new LoaderException(Loc::getMessage('CTHR_LIST_IBLOCK_MODULE_NOT_INSTALLED'));

            //If IBLOCK_ID <= 0
            if ($this->arParams['IBLOCK_ID'] <= 0)
                throw new ArgumentNullException('IBLOCK_ID');

            //set nav params
            if ($this->arParams['NAV_COUNT'] > 0) {
                if ($this->arParams['NAV_SHOW'] == 'Y') {
                    //Not save in session last nav page
                    Option::set('main', 'nav_page_in_session', 'N');

                    $this->navParams = array(
                        'nPageSize' => $this->arParams['NAV_COUNT']
                    );
                } else {
                    $this->navParams = array(
                        'nTopCount' => $this->arParams['NAV_COUNT']
                    );
                }
            }

            //If !CACHE
            if ($this->StartResultCache() || $this->arParams['CACHE_TYPE'] == 'N') {

                // Get elements list
                $this->getList();
                $this->includeComponentTemplate();
            }
        } catch (Exception $e) {
            $this->AbortResultCache();
            ShowError($e->getMessage());
        }

    }
}