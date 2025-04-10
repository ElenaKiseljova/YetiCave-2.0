<?php

/**
 * @param \mysqli $con
 * @return array
 */
function get_lots($con)
{
  $response = [
    'data' => null,
    'success' => null,
    'error' => null,
  ];

  try {
    // Create SQL query string
    $sql_lots = "SELECT * FROM lots";

    // Create query for geting list of Lots
    $result = mysqli_query($con, $sql_lots);

    // Request success
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $response['data'] = $rows;
    $response['success'] = true;
  } catch (\Throwable $th) {
    // Request error
    if ($error_code = mysqli_errno($con)) {
      $error_message = 'Getting list of Lots failed due to an error: ' . mysqli_error($con);

      $response['error'] = [
        'code' => $error_code,
        'message' => $error_message
      ];
    }
  }

  return $response;
}
