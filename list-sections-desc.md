# Bitrix24 Lists Sections API - Резюме для создания сервиса

## Общая информация

**Scope:** `lists`  
**Назначение:** Управление разделами (секциями) универсальных списков Bitrix24  
**Документация:** https://apidocs.bitrix24.com/api-reference/lists/sections/index.html

Разделы помогают организовать данные и упрощают навигацию внутри списков, группируя записи по категориям или уровням вложенности.

## Доступные методы API

### 1. lists.section.add - Создание раздела
**URL:** https://apidocs.bitrix24.com/api-reference/lists/sections/lists-section-add.html  
**Права:** пользователь с правом "Редактирование" для требуемого списка

#### Параметры запроса:
```php
[
    'IBLOCK_TYPE_ID' => string, // Required: 'lists', 'bitrix_processes', 'lists_socnet'
    'IBLOCK_ID' => int,         // Required: ID информационного блока 
    'IBLOCK_CODE' => string,    // Required: символьный код блока (альтернатива ID)
    'IBLOCK_SECTION_ID' => int, // Optional: ID родительского раздела (по умолчанию 0)
    'SECTION_CODE' => string,   // Required: символьный код раздела
    'FIELDS' => [               // Required: массив полей
        'NAME' => string,           // Required: название раздела
        'EXTERNAL_ID' => string,    // Optional: внешний идентификатор
        'XML_ID' => string,         // Optional: внешний идентификатор (XML ID)
        'SORT' => int,              // Optional: сортировка
        'ACTIVE' => string,         // Optional: активность 'Y'/'N'
        'PICTURE' => array,         // Deprecated: изображение
        'DESCRIPTION' => string,    // Deprecated: описание
        'DESCRIPTION_TYPE' => string, // Deprecated: тип описания
        'DETAIL_PICTURE' => array,  // Deprecated: детальное изображение
        'SECTION_PROPERTY' => array // Deprecated: пользовательские свойства
    ]
]
```

#### Ответ:
```php
[
    'result' => int,  // ID созданного раздела
    'time' => array   // Информация о времени выполнения
]
```

#### Ошибки:
- `ERROR_REQUIRED_PARAMETERS_MISSING` - отсутствуют обязательные параметры
- `ERROR_ADD_SECTION` - ошибка добавления раздела
- `ACCESS_DENIED` - недостаточно прав

### 2. lists.section.update - Обновление раздела
**URL:** https://apidocs.bitrix24.com/api-reference/lists/sections/lists-section-update.html  
**Права:** пользователь с правом "Редактирование" или "Редактирование с ограничениями" для требуемого списка

#### Параметры запроса:
```php
[
    'IBLOCK_TYPE_ID' => string, // Required: 'lists', 'bitrix_processes', 'lists_socnet'
    'IBLOCK_ID' => int,         // Required: ID информационного блока
    'IBLOCK_CODE' => string,    // Required: символьный код блока (альтернатива ID)
    'SECTION_ID' => int,        // Required: ID раздела
    'SECTION_CODE' => string,   // Required: символьный код раздела (альтернатива ID)
    'FIELDS' => [               // Required: массив полей (аналогично add)
        'NAME' => string,           // Required: название раздела
        'EXTERNAL_ID' => string,    // Optional: внешний идентификатор
        // ... остальные поля как в add
    ]
]
```

#### Ответ:
```php
[
    'result' => bool, // true при успешном обновлении
    'time' => array   // Информация о времени выполнения
]
```

#### Ошибки:
- `ERROR_REQUIRED_PARAMETERS_MISSING` - отсутствуют обязательные параметры
- `ERROR_SECTION_NOT_FOUND` - раздел не найден
- `ERROR_UPDATE_SECTION` - ошибка обновления раздела
- `ACCESS_DENIED` - недостаточно прав

### 3. lists.section.get - Получение раздела/разделов
**URL:** https://apidocs.bitrix24.com/api-reference/lists/sections/lists-section-get.html  
**Права:** пользователь с правом "Чтение" для требуемого списка

