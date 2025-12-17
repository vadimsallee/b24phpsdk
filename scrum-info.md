# Анализ SCRUM API методов Bitrix24 - План разработки

## Обзор архитектуры SCRUM

SCRUM в Bitrix24 технически является группой (sonet group). Группа считается SCRUM-группой, если поле `SCRUM_MASTER_ID` заполнено. Все методы SCRUM работают в рамках скоупа `task`.

## Компоненты системы SCRUM

### 1. Основные сущности

1. **Backlog** - список всех задач команды
2. **Epic** - тема, контекст или крупная цель для задач  
3. **Sprint** - короткий итеративный цикл работы
4. **Kanban** - визуальное представление работы с задачами
5. **Scrum Task** - обычные задачи Bitrix24 с расширенными возможностями

### 2. Взаимосвязи компонентов

```
Group (Scrum) ──┬── Backlog (auto-created)
                ├── Epics (multiple)
                ├── Sprints (multiple)
                │   └── Kanban Stages
                └── Tasks (связаны с Epic, Backlog или Sprint)
```

### 3. Детальный анализ каждого компонента

#### 3.1 Backlog
**Назначение:** Централизованный список всех задач команды

**Особенности:**
- В каждом Scrum может быть только один backlog
- Bitrix24 создает его автоматически при создании Scrum
- Метод `add` нужен только при импорте данных из других систем
- При удалении backlog автоматически пересоздается при открытии страницы планирования

**Поля:**
```php
'groupId' => 'integer',      // ID группы (Scrum)
'createdBy' => 'integer',    // ID создателя
'modifiedBy' => 'integer'    // ID того, кто изменил
```

**Методы:**
- `tasks.api.scrum.backlog.add` - добавление backlog
- `tasks.api.scrum.backlog.update` - обновление backlog
- `tasks.api.scrum.backlog.get` - получение полей backlog по ID Scrum
- `tasks.api.scrum.backlog.delete` - удаление backlog
- `tasks.api.scrum.backlog.getFields` - получение доступных полей

#### 3.2 Epic
**Назначение:** Тематическая группировка задач для структурирования backlog

**Особенности:**
- Множественные epic'и для каждого Scrum
- Могут содержать прикрепленные файлы с Диска
- Связываются с задачами по `epicId`

**Поля:**
```php
'name' => 'string',          // Название epic
'description' => 'string',   // Описание epic
'groupId' => 'integer',      // ID группы (Scrum)
'color' => 'string',         // Цвет epic
'files' => 'array',          // Массив прикрепленных файлов
'createdBy' => 'integer',    // ID создателя
'modifiedBy' => 'integer'    // ID того, кто изменил
```

**Методы:**
- `tasks.api.scrum.epic.add` - добавление epic
- `tasks.api.scrum.epic.update` - обновление epic
- `tasks.api.scrum.epic.get` - получение полей epic по ID
- `tasks.api.scrum.epic.list` - получение списка epic'ов
- `tasks.api.scrum.epic.delete` - удаление epic
- `tasks.api.scrum.epic.getFields` - получение доступных полей

#### 3.3 Sprint
**Назначение:** Короткие итеративные циклы работы команды

**Особенности:**
- Статусы: `planned` → `active` → `completed`
- Может быть запущен только sprint со статусом `planned`
- В один момент времени может быть активен только один sprint
- Завершение происходит по `groupId`, а не по `sprintId`

**Методы:**
- `tasks.api.scrum.sprint.add` - добавление sprint
- `tasks.api.scrum.sprint.update` - обновление sprint
- `tasks.api.scrum.sprint.start` - запуск sprint (по sprintId)
- `tasks.api.scrum.sprint.complete` - завершение активного sprint (по groupId)
- `tasks.api.scrum.sprint.get` - получение полей sprint по ID
- `tasks.api.scrum.sprint.list` - получение списка sprint'ов
- `tasks.api.scrum.sprint.delete` - удаление sprint
- `tasks.api.scrum.sprint.getFields` - получение доступных полей

#### 3.4 Kanban
**Назначение:** Визуальное управление задачами в рамках sprint'а

**Особенности:**
- Связывается со sprint по `sprintId`
- Обязательно должен содержать стадии типа `NEW` и `FINISH`
- Стадии создаются только для активных sprint'ов (`status: "active"`)
- Задачи перемещаются между стадиями

