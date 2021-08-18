<?php
defined('B_PROLOG_INCLUDED') || die;


use Local\Book\Entity\BookTable;
use Bitrix\Main\Context;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;

class CBookStoresStoreEditComponent extends CBitrixComponent
{
    const FORM_ID = 'BOOK_EDIT';

    private $errors;

    public function __construct(CBitrixComponent $component = null)
    {
        parent::__construct($component);

        $this->errors = new ErrorCollection();

        CBitrixComponent::includeComponentClass('local.book:book.list');
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $title = Loc::getMessage('BOOK_SHOW_TITLE_DEFAULT');

        if (!Loader::includeModule('local.book')) {
            ShowError(Loc::getMessage('BOOK_NO_MODULE'));
            return;
        }

        $store = array(
            'NAME' => '',
            'ADDRESS' => '',
            'ASSIGNED_BY_ID' => 0
        );

        if (intval($this->arParams['BOOK_ID']) > 0) {
            $dbStore = BookTable::getById($this->arParams['BOOK_ID']);
            $store = $dbStore->fetch();

            if (empty($store)) {
                ShowError(Loc::getMessage('BOOK_STORE_NOT_FOUND'));
                return;
            }
        }

        if (!empty($store['ID'])) {
            $title = Loc::getMessage(
                'BOOK_SHOW_TITLE',
                array(
                    '#ID#' => $store['ID'],
                    '#NAME#' => $store['NAME']
                )
            );
        }

        $APPLICATION->SetTitle($title);

        if (self::isFormSubmitted()) {
            $savedStoreId = $this->processSave($store);
            if ($savedStoreId > 0) {
                LocalRedirect($this->getRedirectUrl($savedStoreId));
            }

            $submittedStore = $this->getSubmittedStore();
            $store = array_merge($store, $submittedStore);
        }

        $this->arResult = array(
            'FORM_ID' => self::FORM_ID,
            'GRID_ID' => CBookStoresListComponent::GRID_ID,
            'IS_NEW' => empty($store['ID']),
            'TITLE' => $title,
            'STORE' => $store,
            'BACK_URL' => $this->getRedirectUrl(),
            'ERRORS' => $this->errors,
        );

        $this->includeComponentTemplate();
    }

    private function processSave($initialStore)
    {
        $submittedStore = $this->getSubmittedStore();

        $store = array_merge($initialStore, $submittedStore);

        $this->errors = self::validate($store);

        if (!$this->errors->isEmpty()) {
            return false;
        }

        if (!empty($store['ID'])) {
            $result = BookTable::update($store['ID'], $store);
        } else {
            $result = BookTable::add($store);
        }

        if (!$result->isSuccess()) {
            $this->errors->add($result->getErrors());
        }

        return $result->isSuccess() ? $result->getId() : false;
    }

    private function getSubmittedStore()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        $submittedStore = array(
            'NAME' => $request->get('NAME'),
            'ADDRESS' => $request->get('ADDRESS'),
            'ASSIGNED_BY_ID' => $request->get('ASSIGNED_BY_ID'),
        );

        return $submittedStore;
    }

    private static function validate($store)
    {
        $errors = new ErrorCollection();

        if (empty($store['NAME'])) {
            $errors->setError(new Error(Loc::getMessage('BOOK_ERROR_EMPTY_NAME')));
        }

        if (empty($store['ASSIGNED_BY_ID'])) {
            $errors->setError(new Error(Loc::getMessage('BOOK_ERROR_EMPTY_ASSIGNED_BY_ID')));
        } else {
            $dbUser = UserTable::getById($store['ASSIGNED_BY_ID']);
            if ($dbUser->getSelectedRowsCount() <= 0) {
                $errors->setError(new Error(Loc::getMessage('BOOK_ERROR_UNKNOWN_ASSIGNED_BY_ID')));
            }
        }

        return $errors;
    }

    private static function isFormSubmitted()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();
        $saveAndView = $request->get('saveAndView');
        $saveAndAdd = $request->get('saveAndAdd');
        $apply = $request->get('apply');
        return !empty($saveAndView) || !empty($saveAndAdd) || !empty($apply);
    }

    private function getRedirectUrl($savedStoreId = null)
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        if (!empty($savedStoreId) && $request->offsetExists('apply')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                array('BOOK_ID' => $savedStoreId)
            );
        } elseif (!empty($savedStoreId) && $request->offsetExists('saveAndAdd')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                array('BOOK_ID' => 0)
            );
        }

        $backUrl = $request->get('backurl');
        if (!empty($backUrl)) {
            return $backUrl;
        }

        if (!empty($savedStoreId) && $request->offsetExists('saveAndView')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['DETAIL'],
                array('BOOK_ID' => $savedStoreId)
            );
        } else {
            return $this->arParams['SEF_FOLDER'];
        }
    }
}
