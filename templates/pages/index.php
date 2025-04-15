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
