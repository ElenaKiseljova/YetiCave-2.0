<?php
// when installed via composer
require_once 'vendor/autoload.php';

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create('ru_RU');

$categories = [
  'boards' => 'Доски и лыжи',
  'attachment' => 'Крепления',
  'boots' => 'Ботинки',
  'clothing' => 'Одежда',
  'tools' => 'Инструменты',
  'other' => 'Разное',
];

// Modified Cagegories
$categories_array = [];

foreach ($categories as $key => $category) {
  $categories_array[] = [
    'slug' => $key,
    'title' => $category
  ];
}

$lots = [];

foreach (range(0, 20) as $key) {
  $lots[] = [
    'title' => $faker->sentence(mt_rand(3, 6)),
    'category' => $categories_array[mt_rand(0, 5)]['title'],
    'price' => mt_rand(10000, 5000000),
    'image' => 'img/lot-' . mt_rand(1, 6) . '.jpg',
    'expiration' => $faker->dateTimeBetween('-10 days', '+10 days')->format('Y-m-d H:i:s'),
  ];
}
