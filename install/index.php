<?php
defined('B_PROLOG_INCLUDED') || die;

use Local\Book\Entity\BookTable;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class local_book extends CModule
{
    const MODULE_ID = 'local.book';
    var $MODULE_ID = self::MODULE_ID;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->MODULE_NAME = Loc::getMessage('LOCAL_BOOK.MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('LOCAL_BOOK.MODULE_DESC');

        $this->PARTNER_NAME = Loc::getMessage('LOCAL_BOOK.PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('LOCAL_BOOK.PARTNER_URI');
    }

    function DoInstall()
    {
        ModuleManager::registerModule(self::MODULE_ID);

        $this->InstallDB();
        $this->InstallFiles();
        $this->InstallEvents();
    }

    function DoUninstall()
    {
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UnInstallDB();

        ModuleManager::unRegisterModule(self::MODULE_ID);
    }

    function InstallDB()
    {
        Loader::includeModule('local.book');

        $db = Application::getConnection();

        $bookEntity = BookTable::getEntity();
        if (!$db->isTableExists($bookEntity->getDBTableName())) {
            $bookEntity->createDbTable();
        }
    }

    function UnInstallDB()
    {

    }

    function InstallEvents()
    {
        $eventManager = EventManager::getInstance();


    }

    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();


    }

    function InstallFiles()
    {
        $documentRoot = Application::getDocumentRoot();

        CopyDirFiles(
            __DIR__ . '/files/components',
            $documentRoot . '/local/components',
            true,
            true
        );

        CopyDirFiles(
            __DIR__ . '/files/pub/crm',
            $documentRoot . '/crm',
            true,
            true
        );

        CUrlRewriter::Add(array(
            'CONDITION' => '#^/crm/book/#',
            'RULE' => '',
            'ID' => 'local.book:books',
            'PATH' => '/crm/book/index.php',
        ));
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx('/crm/book');
        DeleteDirFilesEx('/local/components/local.book');

        CUrlRewriter::Delete(array(
            'ID' => 'local.book:books',
            'PATH' => '/crm/book/index.php',
        ));
    }
}
