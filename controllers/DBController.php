<?php
class DBController
{
  /**
   * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
   *
   * @param $link mysqli Ресурс соединения
   * @param $sql string SQL запрос с плейсхолдерами вместо значений
   * @param array $data Данные для вставки на место плейсхолдеров
   *
   * @return mysqli_stmt Подготовленное выражение
   */
  static function getPrepareSTMT($link, $sql, $data = [])
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
   * Устанавливает timezone в MySQL
   *
   * @param \mysqli $con
   */
  private function setTimezone($con)
  {
    $now = new DateTime();

    $mins = $now->getOffset() / 60;

    $sgn = ($mins < 0 ? -1 : 1);
    $mins = abs($mins);
    $hrs = floor($mins / 60);
    $mins -= $hrs * 60;

    $offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);

    mysqli_query($con, "SET time_zone='$offset'");
  }

  /**
   * @param \mysqli $con
   * @return mysqli|false
   */
  public function select($con)
  {
    require $_SERVER['DOCUMENT_ROOT'] . '/env/db.php';

    try {
      // Set charset
      mysqli_set_charset($con, $dbParameters['charset']);

      mysqli_select_db($con, $dbParameters['name']);
    } catch (\Throwable $th) {
      // DB error connect
      if (mysqli_errno($con)) {
        $errorMessage = mysqli_error($con);

        print('An attempt to select the database «' . $dbParameters['name'] . '» failed: ' . $errorMessage);

        die();
      } else {
        throw $th;
      }
    }
  }

  /**
   * @param bool $selectDbManualy
   * @return mysqli|false
   */
  public function connect($selectDbManualy = false)
  {
    require $_SERVER['DOCUMENT_ROOT'] . '/env/db.php';

    try {
      // DB connect
      $con = mysqli_connect($dbParameters['host'], $dbParameters['user'], $dbParameters['password']);

      $this->setTimezone($con);

      // DB success connect
      if (!$selectDbManualy) {
        $this->select($con);
      }

      return $con;
    } catch (\Throwable $th) {
      // DB error connect
      if (mysqli_connect_errno()) {
        $errorMessage = mysqli_connect_error();

        print('Connection to the database failed due to an error: ' . $errorMessage);

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
  public function drop($con, $name)
  {
    try {
      // Drop DB SQL string
      $sqlString = "DROP DATABASE IF EXISTS $name";

      // Drop DB SQL query
      mysqli_query($con, $sqlString);
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
  public function create($con, $name): bool
  {
    try {
      $sqlCreateDb =
        "CREATE DATABASE $name DEFAULT CHARACTER " .
        "SET " .
        "utf8 DEFAULT COLLATE utf8_general_ci";

      $result = mysqli_query($con, $sqlCreateDb);

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
  public function dropTable($con, $name)
  {
    try {
      // Drop DB Table SQL string
      $sqlString = "DROP TABLE IF EXISTS $name";

      // Drop DB SQL query
      mysqli_query($con, $sqlString);
    } catch (\Throwable $th) {
      //throw $th;
    }
  }

  /**
   *
   * @param \mysqli $con
   * @param string $sqlString
   * @return array
   */
  function createTable($con, $sqlString)
  {
    $response = [
      'success' => null,
      'error' => null
    ];

    try {
      $result = mysqli_query($con, $sqlString);

      $response['success'] = !!$result;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Cannot create table: ' . mysqli_error($con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }
}
