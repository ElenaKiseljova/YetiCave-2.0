<?php
class UserController
{
  /**
   * @param \mysqli $con
   * @param string $col email, id, name etc.
   * @param string|int $value
   * @return array
   */
  public function getBy($con, $col, $value)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Get User SQL query string
      $sqlUser = 'SELECT * FROM users WHERE ' . $col . ' = ?';

      $stmt = DBController::getPrepareSTMT($con, $sqlUser, [$value]);

      mysqli_stmt_execute($stmt);

      $result = mysqli_stmt_get_result($stmt);

      // Get User
      $user = mysqli_fetch_assoc($result);

      $response['data'] = $user;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting User by «' . $col . '» failed due to an error: ' . mysqli_error($con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }

  /**
   * @param \mysqli $con
   * @param string $colum id, slug, etc.
   * @return array
   */
  public function getAllCol($con, $colum = 'id')
  {
    $colum = strip_tags(trim($colum));

    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Create SQL query string
      $sqlColUsers = "SELECT $colum FROM users";

      $result = mysqli_query($con, $sqlColUsers);

      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting list of «' . $colum . '» Users failed due to an error: ' . mysqli_error($con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }

  /**
   * @param \mysqli $con
   * @param array $data
   * @return array
   */
  public function create($con, $data)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Create Hash password
      $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

      // Create SQL query string
      $sqlUser = 'INSERT INTO users (name, email, password, contacts)' .
        'VALUES (?, ?, ?, ?)';

      $stmt = DBController::getPrepareSTMT($con, $sqlUser, $data);

      mysqli_stmt_execute($stmt);

      // Redirect to the login page
      header('Location: /login');
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Creating User failed due to an error: ' . mysqli_error($con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }

  /**
   * @param \mysqli $con
   * @param array $data
   * @return array
   */
  public function login($con, $data)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Get User by Email
      ['data' => $user] = $this->getBy($con, 'email', $data['email']);

      // Check Hash password
      if (!$user || !password_verify($data['password'], $user['password'])) {
        throw new Exception('Incorrect email address or password', 403);
      }

      // Create session
      session_start();

      $_SESSION['user']['name'] = $user['name'];
      $_SESSION['user']['id'] = $user['id'];

      // Redirect to the home page
      header('Location: /');
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Creating User failed due to an error: ' . mysqli_error($con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }
}
