<?php
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

      // Create query for geting list of Lots
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
}
