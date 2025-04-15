<?php
// Get Error code from $_SERVER
$errorCode = $_SERVER['REDIRECT_STATUS'];

$errorMessages = [
  '400' => [
    'title' => 'Bad Request',
    'description' => '',
  ],
  '401' => [
    'title' => 'Unautorized',
    'description' => '',
  ],
  '403' => [
    'title' => 'Forbidden',
    'description' => '',
  ],
  '404' => [
    'title' => 'Страница не найдена',
    'description' => 'Данной страницы не существует на сайте.',
  ],
  '500' => [
    'title' => 'Internal Server Error',
    'description' => '',
  ],
];

$errorTitle = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode]['title'] : 'Что-то пошло не так';
$errorDescription = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode]['description'] : 'Попробуйте воспользоваться навигацией и посетить другие страницы.';
?>

<section class="lot-item container">
  <h2><?= $errorCode; ?> <?= $errorTitle; ?></h2>
  <p><?= $errorDescription; ?></p>
</section>
