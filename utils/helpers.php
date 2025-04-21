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

  return $dateTimeObj !== false && ($errs = date_get_last_errors()) ? array_sum($errs) === 0 : !!$dateTimeObj;
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
 * @param string $date
 * @param array $classes
 * @retrn string
 */
function getTimerHTML($date, $classes = [])
{
  // String of classes
  $classes = implode(' ', $classes);

  $time = getTimeLeft(htmlspecialchars($date));
  $hours = $time[0];
  $minutes = $time[1];

  $timerClasses = $hours < 1 ? 'timer--finishing' : '';
  $timerText = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);


  return "
        <div class='$classes timer $timerClasses'>
          $timerText
        </div>
      ";
}

/**
 * Возвращает обновленную квери строку с сохраненными квери-параметрами
 * @param array $newParameters
 * @return string
 */
function withQuery($newParameters = [])
{
  // Summary array of parameters
  $params = [
    ...$_GET,
    ...$newParameters
  ];

  // Query string
  $query = http_build_query($params);

  return '?' . $query;
}

/**
 * Возвращает значение, переданное методом POST
 * @param string $name
 * @return string
 */
function getPostVal($name)
{
  return isset($_POST[$name]) ? htmlspecialchars($_POST[$name]) : "";
}

/**
 * Возвращает CSS класс ошибки поля
 * @param bool $isError
 * @return string
 */
function getFieldErrorClass($isError)
{
  return $isError ? "form__item--invalid" : "";
}

/**
 * Возвращает CSS класс ошибки поля ввода
 * @param bool $isError
 * @return string
 */
function getInputErrorClass($isError)
{
  return $isError ? "form__input--error" : "";
}

/**
 * Проверка на email
 * @param string $value
 * @return string|null
 */
function validateEmail($value)
{
  if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
    return "Введите корректный email";
  };
}

/**
 * Проверка заполненности
 * @param ?string $value
 * @return string|null
 */
function validateFilled($value)
{
  if (empty(trim($value ?? ''))) {
    return "Это поле должно быть заполнено";
  }
}

/**
 * Проверка длины
 * @param ?string $value
 * @param int $min
 * @param int $max
 * @return string|null
 */
function validateLength($value, $min, $max)
{
  $len = strlen(trim($value ?? ''));

  if ($len < $min or $len > $max) {
    return "Значение должно быть от $min до $max символов";
  }
}

/**
 * Проверка формата даты
 * @param ?string $value
 * @param ?string $min
 * @param ?string $max
 * @return string|null
 */
function validateDate($value, $min = null, $max = null)
{
  $value = trim($value ?? '');

  if (!isDateValid($value)) {
    return "Дата должна быть в формате ГГГГ-ММ-ДД";
  }

  // Min
  $min = trim($min ?? '');
  $haveMin = $min && isDateValid($min);

  // Max
  $max = trim($max ?? '');
  $haveMax = $max && isDateValid($max);

  // Check min and max
  if ($haveMin || $haveMax) {
    date_default_timezone_set('Europe/Kyiv');

    $endDate = date_create($value);

    $minDate = $haveMin ? date_create($min) : null;
    $maxDate  = $haveMax ? date_create($max) : null;

    if (!!$minDate && $endDate < $minDate) {
      return "Дата не может быть раньше $min";
    }

    if (!!$maxDate && $endDate > $maxDate) {
      return "Дата не может быть позже $max";
    }
  }
}

/**
 * Проверка целого числа
 * @param ?string|number $value
 * @return string|null
 */
function validateInt($value)
{
  $value = trim($value ?? '');
  $valueInt = intval($value);

  if (!$valueInt || strval($valueInt) !== $value) {
    return "Значение должно быть целым числом больше 0";
  }
}

/**
 * Проверка slug
 * @param ?string $value
 * @param array $notAllowedSlugs
 * @param int $min
 * @param int $max
 * @return string|null
 */
function validateSlug($value, $notAllowedSlugs, $min, $max)
{
  if (in_array(trim($value ?? ''), $notAllowedSlugs)) {
    return 'Слаг уже существует';
  }

  if ($lenErr = validateLength($value, $min, $max)) {
    return $lenErr;
  }
}

/**
 * Проверка category id
 * @param ?int $value
 * @param array $allowedIds
 * @return string|null
 */
function validateCategory($value, $allowedIds)
{
  if (!in_array($value, $allowedIds)) {
    return 'Категория не существует';
  }
}

/**
 * Возвращает путь к файлу в сторе
 * @param string $filePath
 * @return string
 */
function getFilePath($filePath)
{
  $STORE = 'uploads/';

  return $STORE . $filePath;
}
