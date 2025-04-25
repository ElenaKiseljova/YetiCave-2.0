<?php
require_once 'utils/helpers.php';
require_once 'utils/set.php';
require_once 'utils/auth.php';
require_once 'utils/categories.php';
require_once 'controllers/UserController.php';

if ($isAuth) {
  header('Location: /');

  die();
}

$pageContentData = ['nav' => $nav];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userCon = new UserController($con);

  // Required fields
  $required = ['email', 'password'];

  // Array of Errors
  $errors = [];

  // Validation rules
  $rules = [
    'email' => function ($value) {
      return validateEmail($value);
    },
    'password' => function ($value) {
      return validateLength($value, 6, 50);
    },
  ];

  // Create an array with the correct sequence of values ​​for STMT
  $options = [
    'email' => FILTER_UNSAFE_RAW,
    'password' => FILTER_UNSAFE_RAW,
  ];

  $user = filter_input_array(INPUT_POST, $options, true);

  // Run validation fields
  foreach ($user as $key => $value) {
    $hasValue = !empty(trim($value ?? ''));

    // Custome validation check
    if ($hasValue && isset($rules[$key])) {
      $rule = $rules[$key];
      $errors[$key] = $rule($value);
    }

    // Required check
    if (!$hasValue && in_array($key, $required)) {
      $errors[$key] = "Поле $key обязательно для заполнения";
    }
  }

  $errors = array_filter($errors);

  if (empty($errors)) {
    // Formatting data
    foreach ($user as $key => $value) {
      if (is_string($value)) {
        // Trim spaces
        $user[$key] = trim($value);
      }
    }

    // Login user
    ['error' => $userLoginError] = $userCon->login($user);

    if (isset($userLoginError['message'])) {
      $errors['global'] = $userLoginError['message'];
    }
  }

  // Show errors in add user page
  $pageContentData['errors'] = $errors;
}

$pageContent = includeTemplate('pages/login.php', $pageContentData);

$layoutData = [
  'isFull' => true,
  'title' => 'Вход',
  'content' => $pageContent,
  'nav' => $nav,
  'isAuth' => $isAuth,
  'userName' => $userName
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
