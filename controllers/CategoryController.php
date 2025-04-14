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
      $sql_categories = "SELECT * FROM categories";

      // Create query for geting list of Lots
      $result = mysqli_query($con, $sql_categories);

      // Request success
      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      // Request error
      if ($error_code = mysqli_errno($con)) {
        $error_message = 'Getting list of Categories failed due to an error: ' . mysqli_error($con);

        $response['error'] = [
          'code' => $error_code,
          'message' => $error_message
        ];
      }
    }

    return $response;
  }
}
