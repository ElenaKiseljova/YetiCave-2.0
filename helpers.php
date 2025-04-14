<?php

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * isDateValid('2019-01-01'); // true
 * isDateValid('2016-02-29'); // true
 * isDateValid('2019-04-31'); // false
 * isDateValid('10.10.2010'); // false
 * isDateValid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function isDateValid(string $date): bool
{
  $formatToCheck = 'Y-m-d';
  $dateTimeObj = date_create_from_format($formatToCheck, $date);

  return $dateTimeObj !== false && ($errs = date_get_last_errors()) ? array_sum($errs) === 0 : true;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remainingMinutes = 5;
 * echo "Я поставил таймер на {$remainingMinutes} " .
 *     getNounPluralForm(
 *         $remainingMinutes,
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
function getNounPluralForm(int $number, string $one, string $two, string $many): string
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
function includeTemplate($name, array $data = [])
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
 * Возвращает форматированную цену в рублях
 * @param integer $num Price of lot
 * @return string Formatted price of lot
 */
function formatNum($num): string
{
  $num = ceil($num);

  if ($num >= 1000) {
    // 1
    // $num = number_format($num, 0, '', ' ');

    //2
    $arrayNum = [];
    $firstNum = '' . $num;

    do {
      array_unshift($arrayNum, substr($firstNum, -3));

      $firstNum = substr($firstNum, 0, -3);
    } while (strlen($firstNum) > 3);

    $num = $firstNum . ' ' . join(' ', $arrayNum);
  }

  return $num . ' ' . '₽';
}

/**
 * Возвращает в виде массива оставшиеся часы и минуты до закрытия лота
 * @param string $dateString YYYY-MM-DD
 * @return array
 */
function getTimeLeft($dateString): array
{
  // Get date string in YYYY-MM-DD format
  $dateStringFormatted = explode(' ', $dateString)[0];

  // Check date format
  if (!isDateValid($dateStringFormatted)) {
    return [0, 0];
  }

  date_default_timezone_set('Europe/Kyiv');

  // 1 - By Unixtime
  // $curDate = time();
  // $endDate  = strtotime($dateString);

  // 2 - date_*
  $curDate = date_create();
  $endDate  = date_create($dateString);

  if ($curDate >= $endDate) {
    return [0, 0];
  }

  // 1 - By Unixtime
  // $diff = $endDate  - $curDate;

  // $restOfHours = floor($diff / (60 * 60));
  // $restOfMinutes = floor($diff / 60) - $restOfHours * 60;

  // 2 - date_*
  $diff = date_diff($curDate, $endDate);

  $restOfHours = $diff->h + $diff->d * 24;
  $restOfMinutes = $diff->i;

  return [
    $restOfHours,
    $restOfMinutes,
  ];
}

/**
 * Возвращает ссылку с сохраненными квери-параметрами
 * @param string $filePath
 * @param array $newParameters
 * @param 'file'|'base' $pathName
 * @return string
 */
function getUrlWithQuery($filePath, $newParameters = [], $pathName = 'file')
{
  // Summary array of parameters
  $params = [
    ...$_GET,
    ...$newParameters
  ];

  // Flag
  $pathName = $pathName === 'file' ? PATHINFO_FILENAME : PATHINFO_BASENAME;

  // Base URL
  $scriptname = pathinfo($filePath, $pathName);

  // Query string
  $query = http_build_query($params);

  // Base URL with Query
  $url = "/" . ($scriptname === 'index' ? '' : $scriptname) . "?" . $query;

  return $url;
}

/**
 * Возвращает значение, переданное методом POST
 * @param string $name
 * @return string
 */
function getPostVal($name)
{
  return $_POST[$name] ?? "";
}

/**
 * Проверка на email
 * @param string $name
 * @return string|null
 */
function validateEmail($name)
{
  if (!filter_input(INPUT_POST, $name, FILTER_VALIDATE_EMAIL)) {
    return "Введите корректный email";
  };
}

/**
 * Проверка заполненности
 * @param string $name
 * @return string|null
 */
function validateFilled($name)
{
  if (empty($_POST[$name])) {
    return "Это поле должно быть заполнено";
  }
}

/**
 * Проверка длины
 * @param string $name
 * @param int $min
 * @param int $max
 * @return string|null
 */
function isCorrectLength($name, $min, $max)
{
  $len = strlen($_POST[$name]);

  if ($len < $min or $len > $max) {
    return "Значение должно быть от $min до $max символов";
  }
}
