<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;

$arComponentParameters = array(
    'PARAMETERS' => array(
        'SEF_MODE' => array(
            'details' => array(/*'NAME' => Loc::getMessage('BOOK_DETAILS_URL_TEMPLATE'),
                'DEFAULT' => '#BOOK_ID#/',
                'VARIABLES' => array('BOOK_ID')*/
            ),
            'edit' => array(/* 'NAME' => Loc::getMessage('BOOK_EDIT_URL_TEMPLATE'),
                'DEFAULT' => '#BOOK_ID#/edit/',
                'VARIABLES' => array('BOOK_ID')*/
            )
        )
    )
);
