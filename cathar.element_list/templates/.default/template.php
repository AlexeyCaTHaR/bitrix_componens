<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if(count($arResult["ITEMS"])>0):?>
<div class="container">
    <?foreach($arResult["ITEMS"] as $arItem):?>
        <div class="row">
            <div class="thumbnail">
                <?if($arItem['PREVIEW_PICTURE']):?>
                    <img src="<?=$arItem['PREVIEW_PICTURE']?>" alt="<?=$arItem['NAME']?>">
                <?endif;?>
                <div class="caption">
                    <h3><?=$arItem['NAME']?></h3>
                    <p><?=$arItem['DATE_ACTIVE_FROM']?></p>
                    <p><?=$arItem['PREVIEW_TEXT']?></p>
                    <p>
                        <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="btn btn-primary" role="button">
                            <?=Loc::getMessage('DETAIL_LINK')?>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    <?endforeach;?>
    <?=$arResult["NAV_STRING"]?>
</div>
<?endif;?>
