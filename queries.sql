INSERT INTO
  categories (slug, title)
VALUES
  ('boards', 'Доски и лыжи'),
  ('attachment', 'Крепления'),
  ('boots', 'Ботинки'),
  ('clothing', 'Одежда'),
  ('tools', 'Инструменты'),
  ('other', 'Разное');

INSERT INTO
  users (name, email, password, contacts)
VALUES
  (
    'Elena K. Junior',
    'elena@k.jun.com',
    'password',
    'My contacts are not for everyone.'
  ),
  (
    'Nelly P. Junior',
    'nelly@p.jun.com',
    'password',
    'My contacts are not for everyone.'
  ),
  (
    'Vincent R. Junior',
    'vincent@r.jun.com',
    'password',
    'My contacts are not for everyone.'
  ),
  (
    'Ola O. Junior',
    'ola@o.jun.com',
    'password',
    'My contacts are not for everyone.'
  ),
  (
    'Lucy J. Junior',
    'lucy@j.jun.com',
    'password',
    'My contacts are not for everyone.'
  ),
  (
    'Piter H. Junior',
    'piter@h.jun.com',
    'password',
    'My contacts are not for everyone.'
  );

INSERT INTO
  lots (
    slug,
    title,
    image,
    description,
    price_start,
    price_step,
    expiration_date,
    user_id,
    winner_id,
    category_id
  )
VALUES
  (
    'hello-world-12',
    'Hello world 12',
    'img/lot-1.jpg',
    'My contacts are not for everyone.',
    15645,
    100,
    '2025-04-11 21:11:00',
    1,
    7,
    1
  ),
  (
    'hello-world-22',
    'Hello world 22',
    'img/lot-2.jpg',
    'My contacts are not for everyone.',
    555645,
    1000,
    '2025-04-09 21:11:00',
    8,
    NULL,
    1
  ),
  (
    'hello-world-32',
    'Hello world 32',
    'img/lot-3.jpg',
    'My contacts are not for everyone.',
    1645,
    10,
    '2025-05-11 21:11:00',
    7,
    NULL,
    1
  ),
  (
    'hello-world-42',
    'Hello world 42',
    'img/lot-4.jpg',
    'My contacts are not for everyone.',
    7777645,
    1000,
    '2025-04-21 11:11:00',
    7,
    11,
    1
  );

INSERT INTO
  bets (price, user_id, lot_id)
VALUES
  (20000, 7, 1),
  (18000, 5, 1),
  (16000, 8, 1),
  (16500, 10, 1),
  (8000000, 11, 4),
  (7800000, 4, 4),
  (7900000, 9, 4);

--- Get all Categories
SELECT
  *
FROM
  categories;

--- Get newest open Lots (title, price_start, price_current, image, category_name)
SELECT
  l.id,
  l.title,
  price_start,
  MAX(b.price) price_current,
  image,
  c.title category_name
FROM
  categories c
  RIGHT JOIN (
    lots l
    LEFT JOIN bets b ON l.id = b.lot_id
  ) ON l.category_id = c.id
WHERE
  (
    winner_id IS NULL
    AND l.expiration_date > CURRENT_TIMESTAMP
  )
GROUP BY
  l.id
ORDER BY
  l.created_at DESC;

--- Get lot by ID (with price_current and category_name)
SELECT
  l.id,
  l.slug,
  l.title,
  price_start,
  MAX(b.price) price_current,
  image,
  expiration_date,
  l.created_at,
  description,
  winner_id,
  l.user_id,
  category_id,
  c.title category_name
FROM
  lots l
  JOIN categories c ON l.category_id = c.id
  JOIN bets b ON l.id = b.lot_id
WHERE
  l.id = 9
GROUP BY
  l.id;

--- Update Lot Title by ID
UPDATE lots
SET
  title = 'This is a nice title for a nice lot.'
WHERE
  id = 9;

--- Get Bets by Lot ID
SELECT
  price,
  l.title lot_title,
  u.name user_name,
  b.created_at
FROM
  bets b
  JOIN lots l ON l.id = b.lot_id
  JOIN users u ON u.id = b.user_id
WHERE
  lot_id = 4
ORDER BY
  b.created_at DESC;
