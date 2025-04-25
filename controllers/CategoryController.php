<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/DBController.php';

class CategoryController
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
   * @return array
   */
  public function getList()
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Create SQL query string
      $sqlCategories = "SELECT * FROM categories";

      // Create query for geting list of Categories
      $result = mysqli_query($this->con, $sqlCategories);

      // Request success
      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($this->con)) {
        $errorMessage = 'Getting list of Categories failed due to an error: ' . mysqli_error($this->con);
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
      $sqlColLots = "SELECT $colum FROM categories";

      $result = mysqli_query($this->con, $sqlColLots);

      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($this->con)) {
        $errorMessage = 'Getting list of «' . $colum . '» Categories failed due to an error: ' . mysqli_error($this->con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }

  /**
   * @param int $id
   * @return array
   */
  public function getById($id)
  {
    $id = intval($id);

    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Category SQL query string
      $sqlCategory = "SELECT * FROM categories WHERE id = $id";

      // Create query for geting Category
      $result = mysqli_query($this->con, $sqlCategory);

      // Request success
      $row = mysqli_fetch_assoc($result);

      $response['data'] = $row;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($this->con)) {
        $errorMessage = "Getting Category #$id failed due to an error: " . mysqli_error($this->con);
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
  public function create($data)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Create SQL query string
      $sqlCategory = "INSERT INTO categories (slug, title) VALUES (?, ?)";

      $stmt = DBController::getPrepareSTMT($this->con, $sqlCategory, $data);

      mysqli_stmt_execute($stmt);

      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($this->con)) {
        $errorMessage = 'Creating Category failed due to an error: ' . mysqli_error($this->con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }
}
