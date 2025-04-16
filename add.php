<?php
require_once 'utils/helpers.php';
require_once 'utils/set.php';
require_once 'utils/auth.php';
require_once 'utils/categories.php';
require_once 'controllers/DBController.php';

$pageContentData = [
  'categories' => $categories,
  'nav' => $nav
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $lot = $_POST;

  // Array of Errors
  $errors = [];
  // Validation rules
  $rules = [
    'category_id' => function () {
      return validateFilled('category_id');
    },
    'title' => function () {
      return validateLength('title', 10, 200);
    },
    'slug' => function () {
      return validateLength('slug', 5, 200);
    },
    'description' => function () {
      return validateLength('description', 10, 3000);
    },
    'expiration_date' => function () {
      return validateDate('expiration_date');
    },
    'price_start' => function () {
      return validateInt('price_start');
    },
    'price_step' => function () {
      return validateInt('price_step');
    },
  ];

  // Check lot fields
  foreach ($lot as $key => $value) {
    if (isset($rules[$key])) {
      $rule = $rules[$key];
      $errors[$key] = $rule();
    }
  }

  // Check file
  if (isset($_FILES['image']) && $_FILES['image']['size']) {
    $file = $_FILES['image'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileTmpPath = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileName = $file['name'];

    $fileType = finfo_file($finfo, $fileTmpPath);

    if (!in_array($fileType, ['image/png', 'image/jpg', 'image/jpeg'])) {
      $errors['image'] = 'Файл может быть только c расширением .png, .jpg, .jpeg';
    } else if ($fileSize > 1000000) {
      $errors['image'] = 'Максимальный размер файла: 1Мб';
    } else {
      // Get extension
      $extension = array_reverse(explode('.', $fileName))[0];

      // Generate new name
      $fileName = uniqid() . '.' . $extension;

      // Add path
      $lot['image'] = $fileName;

      // Move file
      $moved = move_uploaded_file($fileTmpPath, getFilePath($fileName));

      if (!$moved) {
        $errors['image'] = 'Не удалось сохранить файл';
      }
    }
  } else {
    $errors['image'] = 'Выберите файл';
  }

  // Category ID exists

  // Slug is unique

  $errors = array_filter($errors);

  if (empty($errors)) {
    // Create lot
    $sqlString = 'INSERT INTO lots (slug, title, image, description, price_start, price_step, expiration_date, user_id, winner_id, category_id )' .
      'VALUES (?, ?, ?, ?, ?, ?, ?, 1, NULL, ?)';

    $stmt = DBController::getPrepareSTMT($con, $sqlString, [$lot['slug'], $lot['title'], $lot['image'], $lot['description'], $lot['price_start'], $lot['price_step'], $lot['expiration_date'], $lot['category_id']]);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
      // Get lot id
      $lotId = mysqli_insert_id($con);

      // Redirect to the lot page
      header('Location: /lot?id=' . $lotId);
    } else {
      $errors['global'] = mysqli_error($con);
    }
  }

  // Show errors in add lot page
  $pageContentData['errors'] = $errors;
}

$pageContent = includeTemplate('pages/add.php', $pageContentData);

$layoutData = [
  'isFull' => true,
  'styles' => ['../css/libs/flatpickr.min.css'],
  'title' => 'Добавить лот',
  'content' => $pageContent,
  'nav' => $nav,
  'isAuth' => $isAuth,
  'userName' => $userName
];
$layoutContent = includeTemplate('layout.php', $layoutData);

print($layoutContent);
