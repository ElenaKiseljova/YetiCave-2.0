<?php
require_once 'controllers/DBController.php';

class LotController
{
  /**
   * @param \mysqli $con
   * @param int $limit
   * @return array
   */
  public function getList($con, $limit = 6)
  {
    $limit = intval($limit);

    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Create SQL query string
      $sqlLots =
        "SELECT " .
        "l.id, " .
        "l.title, " .
        "price_start, " .
        "MAX(b.price) price_current, " .
        "image, " .
        "expiration_date, " .
        "c.title category_name " .
        " FROM " .
        "categories c " .
        "RIGHT JOIN ( " .
        " lots l " .
        "LEFT JOIN bets b ON l.id = b.lot_id " .
        " ) ON l.category_id = c.id " .
        "WHERE " .
        "( " .
        "winner_id IS NULL " .
        "AND l.expiration_date > NOW() " .
        ") " .
        "GROUP BY " .
        "l.id " .
        "ORDER BY " .
        "l.created_at DESC " .
        "LIMIT ?";

      $stmt = DBController::getPrepareSTMT($con, $sqlLots, [$limit]);

      mysqli_stmt_execute($stmt);

      // Create query for geting list of Lots
      $result = mysqli_stmt_get_result($stmt);

      // Request success
      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting list of Lots failed due to an error: ' . mysqli_error($con);

        $response['error'] = [
          'code' => $errorCode,
          'message' => $errorMessage
        ];
      }
    }

    return $response;
  }
}
