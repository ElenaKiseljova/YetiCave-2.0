<?= $nav ?? ''; ?>
<form class="form container <?= isset($errors) && !empty($errors) ? 'form--invalid' : ''; ?>"
  action="/register" method="post" autocomplete="off">
  <h2>Регистрация нового аккаунта</h2>
  <div class="form__item <?= getFieldErrorClass(isset($errors['email'])); ?>">
    <label for="email">E-mail <sup>*</sup></label>
    <input id="email" type="text" name="email" value="<?= getPostVal('email'); ?>" placeholder="Введите e-mail">
    <?php if (isset($errors['email'])): ?>
      <span class="form__error"><?= $errors['email']; ?></span>
    <?php endif; ?>
  </div>
  <div class="form__item <?= getFieldErrorClass(isset($errors['password'])); ?>">
    <label for="password">Пароль <sup>*</sup></label>
    <input id="password" type="password" name="password" placeholder="Введите пароль">
    <?php if (isset($errors['password'])): ?>
      <span class="form__error"><?= $errors['password']; ?></span>
    <?php endif; ?>
  </div>
  <div class="form__item <?= getFieldErrorClass(isset($errors['name'])); ?>">
    <label for="name">Имя <sup>*</sup></label>
    <input id="name" type="text" name="name" value="<?= getPostVal('name'); ?>" placeholder="Введите имя">
    <?php if (isset($errors['name'])): ?>
      <span class="form__error"><?= $errors['name']; ?></span>
    <?php endif; ?>
  </div>
  <div class="form__item <?= getFieldErrorClass(isset($errors['contacts'])); ?>">
    <label for="message">Контактные данные <sup>*</sup></label>
    <textarea id="message" name="contacts" placeholder="Напишите как с вами связаться"><?= getPostVal('contacts'); ?></textarea>
    <?php if (isset($errors['contacts'])): ?>
      <span class="form__error"><?= $errors['contacts']; ?></span>
    <?php endif; ?>
  </div>

  <?php if (isset($errors) && !empty($errors)): ?>
    <span class="form__error form__error--bottom"><?= $errors['global'] ?? 'Пожалуйста, исправьте ошибки в форме.'; ?></span>
  <?php endif; ?>

  <button type="submit" class="button">Зарегистрироваться</button>
  <a class="text-link" href="/login">Уже есть аккаунт</a>
</form>