**Методы:**
- `tasks.api.scrum.kanban.addStage` - создание стадии Kanban
- `tasks.api.scrum.kanban.updateStage` - обновление стадии Kanban
- `tasks.api.scrum.kanban.getStages` - получение стадий по ID sprint'а
- `tasks.api.scrum.kanban.deleteStage` - удаление стадии
- `tasks.api.scrum.kanban.addTask` - добавление задачи в Kanban
- `tasks.api.scrum.kanban.deleteTask` - удаление задачи из Kanban
- `tasks.api.scrum.kanban.getFields` - получение доступных полей стадии

#### 3.5 Scrum Task
**Назначение:** Расширенные возможности для обычных задач Bitrix24 в контексте Scrum

**Особенности:**
- Обычные задачи Bitrix24 с дополнительными полями для Scrum
- Создаются через `tasks.task.add` с указанием `GROUP_ID`
- Связываются с Scrum через `entityId` (backlog или sprint ID)
- Могут иметь story points и привязку к epic

**Методы:**
- `tasks.api.scrum.task.update` - создание или обновление Scrum задачи
- `tasks.api.scrum.task.get` - получение полей Scrum задачи по ID
- `tasks.api.scrum.task.getFields` - получение доступных полей Scrum задачи

## Рекомендуемая последовательность разработки

### Этап 1: Базовые независимые сущности
**Приоритет: ВЫСОКИЙ**

1. **Epic Service** - самая независимая сущность
   - Не требует других Scrum компонентов для работы
   - Простая CRUD логика
   - Может быть полностью протестирована независимо

2. **Backlog Service** - базовый компонент
   - Минимальная функциональность (автоматическое создание)
   - Требуется для связи задач с проектом
   - Простая структура данных

### Этап 2: Управление жизненным циклом
**Приоритет: ВЫСОКИЙ**

3. **Sprint Service** - центральный компонент
   - Зависит от Backlog (задачи перемещаются из backlog в sprint)
   - Сложная логика состояний (planned → active → completed)
   - Специальные методы start/complete
   - Необходим для Kanban

### Этап 3: Визуализация и управление задачами
**Приоритет: СРЕДНИЙ**

4. **Kanban Service** - зависит от Sprint
   - Требует активного sprint'а для создания стадий
   - Сложная логика управления стадиями
   - Интеграция с задачами

5. **Scrum Task Service** - интеграционный компонент
   - Зависит от Epic, Sprint, Backlog
   - Расширяет существующий Task API
   - Сложная логика связей

## Детальный план разработки по этапам

### Этап 1.1: Epic Service (Неделя 1)

**Структура:**
```
src/Services/Task/Scrum/Epic/
├── Result/
│   ├── EpicResult.php           # Одиночный epic
│   ├── EpicsResult.php          # Список epic'ов  
│   └── EpicItemResult.php       # DTO для epic
└── Service/
    ├── Epic.php                 # Основной сервис
    └── Batch.php                # Batch операции
```

**Методы для реализации:**
- `add(int $groupId, array $fields): EpicResult`
- `get(int $epicId): EpicResult` 
- `list(array $filter, array $order, array $select): EpicsResult`
- `update(int $epicId, array $fields): EpicResult`
- `delete(int $epicId): DeletedItemResult`
- `fields(): FieldsResult`

### Этап 1.2: Backlog Service (Неделя 1)

**Структура:**
```
src/Services/Task/Scrum/Backlog/
├── Result/
│   └── BacklogResult.php        # Единственный backlog
└── Service/
    └── Backlog.php              # Основной сервис (без batch - только один backlog)
```

**Методы для реализации:**
- `add(int $groupId): BacklogResult` (редко используется)
- `get(int $groupId): BacklogResult`
- `update(int $groupId, array $fields): BacklogResult` 
- `delete(int $groupId): DeletedItemResult` (редко используется)
- `fields(): FieldsResult`

### Этап 2: Sprint Service (Неделя 2)

**Структура:**
```
src/Services/Task/Scrum/Sprint/
├── Result/
│   ├── SprintResult.php         # Одиночный sprint
│   ├── SprintsResult.php        # Список sprint'ов
│   └── SprintItemResult.php     # DTO для sprint
└── Service/
    ├── Sprint.php               # Основной сервис
    └── Batch.php                # Batch операции
```

