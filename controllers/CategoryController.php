<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/DBController.php';

class CategoryController
{
  /**
   * @param \mysqli $con
   * @return array
   */
  public function getList($con)
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
      $result = mysqli_query($con, $sqlCategories);

      // Request success
      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting list of Categories failed due to an error: ' . mysqli_error($con);
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
      $sqlColLots = "SELECT $colum FROM categories";

      $result = mysqli_query($con, $sqlColLots);

      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting list of «' . $colum . '» Categories failed due to an error: ' . mysqli_error($con);
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
   * @param int $id
   * @return array
   */
  public function getById($con, $id)
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
      $result = mysqli_query($con, $sqlCategory);

      // Request success
      $row = mysqli_fetch_assoc($result);

      $response['data'] = $row;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = "Getting Category #$id failed due to an error: " . mysqli_error($con);
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
      // Create SQL query string
      $sqlCategory = "INSERT INTO categories (slug, title) VALUES (?, ?)";

      $stmt = DBController::getPrepareSTMT($con, $sqlCategory, $data);

      mysqli_stmt_execute($stmt);

      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Creating Category failed due to an error: ' . mysqli_error($con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }
}
