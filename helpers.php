<?php

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
  $format_to_check = 'Y-m-d';
  $dateTimeObj = date_create_from_format($format_to_check, $date);

  return $dateTimeObj !== false && ($errs = date_get_last_errors()) ? array_sum($errs) === 0 : true;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
  $stmt = mysqli_prepare($link, $sql);

  if ($stmt === false) {
    $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
    die($errorMsg);
  }

  if ($data && is_array($data)) {
    $types = '';
    $stmt_data = [];

    foreach ($data as $value) {
      $type = 's';

      if (is_int($value)) {
        $type = 'i';
      } else if (is_string($value)) {
        $type = 's';
      } else if (is_double($value)) {
        $type = 'd';
      }

      if ($type) {
        $types .= $type;
        $stmt_data[] = $value;
      }
    }

    $values = array_merge([$stmt, $types], $stmt_data);

    $func = 'mysqli_stmt_bind_param';
    $func(...$values);

    if (mysqli_errno($link) > 0) {
      $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
      die($errorMsg);
    }
  }

  return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
  $number = (int) $number;
  $mod10 = $number % 10;
  $mod100 = $number % 100;

  switch (true) {
    case ($mod100 >= 11 && $mod100 <= 20):
      return $many;

    case ($mod10 > 5):
      return $many;

    case ($mod10 === 1):
      return $one;

    case ($mod10 >= 2 && $mod10 <= 4):
      return $two;

    default:
      return $many;
  }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
  $name = 'templates/' . $name;
  $result = '';

  if (!is_readable($name)) {
    return $result;
  }

  ob_start();
  extract($data);
  require $name;

  $result = ob_get_clean();

  return $result;
}

/**
 * @param integer $num Price of lot
 * @return string Formatted price of lot
 */
function format_num($num): string
{
  $num = ceil($num);

  if ($num >= 1000) {
    // 1
    // $num = number_format($num, 0, '', ' ');

    //2
    $array_num = [];
    $first_num = '' . $num;

    do {
      array_unshift($array_num, substr($first_num, -3));

      $first_num = substr($first_num, 0, -3);
    } while (strlen($first_num) > 3);

    $num = $first_num . ' ' . join(' ', $array_num);
  }

  return $num . ' ' . '₽';
}

/**
 * @param string $date_string YYYY-MM-DD
 * @return array
 */
function get_time_left($date_string): array
{
  // Get date string in YYYY-MM-DD format
  $date_string = explode(' ', $date_string)[0];

  // Check date format
  if (!is_date_valid($date_string)) {
    return [0, 0];
  }

  date_default_timezone_set('Europe/Kyiv');

  // 1 - By Unixtime
  // $cur_date = time();
  // $end_date = strtotime($date_string);

  // 2 - date_*
  $cur_date = date_create();
  $end_date = date_create($date_string);

  if ($cur_date >= $end_date) {
    return [0, 0];
  }

  // 1 - By Unixtime
  // $diff = $end_date - $cur_date;

  // $rest_of_hours = floor($diff / (60 * 60));
  // $rest_of_minutes = floor($diff / 60) - $rest_of_hours * 60;

  // 2 - date_*
  $diff = date_diff($cur_date, $end_date);

  $rest_of_hours = $diff->h + $diff->d * 24;
  $rest_of_minutes = $diff->i;

  return [
    $rest_of_hours,
    $rest_of_minutes,
  ];
}
