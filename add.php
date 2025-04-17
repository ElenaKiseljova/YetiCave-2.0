<?php
require_once 'utils/helpers.php';
require_once 'utils/set.php';
require_once 'utils/auth.php';
require_once 'utils/categories.php';
require_once 'controllers/DBController.php';
require_once 'controllers/LotController.php';

$pageContentData = [
  'categories' => $categories,
  'nav' => $nav
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $lotCon = new LotController();

  // Getting slugs of Lots
  ['data' => $lotsSlugs] = $lotCon->getAllCol($con, 'slug');

  // Slugs of Lots
  if (is_array($lotsSlugs)) {
    $lotsSlugs = array_column($lotsSlugs, 'slug');
  }

  // IDs of Categories
  $catIds = array_column($categories, 'id');

  // Required fields
  $required = ['title', 'slug', 'category_id', 'description', 'image', 'price_start', 'price_step', 'expiration_date'];

  // Array of Errors
  $errors = [];

  // Validation rules
  $rules = [
    'category_id' => function ($value) use ($catIds) {
      return validateCategory($value, $catIds);
    },
    'title' => function ($value) {
      return validateLength($value, 10, 200);
    },
    'slug' => function ($value) use ($lotsSlugs) {
      return validateSlug($value, $lotsSlugs, 5, 200);
    },
    'description' => function ($value) {
      return validateLength($value, 10, 3000);
    },
    'expiration_date' => function ($value) {
      return validateDate($value, date('Y-m-d'));
    },
    'price_start' => function ($value) {
      return validateInt($value);
    },
    'price_step' => function ($value) {
      return validateInt($value);
    },
  ];

  // Create an array with the correct sequence of values ​​for STMT
  $options = [
    'slug' => FILTER_UNSAFE_RAW,
    'title' => FILTER_UNSAFE_RAW,
    'image' => FILTER_UNSAFE_RAW,
    'description' => FILTER_UNSAFE_RAW,
    'price_start' => FILTER_UNSAFE_RAW,
    'price_step' => FILTER_UNSAFE_RAW,
    'expiration_date' => FILTER_UNSAFE_RAW,
    'category_id' => FILTER_UNSAFE_RAW
  ];

  $lot = filter_input_array(INPUT_POST, $options, true);

  // Run validation fields
  foreach ($lot as $key => $value) {
    if ($key === 'image') {
      continue;
    }

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



  // Check file
  if (isset($_FILES['image']) && $_FILES['image']['name'] && in_array('image', $required)) {
    $file = $_FILES['image'];

    $fileTmpPath = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileName = $file['name'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileType = finfo_file($finfo, $fileTmpPath);

    // Validation
    if (!in_array($fileType, ['image/png', 'image/jpg', 'image/jpeg'])) {
      $errors['image'] = 'Файл может быть только c расширением .png, .jpg, .jpeg';
    } else if ($fileSize > 1000000) {
      $errors['image'] = 'Максимальный размер файла: 1Мб';
    } else {
      // Get extension
      $extension = array_reverse(explode('.', $fileName))[0];

      // Generate new name
      $fileName = uniqid('lot-') . '.' . $extension;

      // Move file
      $moved = move_uploaded_file($fileTmpPath, getFilePath($fileName));

      if (!$moved) {
        $errors['image'] = 'Не удалось сохранить файл';
      } else {
        // Update Lot image path
        $lot['image'] = $fileName;
      }
    }
  } else {
    $errors['image'] = 'Выберите файл';
  }

  $errors = array_filter($errors);

  if (empty($errors)) {
    // Formatting data
    foreach ($lot as $key => $value) {
      if (is_numeric($value)) {
        // To integer
        $lot[$key] = intval($value);
      } else if (is_string($value)) {
        // Trim spaces
        $lot[$key] = trim($value);
      }
    }

    // Create lot
    ['error' => $lotCreateError] = $lotCon->create($con, $lot);

    if (isset($lotCreateError['message'])) {
      $errors['global'] = $lotCreateError['message'];
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
