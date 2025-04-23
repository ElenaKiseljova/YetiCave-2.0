<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/DBController.php';

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
        "COUNT(b.id) bets_cnt, " .
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
        "winner_bet_id IS NULL " .
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
   * @param ?string|int $where
   * @param ?'search'|'category $whereType
   * @return array
   */
  public function count($con, $where = null, $whereType = 'search')
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Count SQL query string
      $sqlLotsCount = "SELECT COUNT(*) cnt FROM lots";

      // If Where
      if (isset($where)) {
        // For STMT
        $data = [];

        if ($whereType === 'search' && !empty(($search = htmlspecialchars(trim($where))))) {
          $data[] = $search;

          $sqlLotsCount .= " WHERE MATCH (title, description) AGAINST (?)";
        } else  if ($whereType === 'category' && ($categoryId = intval($where))) {
          $data[] = $categoryId;

          $sqlLotsCount .=  " WHERE category_id = ?";
        }


        $stmt = DBController::getPrepareSTMT($con, $sqlLotsCount, $data);

        mysqli_stmt_execute($stmt);

        // Create query for geting list of Lots
        $result = mysqli_stmt_get_result($stmt);
      } else {
        $result = mysqli_query($con, $sqlLotsCount);
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
   * @param ?string|int $where
   * @param ?'search'|'category' $whereType
   * @return array
   */
  public function paginate($con, $perPage = 9, $offset = 0, $where = null, $whereType = 'search')
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Data for STMT
      $data = [];

      // Default Where
      $sqlWhere = "";
      if (isset($where)) {
        if ($whereType === 'search' && !empty(($search = htmlspecialchars(trim($where))))) {
          $data[] = $search;

          $sqlWhere = "WHERE MATCH (l.title, l.description) AGAINST (?) ";
        } else if ($whereType === 'category' && ($categoryId = intval($where))) {
          $data[] = $categoryId;

          $sqlWhere = "WHERE l.category_id = ? ";
        }
      }

      // Search SQL query string
      $sqlLots =
        "SELECT " .
        "l.id, " .
        "l.title, " .
        "price_start, " .
        "MAX(b.price) price_current, " .
        "COUNT(b.id) bets_cnt, " .
        "image, " .
        "expiration_date, " .
        "c.title category_name " .
        " FROM " .
        "categories c " .
        "RIGHT JOIN ( " .
        " lots l " .
        "LEFT JOIN bets b ON l.id = b.lot_id " .
        " ) ON l.category_id = c.id " .
        $sqlWhere .
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
        "winner_bet_id, " .
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
   * @param int $userId
   * @param bool $redirectAfter
   * @return array
   */
  public function create($con, $data, $userId, $redirectAfter = true)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Create SQL query string
      $sqlLot = 'INSERT INTO lots (slug, title, image, description, price_start, price_step, expiration_date, user_id, winner_bet_id, category_id )' .
        'VALUES (?, ?, ?, ?, ?, ?, ?, ' . $userId . ', NULL, ?)';

      $stmt = DBController::getPrepareSTMT($con, $sqlLot, $data);

      mysqli_stmt_execute($stmt);

      if ($redirectAfter) {
        // Get lot id
        $lotId = mysqli_insert_id($con);

        // Redirect to the lot page
        header('Location: /lot?id=' . $lotId);
      } else {
        $response['success'] = true;
      }
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

  /**
   * @param \mysqli $con
   * @param int $lotId
   * @param int $betId
   * @param bool $redirectAfter
   * @return array
   */
  public function setWin($con, $lotId, $betId, $redirectAfter = true)
  {
    $betId = intval($betId);
    $lotId = intval($lotId);

    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Create SQL query string
      $sqlLot = 'UPDATE lots SET winner_bet_id = ? WHERE id = ?';

      $stmt = DBController::getPrepareSTMT($con, $sqlLot, [$betId, $lotId]);

      mysqli_stmt_execute($stmt);

      if ($redirectAfter) {
        header('Location: /lots');
      } else {
        $response['success'] = true;
      }
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Updating Lot «' . $lotId . '» failed due to an error: ' . mysqli_error($con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }
}
