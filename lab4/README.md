# Лабораторная работа №4 — Разработка плагина для WordPress

**Студент:** Zabudico Alexandr  
**Плагин:** USM Tasks — задачи с приоритетами и дедлайном  
**Репозиторий:** `https://github.com/zabudico/university-labs/tree/lab4`

---

## Содержание

1. [Описание лабораторной работы](#1-описание-лабораторной-работы)
2. [Инструкции по запуску проекта](#2-инструкции-по-запуску-проекта)
3. [Краткая документация к плагину](#3-краткая-документация-к-плагину)
4. [Примеры использования плагина](#4-примеры-использования-плагина)
5. [Ответы на контрольные вопросы](#5-ответы-на-контрольные-вопросы)
6. [Список использованных источников](#6-список-использованных-источников)

---

## 1. Описание лабораторной работы

### Цель работы

Освоить расширяемую модель данных WordPress: создать CPT (Custom Post Type), пользовательскую таксономию, метаданные с метабоксом в административной панели, а также реализовать шорткод для отображения данных на сайте.

### Тема плагина

Вместо стандартной темы «USM Notes» выбрана тема **«USM Tasks»** — менеджер задач с приоритетами и дедлайном. Функциональность полностью соответствует требованиям задания, но адаптирована под управление задачами:

| Элемент задания | Реализация в USM Tasks |
|----------------|----------------------|
| CPT «Заметки» | CPT «Задачи» (`usm_task`) |
| Таксономия «Приоритет» | Таксономия «Приоритет» (High / Medium / Low) |
| Метаполе «Дата напоминания» | Метаполе «Дедлайн» (`_usm_deadline`) |
| Шорткод `[usm_notes]` | Шорткод `[usm_tasks]` |

### Структура плагина

```
usm-tasks/
├── usm-tasks.php   — главный файл плагина (CPT, таксономия, метабокс, шорткод)
├── style.css       — стили для отображения задач на сайте
└── README.md       — документация
```

---

## 2. Инструкции по запуску проекта

### Требования к окружению

| Компонент | Минимальная версия |
|-----------|-------------------|
| PHP | 7.4 |
| WordPress | 5.8 |
| MySQL / MariaDB | 5.7 |
| XAMPP / OpenServer | любая актуальная |

### Шаг 1 — Подготовка среды

Убедитесь, что XAMPP запущен: модули **Apache** и **MySQL** активны (горят зелёным в панели управления).

WordPress доступен по адресу:
```
http://localhost:8080/wp_lab2/wordpress
```

Административная панель:
```
http://localhost:8080/wp_lab2/wordpress/wp-admin
```

### Шаг 2 — Включение режима отладки

Откройте файл `wp-config.php` в корне WordPress и добавьте строки **до** `/* That's all, stop editing! */`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

Логи ошибок будут записываться в `wp-content/debug.log`.

### Шаг 3 — Установка плагина

**Вариант A — через файловую систему:**

1. Скопируйте папку `usm-tasks/` в:
```
C:\xampp\htdocs\wp_lab2\wordpress\wp-content\plugins\usm-tasks\
```

2. В административной панели перейдите в **Плагины → Установленные плагины**

3. Найдите **USM Tasks** и нажмите **«Активировать»**

**Вариант B — через Git:**

```bash
cd C:\xampp\htdocs\wp_lab2\wordpress\wp-content\plugins\
git clone -b lab4 https://github.com/zabudico/university-labs.git temp_lab4
cp -r temp_lab4/lab4/usm-tasks ./usm-tasks
rm -rf temp_lab4
```

### Шаг 4 — Проверка активации

После активации в левом меню административной панели должен появиться раздел:

```
📋 Задачи
   ├── Все задачи
   ├── Добавить задачу
   └── Приоритеты
```

Если появились ошибки — проверьте `wp-content/debug.log`.

### Шаг 5 — Добавление терминов таксономии

Перейдите в **Задачи → Приоритеты** и добавьте три термина:

| Название | Slug | Описание |
|----------|------|----------|
| High | high | Высокий приоритет |
| Medium | medium | Средний приоритет |
| Low | low | Низкий приоритет |

### Шаг 6 — Тестовые данные

Добавьте 5–6 задач через **Задачи → Добавить задачу**. Для каждой задачи:
- Укажите название и описание
- Выберите приоритет в правой панели
- Укажите дату дедлайна в метабоксе «Дедлайн задачи»
- Нажмите **«Опубликовать»**

### Шаг 7 — Создание страницы с шорткодами

Создайте страницу **«All Tasks»** через **Страницы → Добавить новую** и вставьте шорткоды (см. раздел 4).

---

## 3. Краткая документация к плагину

### 3.1 Заголовок плагина

```php
<?php
/**
 * Plugin Name: USM Tasks
 * Plugin URI:  https://github.com/zabudico/university-labs
 * Description: Плагин для управления задачами с приоритетами и дедлайном.
 * Version:     1.0.0
 * Author:      zabudico
 * License:     GPL2
 * Text Domain: usm-tasks
 */
```

Первая строка после открытия файла — проверка прямого доступа:

```php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Запрет прямого доступа к файлу
}
```

---

### 3.2 Custom Post Type — «Задачи»

Регистрируется через хук `init` функцией `register_post_type()`.

**Ключевые параметры CPT:**

| Параметр | Значение | Назначение |
|----------|----------|------------|
| `public` | `true` | Тип виден на фронтенде и в поиске |
| `has_archive` | `true` | Архивная страница `/tasks/` |
| `supports` | `['title','editor','author','thumbnail']` | Поля редактора |
| `menu_icon` | `'dashicons-list-view'` | Иконка в меню админки |
| `show_in_rest` | `true` | Поддержка Gutenberg и REST API |
| `rewrite` | `['slug' => 'tasks']` | Чистый URL `/tasks/` |

**Код регистрации:**

```php
function usm_register_tasks_cpt() {
    $labels = [
        'name'               => 'Задачи',
        'singular_name'      => 'Задача',
        'add_new'            => 'Добавить задачу',
        'add_new_item'       => 'Новая задача',
        'edit_item'          => 'Редактировать задачу',
        'all_items'          => 'Все задачи',
        'not_found'          => 'Задачи не найдены',
        'not_found_in_trash' => 'В корзине задач нет',
    ];

    register_post_type( 'usm_task', [
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => true,
        'supports'     => [ 'title', 'editor', 'author', 'thumbnail' ],
        'menu_icon'    => 'dashicons-list-view',
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'tasks' ],
    ] );
}
add_action( 'init', 'usm_register_tasks_cpt' );
```

---

### 3.3 Пользовательская таксономия — «Приоритет»

Регистрируется через `register_taxonomy()` и привязывается к CPT `usm_task`.

**Ключевые параметры таксономии:**

| Параметр | Значение | Назначение |
|----------|----------|------------|
| `hierarchical` | `true` | Работает как категории (не теги) |
| `public` | `true` | Видна на фронтенде |
| `show_in_rest` | `true` | Поддержка блочного редактора |
| `rewrite` | `['slug' => 'priority']` | URL `/priority/high/` |

**Код регистрации:**

```php
function usm_register_priority_taxonomy() {
    $labels = [
        'name'          => 'Приоритеты',
        'singular_name' => 'Приоритет',
        'all_items'     => 'Все приоритеты',
        'add_new_item'  => 'Добавить приоритет',
        'not_found'     => 'Приоритеты не найдены',
    ];

    register_taxonomy( 'priority', 'usm_task', [
        'labels'       => $labels,
        'hierarchical' => true,
        'public'       => true,
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'priority' ],
    ] );
}
add_action( 'init', 'usm_register_priority_taxonomy' );
```

---

### 3.4 Метабокс — «Дедлайн задачи»

Метабокс добавляется в боковую панель редактора записей типа `usm_task`.

**Регистрация:**

```php
function usm_add_deadline_metabox() {
    add_meta_box(
        'usm_deadline',          // ID
        'Дедлайн задачи',        // Заголовок
        'usm_deadline_callback', // Callback
        'usm_task',              // CPT
        'side',                  // Позиция
        'high'                   // Приоритет
    );
}
add_action( 'add_meta_boxes', 'usm_add_deadline_metabox' );
```

**HTML-форма (с nonce и атрибутом `min` для валидации прошлых дат):**

```php
function usm_deadline_callback( $post ) {
    wp_nonce_field( 'usm_save_deadline', 'usm_deadline_nonce' );
    $value = get_post_meta( $post->ID, '_usm_deadline', true );
    $today = date( 'Y-m-d' );

    echo '<input type="date" name="usm_deadline"
          value="' . esc_attr( $value ) . '"
          min="' . esc_attr( $today ) . '"
          required style="width:100%">';
}
```

**Сохранение с полной цепочкой проверок:**

```php
function usm_save_deadline( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['usm_deadline_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['usm_deadline_nonce'], 'usm_save_deadline' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $date = sanitize_text_field( $_POST['usm_deadline'] ?? '' );

    if ( ! empty( $date ) && $date < date( 'Y-m-d' ) ) {
        set_transient( 'usm_deadline_error_' . $post_id,
            '⛔ Дедлайн не может быть в прошлом!', 45 );
        return;
    }

    $date
        ? update_post_meta( $post_id, '_usm_deadline', $date )
        : delete_post_meta( $post_id, '_usm_deadline' );
}
add_action( 'save_post', 'usm_save_deadline' );
```

**Особенность реализации:** дедлайн отображается в списке задач с цветовой индикацией — зелёный для актуальных, красный + «просрочено» для прошедших.

```php
function usm_show_deadline_column( $column, $post_id ) {
    if ( $column === 'deadline' ) {
        $date  = get_post_meta( $post_id, '_usm_deadline', true );
        $past  = $date && $date < date( 'Y-m-d' );
        $color = $past ? 'color:#e74c3c;' : 'color:#27ae60;';
        echo $date
            ? '<span style="' . $color . 'font-weight:600;">' . esc_html( $date ) . '</span>'
              . ( $past ? ' <span style="color:#e74c3c;">(просрочено)</span>' : '' )
            : '<span style="color:#aaa;">—</span>';
    }
}
```

---

### 3.5 Шорткод `[usm_tasks]`

**Параметры шорткода:**

| Атрибут | Тип | По умолчанию | Описание |
|---------|-----|-------------|----------|
| `priority` | string | `''` (все) | Фильтр по slug приоритета |
| `before_date` | string | `''` (все) | Задачи с дедлайном ≤ указанной даты |

**Логика работы:**
1. Если атрибуты не указаны — выводятся все задачи, отсортированные по дедлайну
2. Если указан `priority` — добавляется `tax_query` по таксономии
3. Если указан `before_date` — добавляется `meta_query` по метаполю `_usm_deadline`
4. Если задач нет — выводится «Нет задач с заданными параметрами»
5. Просроченные задачи автоматически помечаются меткой «просрочено»

---

### 3.6 Файл `style.css` — описание стилей

| Класс | Описание |
|-------|----------|
| `.usm-tasks-list` | Контейнер списка задач |
| `.usm-task` | Карточка задачи с hover-эффектом |
| `.usm-task.usm-overdue` | Просроченная задача (красный фон) |
| `.usm-priority-high/medium/low` | Цвет левой полоски карточки |
| `.usm-badge-high/medium/low` | Цвет бейджа приоритета |
| `.usm-overdue-label` | Красная метка «просрочено» |
| `.usm-empty` | Сообщение при отсутствии задач |

---

## 4. Примеры использования плагина

### 4.1 Все задачи

```
[usm_tasks]
```

Выводит все опубликованные задачи, отсортированные по дедлайну от ближайшего к дальнему.

**Пример вывода:**

```
┌──────────────────────────────────────────────────┐
│ 🔴 Сдать лабораторную работу №4     [High]       │
│    📅 Дедлайн: 2025-03-20                        │
│    Разработать плагин USM Tasks для WordPress... │
├──────────────────────────────────────────────────┤
│ 🔴 Написать отчёт по лаб. №3        [High]       │
│    📅 Дедлайн: 2025-03-25                        │
│    Оформить отчёт в формате Markdown...          │
├──────────────────────────────────────────────────┤
│ 🟡 Изучить Git и GitHub             [Medium]     │
│    📅 Дедлайн: 2025-04-05                        │
│    Освоить основные команды и работу с ветками...│
├──────────────────────────────────────────────────┤
│ 🟡 Прочитать документацию WordPress  [Medium]    │
│    📅 Дедлайн: 2025-04-15                        │
│    Изучить Plugin Handbook и Codex...            │
├──────────────────────────────────────────────────┤
│ 🟢 Обновить портфолио               [Low]        │
│    📅 Дедлайн: 2025-05-01                        │
│    Добавить новые проекты на сайт...             │
├──────────────────────────────────────────────────┤
│ 🟢 Настроить рабочее окружение      [Low]        │
│    📅 Дедлайн: 2025-06-01                        │
│    Установить VS Code, расширения, PHP...        │
└──────────────────────────────────────────────────┘
```

---

### 4.2 Фильтр по приоритету

```
[usm_tasks priority="high"]
```
Показывает только задачи с приоритетом **High**.

```
[usm_tasks priority="medium"]
```
Показывает только задачи с приоритетом **Medium**.

```
[usm_tasks priority="low"]
```
Показывает только задачи с приоритетом **Low**.

> **Важно:** значение `priority` должно совпадать со **slug** термина таксономии (строчные буквы): `high`, `medium`, `low`.

---

### 4.3 Фильтр по дедлайну

```
[usm_tasks before_date="2025-04-30"]
```

Показывает задачи, у которых дедлайн **не позже** 30 апреля 2025 года.

---

### 4.4 Комбинированный фильтр

```
[usm_tasks priority="high" before_date="2025-03-31"]
```

Только срочные задачи с дедлайном до конца марта 2025.

---

### 4.5 Пример страницы «All Tasks»

```
## Все задачи
[usm_tasks]

## Срочные задачи (High Priority)
[usm_tasks priority="high"]

## Задачи до 30 апреля 2025
[usm_tasks before_date="2025-04-30"]
```

---

### 4.6 Поведение при отсутствии задач

Если ни одна задача не удовлетворяет условиям фильтра:

```
Нет задач с заданными параметрами
```

---

## 5. Ответы на контрольные вопросы

### Вопрос 1. Чем пользовательская таксономия принципиально отличается от метаполя?

**Таксономия** — механизм **классификации** записей по общим категориям. Термины таксономии существуют независимо от записей и могут использоваться многократно. WordPress хранит их в отдельных таблицах: `wp_terms`, `wp_term_taxonomy`, `wp_term_relationships`. Таксономии поддерживают архивные страницы, навигацию, фильтрацию через `tax_query` и виджеты.

**Метаполе** — произвольные данные, привязанные к **конкретной записи**. Хранятся в таблице `wp_postmeta` в виде пары ключ-значение. Подходит для уникальных данных каждой записи.

**Сравнительная таблица:**

| Критерий | Таксономия | Метаполе |
|----------|-----------|----------|
| Хранение | `wp_terms` + связующие таблицы | `wp_postmeta` |
| Значения | Общие термины для многих записей | Уникальные данные записи |
| Фильтрация в WP_Query | `tax_query` | `meta_query` |
| Архивные страницы | ✅ Да (`/priority/high/`) | ❌ Нет |
| Переиспользование | ✅ Один термин — много записей | ❌ Значение привязано к записи |
| Виджеты и навигация | ✅ Поддерживаются | ❌ Не поддерживаются |

**Когда выбрать таксономию:**  
Когда значение повторяется у многих записей и нужна фильтрация или архивизация. В данном плагине — **приоритет задачи** (High/Medium/Low): один приоритет могут иметь десятки задач, нужна страница `/priority/high/` и фильтрация через шорткод.

**Когда выбрать метаполе:**  
Когда данные уникальны для каждой записи. В данном плагине — **дедлайн задачи** (`_usm_deadline`): у каждой задачи своя дата, нет смысла делать её термином таксономии. Дата используется только для конкретной записи и фильтрации через `meta_query`.

---

### Вопрос 2. Зачем нужен nonce при сохранении метаполей и что произойдёт, если его не проверять?

**Nonce** (number used once) — одноразовый токен безопасности, генерируемый функцией `wp_nonce_field()`. Он привязан к конкретному: действию, пользователю, сессии и времени (действителен 24 часа).

**Механизм работы в плагине:**

```php
// 1. В форме метабокса — генерируем токен
wp_nonce_field( 'usm_save_deadline', 'usm_deadline_nonce' );

// 2. При сохранении — проверяем токен
if ( ! wp_verify_nonce( $_POST['usm_deadline_nonce'], 'usm_save_deadline' ) ) {
    return; // Прерываем сохранение — запрос нелегитимен
}
```

**Что произойдёт без проверки nonce:**

1. **CSRF-атака:** злоумышленник создаёт стороннюю страницу с формой, отправляющей POST-запрос к сайту. Если администратор открывает эту страницу, браузер автоматически прикрепляет cookies сессии. WordPress принимает запрос как легитимный и выполняет сохранение — злоумышленник удалённо изменил данные без ведома администратора.

2. **Дублирование при автосохранении:** без nonce хук `save_post` срабатывает при каждом автосохранении, что может привести к перезаписи данных в неожиданный момент.

3. **Случайное выполнение:** массовые действия в списке записей также вызывают `save_post` — без nonce это может затронуть поле дедлайна у задач, которые не редактировались.

**Дополнительная защита в плагине** — проверка прав пользователя:

```php
if ( ! current_user_can( 'edit_post', $post_id ) ) return;
```

Это гарантирует, что даже легитимный запрос от авторизованного пользователя будет отклонён, если у него нет прав на редактирование конкретной записи.

---

### Вопрос 3. Какие аргументы register_post_type() и register_taxonomy() важны для фронтенда и UX?

**1. `public => true`** (оба)

Делает тип записи и таксономию видимыми на фронтенде сайта. Без этого параметра CPT существует только в базе данных: записи недоступны по прямым ссылкам, не индексируются поиском, не работают шорткоды для их отображения посетителям. В плагине USM Tasks это критически важно — без `public => true` страница `/tasks/my-task/` вернёт 404.

**2. `has_archive => true`** (для CPT)

Автоматически создаёт архивную страницу по URL `/tasks/`, отображающую все записи данного типа. Важен для UX: пользователи могут просматривать все задачи по единому URL без написания дополнительного PHP-кода. Также WordPress генерирует для этой страницы мета-теги, что улучшает SEO-индексацию.

**3. `show_in_rest => true`** (оба)

Включает поддержку REST API и блочного редактора Gutenberg. Без этого: редактор Gutenberg недоступен для данного CPT (только классический редактор), сторонние приложения не могут обращаться к записям через API, мобильные приложения WordPress не видят этот тип. Важен для современного UX редактирования.

**4. `rewrite => ['slug' => 'tasks']`** (для CPT)

Задаёт человекочитаемый URL-slug. Без него WordPress использует технический идентификатор: `/usm_task/my-task/` вместо `/tasks/my-task/`. Чистые URL: улучшают восприятие пользователем, повышают доверие и напрямую влияют на SEO-позиции.

**5. `labels`** (оба) 

Определяет названия всех элементов интерфейса: пунктов меню, заголовков страниц, кнопок, сообщений поиска. Напрямую влияет на UX администратора — без корректных labels интерфейс отображает технические идентификаторы (`usm_task`, `usm_note`), что затрудняет работу любого человека, кроме разработчика.

---

## 6. Список использованных источников

1. WordPress Developer Documentation — register_post_type():  
   https://developer.wordpress.org/reference/functions/register_post_type/

2. WordPress Developer Documentation — register_taxonomy():  
   https://developer.wordpress.org/reference/functions/register_taxonomy/

3. WordPress Developer Documentation — add_meta_box():  
   https://developer.wordpress.org/reference/functions/add_meta_box/

4. WordPress Developer Documentation — wp_nonce_field() и wp_verify_nonce():  
   https://developer.wordpress.org/reference/functions/wp_nonce_field/

5. WordPress Developer Documentation — WP_Query (tax_query, meta_query):  
   https://developer.wordpress.org/reference/classes/wp_query/

6. WordPress Developer Documentation — add_shortcode():  
   https://developer.wordpress.org/reference/functions/add_shortcode/

7. WordPress Developer Documentation — update_post_meta() / get_post_meta():  
   https://developer.wordpress.org/reference/functions/update_post_meta/

8. WordPress Plugin Handbook — Plugin Security:  
   https://developer.wordpress.org/plugins/security/

9. WordPress Plugin Handbook — Custom Post Types:  
   https://developer.wordpress.org/plugins/post-types/

10. WordPress Plugin Handbook — Taxonomies:  
    https://developer.wordpress.org/plugins/taxonomies/

11. MDN Web Docs — HTML input type="date":  
    https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/date

12. WordPress Codex — Dashicons (иконки для CPT):  
    https://developer.wordpress.org/resource/dashicons/

---

*Все исходные файлы плагина доступны в репозитории:  
`https://github.com/zabudico/university-labs/tree/lab4`*