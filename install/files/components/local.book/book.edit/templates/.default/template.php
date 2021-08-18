<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/** @var CBitrixComponentTemplate $this */

/** @var ErrorCollection $errors */
$errors = $arResult['ERRORS'];

foreach ($errors as $error) {
    /** @var Error $error */
    ShowError($error->getMessage());
}

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.form',
    'edit',
    array(
        'GRID_ID' => $arResult['GRID_ID'],
        'FORM_ID' => $arResult['FORM_ID'],
        'ENABLE_TACTILE_INTERFACE' => 'Y',
        'SHOW_SETTINGS' => 'Y',
        'TITLE' => $arResult['TITLE'],
        'IS_NEW' => $arResult['IS_NEW'],
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
                        'id' => 'NAME',
                        'name' => Loc::getMessage('BOOK_FIELD_NAME'),
                        'type' => 'text',
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
                        'type' => 'intranet_user_search',
                        'value' => $arResult['STORE']['ASSIGNED_BY_ID'],
                        'componentParams' => array(
                            'NAME' => 'book_edit_responsible',
                            'INPUT_NAME' => 'ASSIGNED_BY_ID',
                            'SEARCH_INPUT_NAME' => 'ASSIGNED_BY_NAME',
                            'NAME_TEMPLATE' => CSite::GetNameFormat()
                        ),
                        'isTactile' => true,
                    )
                )
            ),
        ),
        'BUTTONS' => array(
            'back_url' => $arResult['BACK_URL'],
            'standard_buttons' => true,
        ),
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);
