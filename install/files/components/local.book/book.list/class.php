<?php
defined('B_PROLOG_INCLUDED') || die;


use Bitrix\Crm\ContactTable;
use Local\Book\Entity\BookTable;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\UserTable;
use Bitrix\Main\Grid;
use Bitrix\Main\UI\Filter;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Web\Uri;


class CBookStoresListComponent extends CBitrixComponent
{
    const GRID_ID = 'BOOK_LIST';
    const SORTABLE_FIELDS = array(
        'ID',
        'NAME',
        'SHORT_DESCRIPTION',
        'ATTACH_FILES',
        'YEAR_BOOK',
        'PRICE',
        'AUTHOR'
    );
    const FILTERABLE_FIELDS = array(
        'ID',
        'NAME',
        'YEAR_BOOK',
        'PRICE',
        'AUTHOR'
    );
    const SUPPORTED_ACTIONS = array('delete');
    const SUPPORTED_SERVICE_ACTIONS = array('GET_ROW_COUNT');

    private static $headers;
    private static $filterFields;
    private static $filterPresets;

    public function __construct(CBitrixComponent $component = null)
    {
        global $USER;

        parent::__construct($component);

        self::$headers = array(
            array(
                'id' => 'ID',
                'name' => Loc::getMessage('BOOK_HEADER_ID'),
                'sort' => 'ID',
                'first_order' => 'desc',
                'type' => 'int',
            ),
            array(
                'id' => 'NAME',
                'name' => Loc::getMessage('BOOK_HEADER_NAME'),
                'sort' => 'NAME',
                'default' => true,
            ),
            array(
                'id' => 'SHORT_DESCRIPTION',
                'name' => Loc::getMessage('BOOK_SHORT_DESCRIPTION'),
                'sort' => 'SHORT_DESCRIPTION',
                'default' => true,
            ),
            array(
                'id' => 'ATTACH_FILES',
                'name' => Loc::getMessage('BOOK_ATTACH_FILES'),
                'sort' => 'ATTACH_FILES',
                'default' => true,
            ),
            array(
                'id' => 'YEAR_BOOK',
                'name' => Loc::getMessage('BOOK_YEAR_BOOK'),
                'sort' => 'YEAR_BOOK',
                'default' => true,
            ),
            array(
                'id' => 'PRICE',
                'name' => Loc::getMessage('BOOK_PRICE'),
                'sort' => 'PRICE',
                'default' => true,
            ),
            array(
                'id' => 'AUTHOR',
                'name' => Loc::getMessage('BOOK_AUTHOR'),
                'sort' => 'AUTHOR',
                'default' => false,
            ),
            array(
                'id' => 'ENTITY_CONTACT',
                'name' => Loc::getMessage('BOOK_AUTHOR'),
                'default' => true,
                'sort' => 'ENTITY_CONTACT'
            ),
        );

        self::$filterFields = array(
            array(
                'id' => 'ID',
                'name' => Loc::getMessage('BOOK_FILTER_FIELD_ID')
            ),
            array(
                'id' => 'NAME',
                'name' => Loc::getMessage('BOOK_FILTER_FIELD_NAME'),
                'default' => true,
            ),
            array(
                'id' => 'YEAR_BOOK',
                'name' => Loc::getMessage('BOOK_YEAR_BOOK'),
                'default' => true,
            ),
        );

        self::$filterPresets = array();
    }