#### Параметры запроса:
```php
[
    'IBLOCK_TYPE_ID' => string, // Required: 'lists', 'bitrix_processes', 'lists_socnet'
    'IBLOCK_ID' => int,         // Required: ID информационного блока
    'IBLOCK_CODE' => string,    // Required: символьный код блока (альтернатива ID)
    'FILTER' => [               // Optional: фильтр разделов
        'ID' => int,                    // ID раздела
        'CODE' => string,               // символьный код раздела
        'XML_ID' => string,             // внешний идентификатор (XML ID)
        'EXTERNAL_ID' => string,        // внешний идентификатор раздела
        'NAME' => string,               // название раздела (поддерживает %)
        'ACTIVE' => string,             // статус активности
        'GLOBAL_ACTIVE' => string,      // глобальная активность
        'IBLOCK_ACTIVE' => string,      // статус активности инфоблока
        'IBLOCK_NAME' => string,        // название информационного блока
        'IBLOCK_TYPE' => string,        // идентификатор типа инфоблока
        'IBLOCK_XML_ID' => string,      // внешний ID инфоблока (XML ID)
        'IBLOCK_EXTERNAL_ID' => string, // внешний ID инфоблока
        'DEPTH_LEVEL' => int,           // уровень вложенности
        'LEFT_MARGIN' => int,           // левая граница дерева
        'RIGHT_MARGIN' => int,          // правая граница дерева
        'TIMESTAMP_X' => string,        // время последней модификации
        'DATE_CREATE' => string,        // дата создания раздела
        'CREATED_BY' => int,            // ID создавшего пользователя
        'MODIFIED_BY' => int,           // ID модифицировавшего пользователя
        // Поддерживает префиксы фильтрации: !, >=, <=, >, <, %
        '>=DATE_CREATE' => string,      // от даты
        '<=DATE_CREATE' => string,      // до даты
        '%NAME' => string,              // поиск по названию
    ],
    'SELECT' => [               // Optional: поля для выборки
        'ID',                   // идентификатор раздела
        'CODE',                 // символьный код раздела
        'XML_ID',               // внешний идентификатор (XML ID)
        'EXTERNAL_ID',          // внешний идентификатор раздела
        'IBLOCK_SECTION_ID',    // идентификатор родительского раздела
        'TIMESTAMP_X',          // время последней модификации
        'SORT',                 // сортировка
        'NAME',                 // название раздела
        'ACTIVE',               // активность
        'GLOBAL_ACTIVE',        // глобальная активность
        'PICTURE',              // изображение (deprecated)
        'DESCRIPTION',          // описание (deprecated)
        'DESCRIPTION_TYPE',     // тип описания (deprecated)
        'LEFT_MARGIN',          // левая граница дерева
        'RIGHT_MARGIN',         // правая граница дерева
        'DEPTH_LEVEL',          // уровень вложенности
        'SEARCHABLE_CONTENT',   // индексируемое содержимое
        'SECTION_PAGE_URL',     // URL страницы (deprecated)
        'MODIFIED_BY',          // ID модифицировавшего пользователя
        'DATE_CREATE',          // дата создания раздела
        'CREATED_BY',           // ID создавшего пользователя
        'DETAIL_PICTURE'        // детальное изображение (deprecated)
    ]
]
```

#### Ответ:
```php
[
    'result' => [       // Данные раздела или массив разделов
        [
            'ID' => string,
            'CODE' => string,
            'XML_ID' => string,
            'EXTERNAL_ID' => string,
            'IBLOCK_SECTION_ID' => ?string,
            'TIMESTAMP_X' => string,
            'SORT' => string,
            'NAME' => string,
            'ACTIVE' => string,
            'GLOBAL_ACTIVE' => string,
            'LEFT_MARGIN' => string,
            'RIGHT_MARGIN' => string,
            'DEPTH_LEVEL' => string,
            'SEARCHABLE_CONTENT' => string,
            'MODIFIED_BY' => string,
            'DATE_CREATE' => string,
            'CREATED_BY' => string
        ]
    ],
    'total' => int,     // Общее количество
    'time' => array     // Информация о времени выполнения
]
```

#### Ошибки:
- `ERROR_REQUIRED_PARAMETERS_MISSING` - отсутствуют обязательные параметры
- `ACCESS_DENIED` - недостаточно прав для чтения раздела

### 4. lists.section.delete - Удаление раздела
**URL:** https://apidocs.bitrix24.com/api-reference/lists/sections/lists-section-delete.html  
**Права:** пользователь с правом "Редактирование" для требуемого списка