**Методы для реализации:**
- `add(array $fields): SprintResult`
- `get(int $sprintId): SprintResult`
- `list(array $filter, array $order, array $select): SprintsResult`
- `update(int $sprintId, array $fields): SprintResult`
- `start(int $sprintId): SprintResult` **[СПЕЦИАЛЬНЫЙ]**
- `complete(int $groupId): SprintResult` **[СПЕЦИАЛЬНЫЙ]**
- `delete(int $sprintId): DeletedItemResult`
- `fields(): FieldsResult`

### Этап 3.1: Kanban Service (Неделя 3)

**Структура:**
```
src/Services/Task/Scrum/Kanban/
├── Result/
│   ├── StageResult.php          # Одиночная стадия
│   ├── StagesResult.php         # Список стадий
│   └── StageItemResult.php      # DTO для стадии
└── Service/
    ├── Kanban.php               # Основной сервис
    └── Batch.php                # Batch операции
```

**Методы для реализации:**
- `addStage(array $fields): StageResult`
- `updateStage(int $stageId, array $fields): StageResult`
- `getStages(int $sprintId): StagesResult`
- `deleteStage(int $stageId): DeletedItemResult`
- `addTask(int $sprintId, int $taskId, int $stageId): UpdatedItemResult`
- `deleteTask(int $sprintId, int $taskId): DeletedItemResult`
- `fields(): FieldsResult`

### Этап 3.2: Scrum Task Service (Неделя 4)

**Структура:**
```
src/Services/Task/Scrum/ScrumTask/
├── Result/
│   └── ScrumTaskResult.php      # Scrum задача
└── Service/
    └── ScrumTask.php            # Основной сервис
```

**Методы для реализации:**
- `update(array $fields): ScrumTaskResult` (создание или обновление)
- `get(int $taskId): ScrumTaskResult`
- `fields(): FieldsResult`

### Этап 4: Интеграция и Service Builder (Неделя 4)

**Структура:**
```
src/Services/Task/Scrum/
├── ScrumServiceBuilder.php      # Основной builder для Scrum
└── Service/
    └── Scrum.php                # Корневой сервис (опционально)
```

**Интеграция в Task Service Builder:**
```php
// В TaskServiceBuilder.php
public function scrum(): Scrum\ScrumServiceBuilder
{
    if (!isset($this->serviceCache[__METHOD__])) {
        $this->serviceCache[__METHOD__] = new Scrum\ScrumServiceBuilder(
            $this->core,
            $this->batch,
            $this->bulkItemsReader,
            $this->log
        );
    }
    return $this->serviceCache[__METHOD__];
}
```

## Технические рекомендации

### 1. Управление зависимостями
- Epic и Backlog - полностью независимые
- Sprint зависит от Backlog концептуально
- Kanban строго зависит от Sprint (проверять статус sprint'а)
- ScrumTask может использовать все предыдущие компоненты

### 2. Обработка особых случаев
- **Backlog**: автоматическое создание, обработка попыток создания дублей
- **Sprint**: валидация переходов состояний, проверка единственности активного sprint'а
- **Kanban**: валидация обязательных стадий NEW/FINISH, проверка статуса sprint'а

### 3. Тестирование
- Каждый этап должен включать полное покрытие unit и integration тестов
- Особое внимание к тестированию переходов состояний Sprint
- Тестирование граничных случаев (единственность backlog, активность sprint)

### 4. Результирующие классы
- Использовать lazy loading для сложных свойств
- Типизированные поля с правильными PHPDoc аннотациями
- Обработка дат через CarbonImmutable
- Enum'ы для статусов и типов

### 5. Batch операции
- Epic и Sprint поддерживают batch операции (list методы)
- Backlog не нуждается в batch (один объект на группу)
- Kanban частично поддерживает batch (getStages)

## Заключение

Разработка должна следовать принципу "снизу вверх" - от независимых компонентов к зависимым. Это обеспечит:

1. **Поэтапное тестирование** каждого компонента
2. **Раннее выявление проблем** в простых компонентах
3. **Возможность параллельной разработки** Epic и Backlog
4. **Стабильную основу** для сложных компонентов Sprint и Kanban

Общая оценка времени: **4 недели** для полной реализации всех SCRUM сервисов с тестами и документацией.