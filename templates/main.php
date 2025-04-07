<section class="promo">
  <h2 class="promo__title">Нужен стафф для катки?</h2>
  <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
  <ul class="promo__list">
    <?php foreach ($categories as $key => $category): ?>
      <li class="promo__item promo__item--<?= $key; ?>">
        <a class="promo__link" href="pages/all-lots.html"><?= $category; ?></a>
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
      <li class="lots__item lot">
        <div class="lot__image">
          <img src="<?= htmlspecialchars($lot['image']); ?>" width="350" height="260" alt="<?= htmlspecialchars($lot['title']); ?>">
        </div>
        <div class="lot__info">
          <span class="lot__category"><?= $lot['category']; ?></span>
          <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?= htmlspecialchars($lot['title']); ?></a></h3>
          <div class="lot__state">
            <div class="lot__rate">
              <span class="lot__amount">Стартовая цена</span>
              <span class="lot__cost"><?= format_num(htmlspecialchars($lot['price'])); ?></span>
            </div>

            <?php
            $time = get_time_left(htmlspecialchars($lot['expiration']));
            $hours = $time[0];
            $minutes = $time[1];

            $timer_classes = $hours < 1 ? 'timer--finishing' : '';
            $timer_text = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
            ?>
            <div class="lot__timer timer <?= $timer_classes ?>">
              <?= $timer_text; ?>
            </div>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</section>