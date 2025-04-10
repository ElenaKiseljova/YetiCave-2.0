<?php

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
 * @param \mysqli $con
 * @return mysqli|false
 */
function db_select($con)
{
  require 'db.php';

  try {
    // Set charset
    mysqli_set_charset($con, $db['charset']);

    mysqli_select_db($con, $db['name']);
  } catch (\Throwable $th) {
    // DB error connect
    if (mysqli_errno($con)) {
      $error_message = mysqli_error($con);

      print('An attempt to select the database «' . $db['name'] . '» failed: ' . $error_message);

      die();
    } else {
      throw $th;
    }
  }
}

/**
 * @param bool $select_db_manualy
 * @return mysqli|false
 */
function db_connect($select_db_manualy = false)
{
  require 'db.php';

  try {
    // DB connect
    $con = mysqli_connect($db['host'], $db['user'], $db['password']);

    // DB success connect
    if (!$select_db_manualy) {
      db_select($con);
    }

    return $con;
  } catch (\Throwable $th) {
    // DB error connect
    if (mysqli_connect_errno()) {
      $error_message = mysqli_connect_error();

      print('Connection to the database failed due to an error: ' . $error_message);

      die();
    } else {
      throw $th;
    }
  }
}

/**
 * @param \mysqli $con
 * @param string $name
 * @return void
 */
function db_drop($con, $name)
{
  try {
    // Drop DB SQL string
    $sql_string = "DROP DATABASE IF EXISTS $name";

    // Drop DB SQL query
    mysqli_query($con, $sql_string);
  } catch (\Throwable $th) {
    //throw $th;
  }
}

/**
 *
 * @param \mysqli $con
 * @param string $name
 * @return bool
 */
function db_create($con, $name): bool
{
  try {
    $sql_create_db =
      "CREATE DATABASE $name DEFAULT CHARACTER " .
      "SET " .
      "utf8 DEFAULT COLLATE utf8_general_ci";

    $result = mysqli_query($con, $sql_create_db);

    return !!$result;
  } catch (\Throwable $th) {
    // throw $th;

    return false;
  }
}

/**
 * @param \mysqli $con
 * @param string $name
 * @return void
 */
function db_drop_table($con, $name)
{
  try {
    // Drop DB Table SQL string
    $sql_string = "DROP TABLE IF EXISTS $name";

    // Drop DB SQL query
    mysqli_query($con, $sql_string);
  } catch (\Throwable $th) {
    //throw $th;
  }
}

/**
 *
 * @param \mysqli $con
 * @param string $sql_string
 * @return array
 */
function db_create_table($con, $sql_string)
{
  $response = [
    'success' => null,
    'error' => null
  ];

  try {
    $result = mysqli_query($con, $sql_string);

    $response['success'] = !!$result;
  } catch (\Throwable $th) {
    if ($error_code = mysqli_errno($con)) {
      $response['error'] = [
        'code' => $error_code,
        'message' => 'Cannot create table: ' . mysqli_error($con)
      ];
    }
  }

  return $response;
}
