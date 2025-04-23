<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/DBController.php';
class BetController
{
  /**
   * @param \mysqli $con
   * @param ?int $lotId
   * @return array
   */
  public function getList($con, $lotId = null)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Bets SQL query string
      $sqlBets = "SELECT b.id, price, b.created_at, u.id user_id, u.name user_name FROM bets b JOIN users u ON b.user_id = u.id";

      if (isset($lotId)) {
        $lotId = intval($lotId);

        $sqlBets .= ' WHERE lot_id = ? ORDER BY price DESC';

        $stmt = DBController::getPrepareSTMT($con, $sqlBets, [$lotId]);

        mysqli_stmt_execute($stmt);

        // Create query for geting list of Bets
        $result = mysqli_stmt_get_result($stmt);
      } else {
        $result = mysqli_query($con, $sqlBets);
      }

      // Request success
      $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

      $response['data'] = $rows;
      $response['success'] = true;
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Getting list of Bets failed due to an error: ' . mysqli_error($con);
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
   * @param int $userId
   * @return array
   */
  public function getHistory($con, $userId)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Bets SQL query string
      $sqlBets =
        "SELECT " .
        "b.id, lot_id, price, b.created_at, u.id user_id, u.contacts user_contacts, l.image lot_image, l.title lot_title, l.expiration_date lot_expiration_date, l.winner_bet_id lot_winner_bet_id, c.title category_title " .
        "FROM bets b " .
        "JOIN users u ON b.user_id = u.id " .
        "JOIN lots l ON b.lot_id = l.id " .
        "JOIN categories c ON l.category_id = c.id " .
        "WHERE b.user_id = ? " .
        "ORDER BY b.created_at DESC";

      $userId = intval($userId);

      $stmt = DBController::getPrepareSTMT($con, $sqlBets, [$userId]);

      mysqli_stmt_execute($stmt);

      // Create query for geting list of Bets
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
        $errorMessage = 'Getting list of Bets failed due to an error: ' . mysqli_error($con);
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
   * @param int $price
   * @param int $userId
   * @param bool $redirectAfter
   * @return array
   */
  public function create($con, $lotId, $price, $userId, $redirectAfter = true)
  {
    $response = [
      'data' => null,
      'success' => null,
      'error' => null,
    ];

    try {
      // Bets SQL query string
      $sqlBets = 'INSERT INTO bets (user_id, lot_id, price) VALUES (?, ?, ?)';

      $stmt = DBController::getPrepareSTMT($con, $sqlBets, [intval($userId), intval($lotId), intval($price)]);

      mysqli_stmt_execute($stmt);

      if ($redirectAfter) {
        header('Location: /my-bets');
      } else {
        $response['success'] = true;
      }
    } catch (\Throwable $th) {
      $errorCode = $th->getCode();
      $errorMessage = $th->getMessage();

      // Request error
      if ($errorCode = mysqli_errno($con)) {
        $errorMessage = 'Creating Bet failed due to an error: ' . mysqli_error($con);
      }

      $response['error'] = [
        'code' => $errorCode,
        'message' => $errorMessage
      ];
    }

    return $response;
  }
}
