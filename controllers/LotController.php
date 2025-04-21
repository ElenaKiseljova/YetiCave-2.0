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
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting list of Lots failed due to an error: ' . mysqli_error($con);
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
   * @param ?string $search
   * @return array
   */
  public function count($con, $search = '')
  {
    $search = trim(htmlspecialchars($search));

    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Search SQL query string
      $sqlLots = "SELECT COUNT(*) cnt FROM lots";

      // If search no empty
      if (!empty($search)) {
        $sqlLots .= " WHERE MATCH (title, description) AGAINST (?)";

        $stmt = DBController::getPrepareSTMT($con, $sqlLots, [$search]);

        mysqli_stmt_execute($stmt);

        // Create query for geting list of Lots
        $result = mysqli_stmt_get_result($stmt);
      } else {
        $result = mysqli_query($con, $sqlLots);
      }

      // Request success
      $row = mysqli_fetch_assoc($result);

      $response['data'] = $row['cnt'];
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting count of Lots failed due to an error: ' . mysqli_error($con);
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
   * @param int $perPage
   * @param int $offset
   * @param ?string $search
   * @return array
   */
  public function paginate($con, $perPage = 9, $offset = 0, $search = '')
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Default
      $sqlSearch = "";
      $data = [];
      if (!empty($search)) {
        $sqlSearch = "WHERE MATCH (l.title, l.description) AGAINST (?) ";

        $data[] = $search;
      }

      // Search SQL query string
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
        $sqlSearch .
        "GROUP BY " .
        "l.id " .
        "ORDER BY " .
        "l.created_at DESC " .
        "LIMIT ? OFFSET ?";

      $stmt = DBController::getPrepareSTMT($con, $sqlLots, [...$data, $perPage, $offset]);

      mysqli_stmt_execute($stmt);

      // Create query for geting list of Lots
      $result = mysqli_stmt_get_result($stmt);

      // Request success
      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting list of Lots failed due to an error: ' . mysqli_error($con);
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
  public function getItem($con, $id)
  {
    $id = intval($id);

    $response = [
      'data' => null,
      'success' => null,
      'error' => null
    ];

    try {
      // Create SQL query string
      $sqlLot =
        "SELECT " .
        "l.id, " .
        "l.slug, " .
        "l.title, " .
        "price_start, " .
        "MAX(b.price) price_current, " .
        "price_step, " .
        "image, " .
        "expiration_date, " .
        "l.created_at, " .
        "description, " .
        "winner_id, " .
        "l.user_id, " .
        "category_id, " .
        "c.title category_name " .
        "FROM " .
        "categories c " .
        "RIGHT JOIN ( " .
        "lots l " .
        "LEFT JOIN bets b ON l.id = b.lot_id " .
        ") ON l.category_id = c.id " .
        "WHERE " .
        "l.id = ? " .
        "GROUP BY " .
        "l.id ";

      $stmt = DBController::getPrepareSTMT($con, $sqlLot, [$id]);

      mysqli_stmt_execute($stmt);

      // Create query for geting list of Lots
      $result = mysqli_stmt_get_result($stmt);

      // Request success
      $row = mysqli_fetch_assoc($result);

      if ($row) {
        $response['data'] = $row;
        $response['success'] = true;
      } else {
        throw new Exception("Lot ID #$id does not exist", 404);
      }
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = "Getting Lot #$id failed due to an error: " . mysqli_error($con);
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
      $sqlColLots = "SELECT $colum FROM lots";

      $result = mysqli_query($con, $sqlColLots);

      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting list of «' . $colum . '» Lots failed due to an error: ' . mysqli_error($con);
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
      $sqlLot = 'INSERT INTO lots (slug, title, image, description, price_start, price_step, expiration_date, user_id, winner_id, category_id )' .
        'VALUES (?, ?, ?, ?, ?, ?, ?, ' . $_SESSION['user']['id'] . ', NULL, ?)';

      $stmt = DBController::getPrepareSTMT($con, $sqlLot, $data);

      mysqli_stmt_execute($stmt);

      // Get lot id
      $lotId = mysqli_insert_id($con);

      // Redirect to the lot page
      header('Location: /lot?id=' . $lotId);
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Creating Lot failed due to an error: ' . mysqli_error($con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }
}
