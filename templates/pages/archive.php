<?= $nav ?? ''; ?>
<div class="container">
  <section class="lots">
    <h2><?= $title ?? 'Список лотов'; ?> <?= isset($titleInQuote) ? "«<span>$titleInQuote</span>»" : ''; ?></h2>

    <?php if (isset($lots) && is_array($lots) && !empty($lots)): ?>
      <ul class="lots__list">
        <?php foreach ($lots as $key => $lot): ?>
          <?= includeTemplate('components/lot-card.php', ['lot' => $lot]); ?>
        <?php endforeach; ?>
      </ul>
    <?php else : ?>
      <p>Ничего не найдено по вашему запросу</p>
    <?php endif; ?>
  </section>

  <?php if ($pagesCount > 1): ?>
    <ul class="pagination-list">
      <li class="pagination-item pagination-item-prev">
        <?php if ($curPage > 1): ?>
          <a href="/search<?= withQuery(['page' => $curPage - 1]); ?>">Назад</a>
        <?php endif; ?>
      </li>

      <?php foreach ($pages as $page): ?>
        <li class="pagination-item <?= $curPage == $page ? 'pagination-item-active' : ''; ?>">
          <a href="/search<?= withQuery(['page' => $page]); ?>">
            <?= $page; ?>
          </a>
        </li>
      <?php endforeach; ?>

      <li class="pagination-item pagination-item-next">
        <?php if ($curPage < $pagesCount): ?>
          <a href="/search<?= withQuery(['page' => $curPage + 1]); ?>">Вперед</a>
        <?php endif; ?>
      </li>
    </ul>
  <?php endif; ?>

</div>