    public function executeComponent()
    {
        if (!Loader::includeModule('local.book')) {
            ShowError(Loc::getMessage('BOOK_NO_MODULE'));
            return;
        }

        $context = Context::getCurrent();
        $request = $context->getRequest();

        $grid = new Grid\Options(self::GRID_ID);

        //region Sort
        $gridSort = $grid->getSorting();
        $sort = array_filter(
            $gridSort['sort'],
            function ($field) {
                return in_array($field, self::SORTABLE_FIELDS);
            },
            ARRAY_FILTER_USE_KEY
        );
        if (empty($sort)) {
            $sort = array('NAME' => 'asc');
        }
        //endregion

        //region Filter
        $gridFilter = new Filter\Options(self::GRID_ID, self::$filterPresets);
        $gridFilterValues = $gridFilter->getFilter(self::$filterFields);
        $gridFilterValues = array_filter(
            $gridFilterValues,
            function ($fieldName) {
                return in_array($fieldName, self::FILTERABLE_FIELDS);
            },
            ARRAY_FILTER_USE_KEY
        );
        //endregion

        $this->processGridActions($gridFilterValues);
        $this->processServiceActions($gridFilterValues);

        //region Pagination
        $gridNav = $grid->GetNavParams();
        $pager = new PageNavigation('');
        $pager->setPageSize($gridNav['nPageSize']);
        $pager->setRecordCount(BookTable::getCount($gridFilterValues));
        if ($request->offsetExists('page')) {
            $currentPage = $request->get('page');
            $pager->setCurrentPage($currentPage > 0 ? $currentPage : $pager->getPageCount());
        } else {
            $pager->setCurrentPage(1);
        }
        //endregion

        $stores = $this->getStores(array(
            'filter' => $gridFilterValues,
            'limit' => $pager->getLimit(),
            'offset' => $pager->getOffset(),
            'order' => $sort
        ));

        $requestUri = new Uri($request->getRequestedPage());
        $requestUri->addParams(array('sessid' => bitrix_sessid()));

        $this->arResult = array(
            'GRID_ID' => self::GRID_ID,
            'STORES' => $stores,
            'HEADERS' => self::$headers,
            'PAGINATION' => array(
                'PAGE_NUM' => $pager->getCurrentPage(),
                'ENABLE_NEXT_PAGE' => $pager->getCurrentPage() < $pager->getPageCount(),
                'URL' => $request->getRequestedPage(),
            ),
            'SORT' => $sort,
            'FILTER' => self::$filterFields,
            'FILTER_PRESETS' => self::$filterPresets,
            'ENABLE_LIVE_SEARCH' => false,
            'DISABLE_SEARCH' => true,
            'SERVICE_URL' => $requestUri->getUri(),
        );

        $this->includeComponentTemplate();
    }

    private function processGridActions($currentFilter)
    {
        if (!check_bitrix_sessid()) {
            return;
        }

        $context = Context::getCurrent();
        $request = $context->getRequest();

        $action = $request->get('action_button_' . self::GRID_ID);

        if (!in_array($action, self::SUPPORTED_ACTIONS)) {
            return;
        }

        $allRows = $request->get('action_all_rows_' . self::GRID_ID) == 'Y';
        if ($allRows) {
            $dbStores = BookTable::getList(array(
                'filter' => $currentFilter,
                'select' => array('ID'),
            ));
            $storeIds = array();
            foreach ($dbStores as $store) {
                $storeIds[] = $store['ID'];
            }
        } else {
            $storeIds = $request->get('ID');
            if (!is_array($storeIds)) {
                $storeIds = array();
            }
        }

        if (empty($storeIds)) {
            return;
        }

        switch ($action) {
            case 'delete':
                foreach ($storeIds as $storeId) {
                    BookTable::delete($storeId);
                }
                break;

            default:
                break;
        }
    }

    private function processServiceActions($currentFilter)
    {
        global $APPLICATION;

        if (!check_bitrix_sessid()) {
            return;
        }

        $context = Context::getCurrent();
        $request = $context->getRequest();

        $params = $request->get('PARAMS');

        if (empty($params['GRID_ID']) || $params['GRID_ID'] != self::GRID_ID) {
            return;
        }

        $action = $request->get('ACTION');

        if (!in_array($action, self::SUPPORTED_SERVICE_ACTIONS)) {
            return;
        }

        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');

        switch ($action) {
            case 'GET_ROW_COUNT':
                $count = BookTable::getCount($currentFilter);
                echo Json::encode(array(
                    'DATA' => array(
                        'TEXT' => Loc::getMessage('BOOK_GRID_ROW_COUNT', array('#COUNT#' => $count))
                    )
                ));
                break;

            default:
                break;
        }

        die;
    }

    private function getStores($params = array())
    {
        $dbStores = BookTable::getList($params);
        $stores = $dbStores->fetchAll();

        $userIds = array_column($stores, 'AUTHOR');
        $userIds = array_unique($userIds);
        $userIds = array_filter(
            $userIds,
            function ($userId) {
                return intval($userId) > 0;
            }
        );

        $dbUsers = ContactTable::getList(array(
            'filter' => array('=ID' => $userIds)
        ));
        $users = array();
        foreach ($dbUsers as $user) {
            $users[$user['ID']] = $user;
        }

        foreach ($stores as &$store) {
            if ((int)$store['AUTHOR'] > 0) {
                $store['ENTITY_CONTACT'] = trim(sprintf('%s %s',
                    $users[$store['AUTHOR']]['NAME'],
                    $users[$store['AUTHOR']]['LAST_NAME']
                ));
            }
            $store['YEAR_BOOK'] = $store['YEAR_BOOK']->format('d.m.Y');
        }
        return $stores;
    }

}
