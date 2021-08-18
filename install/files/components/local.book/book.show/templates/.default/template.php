<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/** @var CBitrixComponentTemplate $this */

if (!Loader::includeModule('crm')) {
    ShowError(Loc::getMessage('BOOK_NO_CRM_MODULE'));
    return;
}

ob_start();
$APPLICATION->IncludeComponent(
    'local.book:xxxxx.bounddeals',
    '',
    array(
        'STORE_ID' => $arResult['STORE']['ID']
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);
$boundDealsHtml = ob_get_clean();

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.form',
    'show',
    array(
        'GRID_ID' => $arResult['GRID_ID'],
        'FORM_ID' => $arResult['FORM_ID'],
        'TACTILE_FORM_ID' => $arResult['TACTILE_FORM_ID'],
        'ENABLE_TACTILE_INTERFACE' => 'Y',
        'SHOW_SETTINGS' => 'Y',
        'DATA' => $arResult['STORE'],
        'TABS' => array(
            array(
                'id' => 'tab_1',
                'name' => Loc::getMessage('BOOK_TAB_STORE_NAME'),
                'title' => Loc::getMessage('BOOK_TAB_STORE_TITLE'),
                'display' => false,
                'fields' => array(
                    array(
                        'id' => 'section_store',
                        'name' => Loc::getMessage('BOOK_FIELD_SECTION_STORE'),
                        'type' => 'section',
                        'isTactile' => true,
                    ),
                    array(
                        'id' => 'ID',
                        'name' => Loc::getMessage('BOOK_FIELD_ID'),
                        'type' => 'label',
                        'value' => $arResult['STORE']['ID'],
                        'isTactile' => true,
                    ),
                    array(
                        'id' => 'NAME',
                        'name' => Loc::getMessage('BOOK_FIELD_NAME'),
                        'type' => 'label',
                        'value' => $arResult['STORE']['NAME'],
                        'isTactile' => true,
                    ),
                    array(
                        'id' => 'SHORT_DESCRIPTION',
                        'name' => Loc::getMessage('BOOK_SHORT_DESCRIPTION'),
                        'type' => 'text',
                        'value' => $arResult['STORE']['SHORT_DESCRIPTION'],
                        'isTactile' => true,
                    ),
                    array(
                        'id' => 'ATTACH_FILES',
                        'name' => Loc::getMessage('BOOK_ATTACH_FILES'),
                        'type' => 'text',
                        'value' => $arResult['STORE']['ATTACH_FILES'],
                        'isTactile' => true,
                    ),
                    array(
                        'id' => 'YEAR_BOOK',
                        'name' => Loc::getMessage('BOOK_YEAR_BOOK'),
                        'type' => 'text',
                        'value' => $arResult['STORE']['YEAR_BOOK'],
                        'isTactile' => true,
                    ),
                    array(
                        'id' => 'PRICE',
                        'name' => Loc::getMessage('BOOK_PRICE'),
                        'type' => 'text',
                        'value' => $arResult['STORE']['PRICE'],
                        'isTactile' => true,
                    ),
                    array(
                        'id' => 'ASSIGNED_BY',
                        'name' => Loc::getMessage('BOOK_FIELD_ASSIGNED_BY'),
                        'type' => 'custom',
                        'value' => CCrmViewHelper::PrepareFormResponsible(
                            $arResult['STORE']['ASSIGNED_BY_ID'],
                            CSite::GetNameFormat(),
                            Option::get('intranet', 'path_user', '', SITE_ID) . '/'
                        ),
                        'isTactile' => true,
                    )
                )
            ),
            array(
                'id' => 'deals',
                'name' => Loc::getMessage('BOOK_TAB_DEALS_NAME'),
                'title' => Loc::getMessage('BOOK_TAB_DEALS_TITLE'),
                'fields' => array(
                    array(
                        'id' => 'DEALS',
                        'colspan' => true,
                        'type' => 'custom',
                        'value' => $boundDealsHtml
                    )
                )
            )
        ),
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);
