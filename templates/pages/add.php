<?= $nav ?? ''; ?>
<form class="form form--add-lot container <?= isset($errors) && !empty($errors) ? 'form--invalid' : ''; ?>"
  action="/add" method="post" enctype="multipart/form-data">
  <h2>Добавление лота</h2>
  <div class="form__container-two">
    <div class="form__item <?= getFieldErrorClass(isset($errors['title'])); ?>">
      <label for="lot-name">Наименование <sup>*</sup></label>
      <input id="lot-name" type="text" name="title" value="<?= getPostVal('title'); ?>" placeholder="Введите наименование лота" class="<?= getInputErrorClass(isset($errors['title'])); ?>">

      <?php if (isset($errors['title'])): ?>
        <span class="form__error"><?= $errors['title']; ?></span>
      <?php endif; ?>
    </div>
    <div class="form__item <?= getFieldErrorClass(isset($errors['slug'])); ?>">
      <label for="lot-name">Слаг <sup>*</sup></label>
      <input id="lot-name" type="text" name="slug" value="<?= getPostVal('slug'); ?>" placeholder="Введите наименование лота" class="<?= getInputErrorClass(isset($errors['slug'])); ?>">

      <?php if (isset($errors['slug'])): ?>
        <span class="form__error"><?= $errors['slug']; ?></span>
      <?php endif; ?>
    </div>

  </div>
  <div class="form__item form__item--wide  <?= getFieldErrorClass(isset($errors['category_id'])); ?>">
    <label for="category">Категория <sup>*</sup></label>
    <select id="category" name="category_id" class="<?= getInputErrorClass(isset($errors['category_id'])); ?>">
      <option value="0">Выберите категорию</option>
      <?php foreach ($categories as $key => $category): ?>
        <option
          value="<?= $category['id']; ?>"
          <?= getPostVal('category_id') === $category['id'] ? 'selected' : ''; ?>>
          <?= $category['title']; ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php if (isset($errors['category_id'])): ?>
      <span class="form__error"><?= $errors['category_id']; ?></span>
    <?php endif; ?>
  </div>
  <div class="form__item form__item--wide <?= getFieldErrorClass(isset($errors['description'])); ?>">
    <label for="message">Описание <sup>*</sup></label>
    <textarea id="message" name="description" placeholder="Напишите описание лота" class="<?= getInputErrorClass(isset($errors['description'])); ?>">
    <?= getPostVal('description'); ?>
    </textarea>
    <?php if (isset($errors['description'])): ?>
      <span class="form__error"><?= $errors['description']; ?></span>
    <?php endif; ?>
  </div>
  <div class="form__item form__item--file <?= getFieldErrorClass(isset($errors['image'])); ?>">
    <label>Изображение <sup>*</sup></label>
    <div class="form__input-file">
      <img width="300" src="img/new-lot.png" alt="Image">
      <input class="visually-hidden" type="file" id="lot-img" name="image">

      <label for="lot-img">
        Добавить
      </label>
    </div>
    <?php if (isset($errors['image'])): ?>
      <span class="form__error"><?= $errors['image']; ?></span>
    <?php endif; ?>
  </div>
  <div class="form__container-three">
    <div class="form__item form__item--small <?= getFieldErrorClass(isset($errors['price_start'])); ?>">
      <label for="lot-rate">Начальная цена <sup>*</sup></label>
      <input id="lot-rate" type="text" name="price_start" value="<?= getPostVal('price_start'); ?>" placeholder="0" class="<?= getInputErrorClass(isset($errors['price_start'])); ?>">
      <?php if (isset($errors['price_start'])): ?>
        <span class="form__error"><?= $errors['price_start']; ?></span>
      <?php endif; ?>
    </div>
    <div class="form__item form__item--small <?= getFieldErrorClass(isset($errors['price_step'])); ?>">
      <label for="lot-step">Шаг ставки <sup>*</sup></label>
      <input id="lot-step" type="text" name="price_step" value="<?= getPostVal('price_step'); ?>" placeholder="0" class="<?= getInputErrorClass(isset($errors['price_step'])); ?>">
      <?php if (isset($errors['price_step'])): ?>
        <span class="form__error"><?= $errors['price_step']; ?></span>
      <?php endif; ?>
    </div>
    <div class="form__item <?= getFieldErrorClass(isset($errors['expiration_date'])); ?>">
      <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
      <input class="form__input-date" id="lot-date" type="text" name="expiration_date" value="<?= getPostVal('expiration_date'); ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД" class="<?= getInputErrorClass(isset($errors['expiration_date'])); ?>">
      <?php if (isset($errors['expiration_date'])): ?>
        <span class="form__error"><?= $errors['expiration_date']; ?></span>
      <?php endif; ?>
    </div>
  </div>

  <?php if (isset($errors) && !empty($errors)): ?>
    <span class="form__error form__error--bottom"><?= $errors['global'] ?? 'Пожалуйста, исправьте ошибки в форме.'; ?></span>
  <?php endif; ?>

  <button type="submit" class="button">Добавить лот</button>
</form>