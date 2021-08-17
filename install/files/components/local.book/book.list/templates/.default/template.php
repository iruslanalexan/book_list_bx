<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Grid\Panel\Snippet;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Web\Json;

/** @var CBitrixComponentTemplate $this */

if (!Loader::includeModule('crm')) {
    ShowError(Loc::getMessage('BOOK_NO_CRM_MODULE'));
    return;
}

$asset = Asset::getInstance();
$asset->addJs('/bitrix/js/crm/interface_grid.js');

$gridManagerId = $arResult['GRID_ID'] . '_MANAGER';

$rows = array();
foreach ($arResult['STORES'] as $store) {

    $viewUrl = CComponentEngine::makePathFromTemplate(
        $arParams['URL_TEMPLATES']['DETAIL'],
        array('BOOK_ID' => $store['ID'])
    );
    $editUrl = CComponentEngine::makePathFromTemplate(
        $arParams['URL_TEMPLATES']['EDIT'],
        array('BOOK_ID' => $store['ID'])
    );

    $deleteUrlParams = http_build_query(array(
        'action_button_' . $arResult['GRID_ID'] => 'delete',
        'ID' => array($store['ID']),
        'sessid' => bitrix_sessid()
    ));
    $deleteUrl = $arParams['SEF_FOLDER'] . '?' . $deleteUrlParams;

    $rows[] = array(
        'id' => $store['ID'],
        'actions' => array(
            array(
                'TITLE' => Loc::getMessage('BOOK_ACTION_VIEW_TITLE'),
                'TEXT' => Loc::getMessage('BOOK_ACTION_VIEW_TEXT'),
                'ONCLICK' => 'BX.Crm.Page.open(' . Json::encode($viewUrl) . ')',
                'DEFAULT' => true
            ),
            array(
                'TITLE' => Loc::getMessage('BOOK_ACTION_EDIT_TITLE'),
                'TEXT' => Loc::getMessage('BOOK_ACTION_EDIT_TEXT'),
                'ONCLICK' => 'BX.Crm.Page.open(' . Json::encode($editUrl) . ')',
            ),
            array(
                'TITLE' => Loc::getMessage('BOOK_ACTION_DELETE_TITLE'),
                'TEXT' => Loc::getMessage('BOOK_ACTION_DELETE_TEXT'),
                'ONCLICK' => 'BX.CrmUIGridExtension.processMenuCommand(' . Json::encode($gridManagerId) . ', BX.CrmUIGridMenuCommand.remove, { pathToRemove: ' . Json::encode($deleteUrl) . ' })',
            )
        ),
        'data' => $store,
        'columns' => array(
            'ID' => $store['ID'],
            'NAME' => '<a href="' . $viewUrl . '" target="_self">' . $store['NAME'] . '</a>',
            'ASSIGNED_BY' => empty($store['ASSIGNED_BY']) ? '' : CCrmViewHelper::PrepareUserBaloonHtml(
                array(
                    'PREFIX' => "STORE_{$store['ID']}_RESPONSIBLE",
                    'USER_ID' => $store['ASSIGNED_BY_ID'],
                    'USER_NAME' => CUser::FormatName(CSite::GetNameFormat(), $store['ASSIGNED_BY']),
                    'USER_PROFILE_URL' => Option::get('intranet', 'path_user', '', SITE_ID) . '/'
                )
            ),
            'ADDRESS' => $store['ADDRESS'],
        )
    );
}

$snippet = new Snippet();

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.grid',
    'titleflex',
    array(
        'GRID_ID' => $arResult['GRID_ID'],
        'HEADERS' => $arResult['HEADERS'],
        'ROWS' => $rows,
        'PAGINATION' => $arResult['PAGINATION'],
        'SORT' => $arResult['SORT'],
        'FILTER' => $arResult['FILTER'],
        'FILTER_PRESETS' => $arResult['FILTER_PRESETS'],
        'IS_EXTERNAL_FILTER' => false,
        'ENABLE_LIVE_SEARCH' => $arResult['ENABLE_LIVE_SEARCH'],
        'DISABLE_SEARCH' => $arResult['DISABLE_SEARCH'],
        'ENABLE_ROW_COUNT_LOADER' => true,
        'AJAX_ID' => '',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
        'AJAX_LOADER' => null,
        'ACTION_PANEL' => array(
            'GROUPS' => array(
                array(
                    'ITEMS' => array(
                        $snippet->getRemoveButton(),
                        $snippet->getForAllCheckbox(),
                    )
                )
            )
        ),
        'EXTENSION' => array(
            'ID' => $gridManagerId,
            'CONFIG' => array(
                'ownerTypeName' => 'STORE',
                'gridId' => $arResult['GRID_ID'],
                'serviceUrl' => $arResult['SERVICE_URL'],
            ),
            'MESSAGES' => array(
                'deletionDialogTitle' => Loc::getMessage('BOOK_DELETE_DIALOG_TITLE'),
                'deletionDialogMessage' => Loc::getMessage('BOOK_DELETE_DIALOG_MESSAGE'),
                'deletionDialogButtonTitle' => Loc::getMessage('BOOK_DELETE_DIALOG_BUTTON'),
            )
        ),
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y',)
);
