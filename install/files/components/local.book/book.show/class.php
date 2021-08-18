<?php
defined('B_PROLOG_INCLUDED') || die;


use Local\Book\Entity\BookTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

class CBookStoresStoreShowComponent extends CBitrixComponent
{
    const FORM_ID = 'BOOK_SHOW';

    public function __construct(CBitrixComponent $component = null)
    {
        parent::__construct($component);

        CBitrixComponent::includeComponentClass('local.book:book.list');
        CBitrixComponent::includeComponentClass('local.book:book.edit');
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $APPLICATION->SetTitle(Loc::getMessage('BOOK_SHOW_TITLE_DEFAULT'));

        if (!Loader::includeModule('local.book')) {
            ShowError(Loc::getMessage('BOOK_NO_MODULE'));
            return;
        }

        $dbStore = BookTable::getById($this->arParams['BOOK_ID']);
        $store = $dbStore->fetch();

        if (empty($store)) {
            ShowError(Loc::getMessage('BOOK_STORE_NOT_FOUND'));
            return;
        }

        $APPLICATION->SetTitle(Loc::getMessage(
            'BOOK_SHOW_TITLE',
            array(
                '#ID#' => $store['ID'],
                '#NAME#' => $store['NAME']
            )
        ));

        $this->arResult = array(
            'FORM_ID' => self::FORM_ID,
            'TACTILE_FORM_ID' => CBookStoresStoreEditComponent::FORM_ID,
            'GRID_ID' => CBookStoresListComponent::GRID_ID,
            'STORE' => $store
        );

        $this->includeComponentTemplate();
    }
}
