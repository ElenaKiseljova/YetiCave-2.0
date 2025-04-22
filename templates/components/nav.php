<nav class="nav">
  <ul class="nav__list container">
    <?php foreach ($categories as $key => $category): ?>
      <li class="nav__item <?= isset($_GET['category_id']) && $category['id'] == htmlspecialchars(trim($_GET['category_id'])) ? 'nav__item--current' : ''; ?>">
        <a href="/lots?category_id=<?= $category['id']; ?>"><?= $category['title']; ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
