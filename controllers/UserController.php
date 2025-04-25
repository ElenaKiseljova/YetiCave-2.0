<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/DBController.php';

class UserController
{
  /**
   * @var \mysqli
   */
  private $con;

  public function __construct(\mysqli $con)
  {
    $this->con = $con;
  }

  /**
   * @param string $col email, id, name etc.
   * @param string|int $value
   * @return array
   */
  public function getBy($col, $value)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Get User SQL query string
      $sqlUser = 'SELECT * FROM users WHERE ' . $col . ' = ?';

      $stmt = DBController::getPrepareSTMT($this->con, $sqlUser, [$value]);

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
      if ($errorCode = mysqli_errno($this->con)) {
        $errorMessage = 'Getting User by «' . $col . '» failed due to an error: ' . mysqli_error($this->con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }

  /**
   * @param string $colum id, slug, etc.
   * @return array
   */
  public function getAllCol($colum = 'id')
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

      $result = mysqli_query($this->con, $sqlColUsers);

      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($this->con)) {
        $errorMessage = 'Getting list of «' . $colum . '» Users failed due to an error: ' . mysqli_error($this->con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }

  /**
   * @param array $data
   * @param bool $redirectAfter
   * @return array
   */
  public function create($data, $redirectAfter = true)
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

      $stmt = DBController::getPrepareSTMT($this->con, $sqlUser, $data);

      mysqli_stmt_execute($stmt);

      if ($redirectAfter) {
        // Redirect to the login page
        header('Location: /login');
      } else {
        $response['success'] = true;
      }
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($this->con)) {
        $errorMessage = 'Creating User failed due to an error: ' . mysqli_error($this->con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }

  /**
   * @param array $data
   * @return array
   */
  public function login($data)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Get User by Email
      ['data' => $user] = $this->getBy('email', $data['email']);

      // Check Hash password
      if (!$user || !password_verify($data['password'], $user['password'])) {
        throw new Exception('Incorrect email address or password', 403);
      }

      // Create session
      session_start();

      $_SESSION['user']['id'] = $user['id'];

      // Redirect to the home page
      header('Location: /');
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($this->con)) {
        $errorMessage = 'Creating User failed due to an error: ' . mysqli_error($this->con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }
}
