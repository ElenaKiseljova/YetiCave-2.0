<?= $nav ?? ''; ?>
<section class="rates container">
  <h2>Мои ставки</h2>
  <?php if (isset($bets) && is_array($bets) && !empty($bets)): ?>
    <table class="rates__list">
      <?php foreach ($bets as $key => $bet): ?>
        <?php
        $isWin = (isset($bet['lot_winner_bet_id']) && $bet['id'] === $bet['lot_winner_bet_id']);
        $expired = date_create($bet['lot_expiration_date']) <= date_create();

        $itemClasses = '';

        if ($expired) {
          $itemClasses = 'rates__item--end';
        }

        if ($isWin) {
          $itemClasses = 'rates__item--win';
        }
        ?>
        <tr class="rates__item <?= $itemClasses; ?>">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?= getFilePath(htmlspecialchars($bet['lot_image'])); ?>" width="54" height="40" alt="<?= htmlspecialchars($bet['lot_title']); ?>">
            </div>
            <h3 class="rates__title"><a href="/lot?id=<?= $bet['lot_id']; ?>"><?= htmlspecialchars($bet['lot_title']); ?></a></h3>
          </td>
          <td class="rates__category">
            <?= htmlspecialchars($bet['category_title']); ?>
          </td>
          <td class="rates__timer">
            <?= getTimerHTML(htmlspecialchars($bet['lot_expiration_date']), [], $isWin); ?>
          </td>
          <td class="rates__price">
            <?= formatNum(htmlspecialchars($bet['price'])); ?>
          </td>
          <td class="rates__time">
            <?= diffForHumans(htmlspecialchars($bet['created_at'])); ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php else : ?>
    <p>Нет ставок</p>
  <?php endif; ?>
</section>