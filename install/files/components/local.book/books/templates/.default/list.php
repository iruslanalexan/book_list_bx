<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;


/** @var CBitrixComponentTemplate $this */

$APPLICATION->SetTitle(Loc::getMessage('BOOK_LIST_TITLE'));

$APPLICATION->IncludeComponent(
    'bitrix:crm.control_panel',
    '',
    array(
        'ID' => 'STORES',
        'ACTIVE_ITEM_ID' => 'STORES',
    ),
    $component
);

$urlTemplates = array(
    'DETAIL' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['details'],
    'EDIT' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['edit'],
);

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.toolbar',
    'title',
    array(
        'TOOLBAR_ID' => 'BOOK_TOOLBAR',
        'BUTTONS' => array(
            array(
                'TEXT' => Loc::getMessage('BOOK_ADD'),
                'TITLE' => Loc::getMessage('BOOK_ADD'),
                'LINK' => CComponentEngine::makePathFromTemplate($urlTemplates['EDIT'], array('BOOK_ID' => 0)),
                'ICON' => 'btn-add',
            ),
        )
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);

$APPLICATION->IncludeComponent(
    'local.book:book.list',
    '',
    array(
        'URL_TEMPLATES' => $urlTemplates,
        'SEF_FOLDER' => $arResult['SEF_FOLDER'],
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y',)
);
