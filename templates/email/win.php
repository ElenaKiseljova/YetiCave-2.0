<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ваша ставка победила</title>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      width: 100%;

      margin: 0;
      padding: 40px 20px;
    }

    div {
      display: flex;

      flex-direction: column;

      align-items: center;
      justify-content: flex-start;

      width: 100%;

      padding: 30px 10px;

      border: 3px dotted #471aa1;

      border-radius: 10px;
    }

    h1 {
      margin-top: 0;
      margin-bottom: 30px;

      text-transform: uppercase;
    }

    a {
      display: flex;

      align-items: center;
      justify-content: center;

      width: fit-content;

      margin-top: 20px;

      padding: 10px 30px;

      color: #ffffff;

      text-transform: uppercase;

      text-decoration: none;

      border-radius: 5px;

      background-color: #471aa1;

      transition: background-color 0.5s easy;
    }

    a:hover {
      background-color: #642bd6;
    }
  </style>
</head>

<body>
  <div>
    <?php
    $siteUrl = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']) ? 'https' : 'http';
    $siteUrl .= '://';
    $siteUrl .= $_SERVER['SERVER_NAME'];
    ?>
    <h1>Ваша ставка победила!</h1>

    <p>Просмотреть лот можно по ссылке ниже:</p>

    <a href="<?= $siteUrl; ?>/lot?id=<?= $lotId ?? ''; ?>">Смотреть лот</a>

    <p>Перейдите к списку Ваших ставок, чтобы связаться с владельцем лота.</p>

    <a href="<?= $siteUrl; ?>/my-bets">Мои ставки</a>
  </div>
</body>

</html>