#### Параметры запроса:
```php
[
    'IBLOCK_TYPE_ID' => string, // Required: 'lists', 'bitrix_processes', 'lists_socnet'
    'IBLOCK_ID' => int,         // Required: ID информационного блока
    'IBLOCK_CODE' => string,    // Required: символьный код блока (альтернатива ID)
    'SECTION_ID' => int,        // Required: ID раздела
    'SECTION_CODE' => string    // Required: символьный код раздела (альтернатива ID)
]
```

#### Ответ:
```php
[
    'result' => bool, // true при успешном удалении
    'time' => array   // Информация о времени выполнения
]
```

#### Ошибки:
- `ERROR_REQUIRED_PARAMETERS_MISSING` - отсутствуют обязательные параметры
- `ERROR_SECTION_NOT_FOUND` - раздел не найден
- `ERROR_DELETE_SECTION` - ошибка удаления раздела
- `ACCESS_DENIED` - недостаточно прав

## Структура для реализации

### Классы сервисов:
1. **Section\Service\Section** - основной сервис для работы с разделами
2. **Section\Service\Batch** - сервис для массовых операций
3. **Section\Result\SectionResult** - результат одного раздела
4. **Section\Result\SectionsResult** - результат списка разделов  
5. **Section\Result\SectionItemResult** - DTO для отдельного раздела

### Методы основного сервиса:
- `add(string $iblockTypeId, int|string $iblockId, string $sectionCode, array $fields, ?int $parentSectionId = null): SectionResult`
- `update(string $iblockTypeId, int|string $iblockId, int|string $sectionId, array $fields): UpdatedItemResult`
- `get(string $iblockTypeId, int|string $iblockId, array $filter = [], array $select = []): SectionsResult`
- `delete(string $iblockTypeId, int|string $iblockId, int|string $sectionId): DeletedItemResult`

### Методы batch-сервиса:
- `list(string $iblockTypeId, int|string $iblockId, array $filter = [], array $select = []): Generator`

### DTO Properties (SectionItemResult):
```php
/**
 * @property-read int $ID
 * @property-read string $CODE
 * @property-read string $XML_ID
 * @property-read string $EXTERNAL_ID
 * @property-read ?int $IBLOCK_SECTION_ID
 * @property-read CarbonImmutable $TIMESTAMP_X
 * @property-read int $SORT
 * @property-read string $NAME
 * @property-read bool $ACTIVE               // Y/N -> bool
 * @property-read bool $GLOBAL_ACTIVE        // Y/N -> bool
 * @property-read int $LEFT_MARGIN
 * @property-read int $RIGHT_MARGIN
 * @property-read int $DEPTH_LEVEL
 * @property-read string $SEARCHABLE_CONTENT
 * @property-read int $MODIFIED_BY
 * @property-read CarbonImmutable $DATE_CREATE
 * @property-read int $CREATED_BY
 */
```

## Особенности реализации

1. **Альтернативные идентификаторы**: методы поддерживают как ID, так и CODE для указания списка и раздела
2. **Deprecated поля**: поля PICTURE, DESCRIPTION, DETAIL_PICTURE, SECTION_PROPERTY помечены как устаревшие
3. **Hierarchical structure**: разделы поддерживают иерархическую структуру с родительскими разделами
4. **Tree margins**: используется алгоритм Nested Sets (LEFT_MARGIN, RIGHT_MARGIN) для работы с деревом
5. **Filtering**: метод get поддерживает развитую систему фильтрации с префиксами
6. **Batch operations**: только для метода list, остальные операции не поддерживают batch

## Интеграционные тесты

### Тест-кейсы:
1. **CRUD операции**: создание, чтение, обновление, удаление разделов
2. **Иерархия**: создание и работа с родительскими/дочерними разделами
3. **Фильтрация**: тестирование различных фильтров и их комбинаций
4. **Альтернативные идентификаторы**: работа с CODE вместо ID
5. **Ошибки и права доступа**: проверка обработки ошибок и прав

### Необходимые списки для тестов:
- Список с разделами для полного тестирования CRUD операций
- Тестовые разделы разных уровней для проверки иерархии
- Разделы с различными состояниями (активные/неактивные) для фильтрации