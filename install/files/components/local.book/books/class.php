<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;


class CBookStoresComponent extends CBitrixComponent
{
    const SEF_DEFAULT_TEMPLATES = array(
        'details' => '#BOOK_ID#/',
        'edit' => '#BOOK_ID#/edit/',
    );

    public function executeComponent()
    {
        // Полноценное управление адресами страниц не существенно для данного урока.

        if (empty($this->arParams['SEF_MODE']) || $this->arParams['SEF_MODE'] != 'Y') {
            ShowError(Loc::getMessage('BOOK_SEF_NOT_ENABLED'));
            return;
        }

        if (empty($this->arParams['SEF_FOLDER'])) {
            ShowError(Loc::getMessage('BOOK_SEF_BASE_EMPTY'));
            return;
        }

        if (!is_array($this->arParams['SEF_URL_TEMPLATES'])) {
            $this->arParams['SEF_URL_TEMPLATES'] = array();
        }

        $sefTemplates = array_merge(self::SEF_DEFAULT_TEMPLATES, $this->arParams['SEF_URL_TEMPLATES']);

        $page = CComponentEngine::parseComponentPath(
            $this->arParams['SEF_FOLDER'],
            $sefTemplates,
            $arVariables
        );

        if (empty($page)) {
            $page = 'list';
        }

        $this->arResult = array(
            'SEF_FOLDER' => $this->arParams['SEF_FOLDER'],
            'SEF_URL_TEMPLATES' => $sefTemplates,
            'VARIABLES' => $arVariables,
        );

        $this->includeComponentTemplate($page);
    }
}
