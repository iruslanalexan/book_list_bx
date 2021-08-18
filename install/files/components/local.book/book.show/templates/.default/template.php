<?php
defined('B_PROLOG_INCLUDED') || die;


use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Crm\ContactTable;

/** @var CBitrixComponentTemplate $this */

if (!Loader::includeModule('crm')) {
    ShowError(Loc::getMessage('BOOK_NO_CRM_MODULE'));
    return;
}

ob_start();
$dbUsersContact = ContactTable::getList(array(
    'select' => array('ID', 'NAME', 'LAST_NAME')
));
$contacts = array();

?>
    <select
        name="setContact">
        <option value="0"><?= Loc::getMessage('BOOK_NOTCHOISE'); ?></option><?
        foreach ($dbUsersContact as $valueID => $value) {
            // print_r($value);
            ?>
            <option
            value="<? echo $value['ID']; ?>"><? echo trim(sprintf('%s %s',
                $value['NAME'],
                $value['LAST_NAME']
            )) ?></option><?
        }
        ?></select>
<?php

$boundContact = ob_get_clean();

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
                        'type' => 'date',
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
                        'value' => $boundContact,
                        'isTactile' => true,
                    )
                )
            ),

        ),
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);

