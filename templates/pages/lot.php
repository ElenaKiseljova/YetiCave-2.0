<?php
// Category
$category = isset($lot['category_name']) ? $lot['category_name'] : '-';

// Description
$description = isset($lot['description']) ? htmlspecialchars($lot['description']) : '-';

// Timer
$timer = getTimerHTML(htmlspecialchars($lot['expiration_date']), ['lot-item__timer']);

// Image
$image = getFilePath(htmlspecialchars($lot['image']));
?>

<?= $nav ?? ''; ?>
<section class="lot-item container">
  <h2><?= $title; ?></h2>
  <div class="lot-item__content">
    <div class="lot-item__left">
      <div class="lot-item__image">
        <img src="<?= $image; ?>" width="730" height="548" alt="Сноуборд">
      </div>
      <p class="lot-item__category">Категория: <span><?= $category; ?></span></p>
      <p class="lot-item__description"><?= $description; ?></p>
    </div>
    <div class="lot-item__right">
      <div class="lot-item__state">
        <?= $timer; ?>
        <div class="lot-item__cost-state">
          <div class="lot-item__rate">
            <span class="lot-item__amount">Текущая цена</span>
            <span class="lot-item__cost"><?= formatNum(htmlspecialchars($priceCurrent)); ?></span>
          </div>
          <div class="lot-item__min-cost">
            Мин. ставка <span><?= formatNum(htmlspecialchars($priceStep)); ?></span>
          </div>
        </div>
        <?php if ($isAuth && $userId !== $lot['user_id']): ?>
          <form class="lot-item__form" action="/lot?id=<?= $lot['id']; ?>" method="post" autocomplete="off">
            <p class="lot-item__form-item form__item  <?= getFieldErrorClass(isset($errors['price'])); ?>">
              <label for="cost">Ваша ставка</label>
              <input id="cost" type="text" name="price" value="<?= getPostVal('price'); ?>" placeholder="12 000">

              <?php if (isset($errors['price'])): ?>
                <span class="form__error"><?= $errors['price']; ?></span>
              <?php endif; ?>
            </p>
            <button type="submit" class="button">Сделать ставку</button>
          </form>
        <?php endif; ?>

      </div>
      <div class="history">
        <?php if (isset($bets) && !empty($bets)): ?>
          <h3>История ставок (<span><?= count($bets); ?></span>)</h3>
          <table class="history__list">
            <?php foreach ($bets as $key => $bet): ?>
              <tr class="history__item">
                <td class="history__name"><?= htmlspecialchars($bet['user_name']); ?></td>
                <td class="history__price"><?= formatNum(htmlspecialchars($bet['price'])); ?></td>
                <td class="history__time"><?= diffForHumans(htmlspecialchars($bet['created_at'])); ?></td>
              </tr>
            <?php endforeach; ?>
            <!-- <tr class="history__item">
            <td class="history__name">Иван</td>
            <td class="history__price">10 999 р</td>
            <td class="history__time">5 минут назад</td>
          </tr> -->
          </table>
        <?php else: ?>
          <h3>Нет ставок</h3>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
