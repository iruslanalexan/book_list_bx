<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$APPLICATION->IncludeComponent(
    'local.book:books',
    '',
    array(
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/crm/book/',
        'SEF_URL_TEMPLATES' => array(
            'details' => '#BOOK_ID#/',
            'edit' => '#BOOK_ID#/edit/',
        )
    ),
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
