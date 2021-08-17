<?php

namespace Local\Book\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\StringField;
use Bitrix\CRM\ContactTable;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\Type\DateTime;

class BookTable extends DataManager
{
    public static function getTableName()
    {
        return 'abook_store';
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new StringField('NAME'),
            new StringField('SHORT_DESCRIPTION'),
            new IntegerField('ATTACH_FILES'),
            new DatetimeField('YEAR_BOOK', array(
                'default_value' => function () {
                    return new DateTime();
                }
            )),
            new IntegerField('PRICE'),
            new IntegerField('AUTHOR'),

            new ReferenceField(
                'ASSIGNED_BY',
                ContactTable::getEntity(),
                array('=this.AUTHOR' => 'ref.ID')
            )
        );
    }
}
