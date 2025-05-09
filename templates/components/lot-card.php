<li class="lots__item lot">
  <div class="lot__image">
    <img src="<?= getFilePath(htmlspecialchars($lot['image'])); ?>" width="350" height="260" alt="<?= htmlspecialchars($lot['title']); ?>">
  </div>
  <div class="lot__info">
    <span class="lot__category"><?= $lot['category_name']; ?></span>
    <h3 class="lot__title"><a class="text-link" href="/lot?id=<?= $lot['id']; ?>"><?= htmlspecialchars($lot['title']); ?></a></h3>
    <div class="lot__state">
      <?php
      $priceCurrent = $lot['price_current'];
      $price = isset($priceCurrent) ? $priceCurrent : $lot['price_start'];

      $betsCount = $lot['bets_cnt'] ?? 0;
      $priceTitle = $betsCount ? ($betsCount . ' ' . getNounPluralForm($betsCount, 'ставка', 'ставки', 'ставок')) : "Стартовая цена";
      ?>
      <div class="lot__rate">
        <span class="lot__amount"><?= $priceTitle; ?></span>
        <span class="lot__cost"><?= formatNum(htmlspecialchars($price)); ?></span>
      </div>

      <?= getTimerHTML(htmlspecialchars($lot['expiration_date']), ['lot__timer']); ?>
    </div>
  </div>
</li>
