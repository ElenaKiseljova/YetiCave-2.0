# Личный проект «YetiCave»

- Студент: [Elena Kiseljova](https://github.com/ElenaKiseljova).
- Наставник: `OnMyOwn`.

---

## Развертывание проекта

1. Добавить файл `db.php` в каталоге `/env` проекта с ассоциативным массивом `$dbParameters` внутри, содержащим данные для подклчения к БД:

```php
$dbParameters = [
  'host' => 'host',
  'user' => 'user',
  'password' => 'password',
  'name' => 'db_name',
];
```

2. Запустить скрипт `init.php` для создания БД (`$dbParameters ['name']`) и таблиц `users`, `categories`, `lots`, `bets`

3. Запустить скрипт `seeds.php` для заполнения таблиц `users`, `categories`, `lots`, `bets` фейковыми данными

---

**Обратите внимание на файл:**

_Не удаляйте и не обращайте внимание на файлы:_<br>
_`.editorconfig`, `.gitattributes`, `.gitignore`._

---
