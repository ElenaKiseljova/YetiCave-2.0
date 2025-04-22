<section class="promo">
  <h2 class="promo__title">Нужен стафф для катки?</h2>
  <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
  <ul class="promo__list">
    <?php foreach ($categories as $key => $category): ?>
      <li class="promo__item promo__item--<?= $category['slug']; ?>">
        <a class="promo__link" href="/lots?category_id=<?= $category['id']; ?>"><?= $category['title']; ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
<section class="lots">
  <div class="lots__header">
    <h2>Открытые лоты</h2>
  </div>
  <ul class="lots__list">
    <?php foreach ($lots as $key => $lot): ?>
      <?= includeTemplate('components/lot-card.php', ['lot' => $lot]); ?>
    <?php endforeach; ?>
  </ul>
</section>
