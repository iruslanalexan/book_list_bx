1С-Битрикс - "Каталог книг"

Задание:
Необходимо написать модуль 1С-Битрикс - "Каталог книг" (либо оформить не в виде модуля, а виде сборки под composer):

- Модуль должен без ошибок устанавливаться и удаляться из панели администрирования (не релевантно, если это сборка
  composer).
- Модуль должен содержать две отдельные таблицы: книги (название, краткое описание, приложенные файлы, год книги, цена,
  автор) и авторы (ФИО автора, страна). Работа с таблицами должна происходить путем ORM классов, описывающих таблицы.
- Модуль должен содержать компонент добавления книги.
- Модуль должен содержать компонент для вывода списка книг с фильтром по году и автору. Должна присутствовать пажинация.

--------------------

Обычное задание. Написал шефу, что решение есть

    use Bitrix\CRM\ContactTable;
    use Bitrix\Main\ORM\Fields\IntegerField;
    use Bitrix\Main\ORM\Fields\Relations\Reference;
    use Bitrix\Main\ORM\Fields\StringField;
    use Bitrix\Main\ORM\Fields\DatetimeField;
    use Bitrix\Main\Type\DateTime;
    use \Bitrix\Main\Entity\ReferenceField;
    
    
    
    class StoreTable extends Bitrix\Main\ORM\Data\DataManager
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
    
    
    $cl = new StoreTable();
    
    //  создаем таблицу если ее нет
    try {
    $cl::getEntity()->createDbTable();
    } catch (Exception $e) {
    }
    
    $resdb = $cl::getList(array(
    'select' => array(
    '*'
    )
    ));
    
    if (empty($resdb->fetch())) {
    
    // если нет данных - наполняем. Лучше бы миграцией, но сейчас - что-то, для теста пагинации
    
        $cnt = 20;
        while ($cnt--) {
            $iter = rand(10, 30);
    
    
            $addResult = $cl::add([
                'NAME' => trim(sprintf("%s %s", 'Name', $iter)),
                'SHORT_DESCRIPTION' => trim(sprintf("%s %s", 'first short description', $iter)),
                // 'ATTACH_FILES'=>'',
                'YEAR_BOOK' => \Bitrix\Main\Type\DateTime::createFromUserTime("$iter.06.2018"),
                'PRICE' => 999,
                'AUTHOR' => 1
    
            ]);
            if ($addResult->isSuccess()) {
                $bookid = $addResult->getId();
                print_r('added:' . $bookid . "\n");
            }
        }
    
    }
    
    print_r($resdb->fetchall());

Однако, он предпочел увидеть код PHP

Извольте) Внесу свои коррективы.

- По части "авторы (ФИО автора, страна)" - возьму сущность "Контакты"
- По завершению добавления записи - отправляется месседж в очередь сообщений
- Свойство - не пользовательское, не генерируется, а создается в админке в "Пользовательские свойства". Ну а зачем вам,
  скажите, городить по OnUserTypeBuildList свой развесистый примитивный тип? Хотите улучшений по части пользовательских
  свойств? Это эже другой гит))

  


