<?php
defined('B_PROLOG_INCLUDED') || die;

/**
 * @var string $mid module id from GET
 */

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

global $APPLICATION, $USER;

if (!$USER->IsAdmin()) {
    return;
}

$module_id = 'local.book';
Loader::includeModule($module_id);

$tabs = array(
    array(
        'DIV' => 'general',
        'TAB' => Loc::getMessage('BOOK_TAB_GENERAL_NAME'),
        'TITLE' => Loc::getMessage('BOOK_TAB_GENERAL_TITLE')
    )
);

$options = array(
    'general' => array(
        array('STORE_DETAIL_TEMPLATE', Loc::getMessage('BOOK_OPTION_STORE_DETAIL_TEMPLATE'), '', array('text', '50')),
        array('DEAL_DETAIL_TEMPLATE', Loc::getMessage('BOOK_OPTION_DEAL_DETAIL_TEMPLATE'), '', array('text', '50')),
        array('DEAL_UF_NAME', Loc::getMessage('BOOK_OPTION_DEAL_UF_NAME'), '', array('text', '20')),
    )
);

if (check_bitrix_sessid() && strlen($_POST['save']) > 0) {
    foreach ($options as $option) {
        __AdmSettingsSaveOptions($module_id, $option);
    }
    LocalRedirect($APPLICATION->GetCurPageParam());
}

$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin();
?>
<form method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>">
    <? $tabControl->BeginNextTab(); ?>
    <? __AdmSettingsDrawList($module_id, $options['general']); ?>
    <? $tabControl->Buttons(array('btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false)); ?>
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>
