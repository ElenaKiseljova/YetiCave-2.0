<?= $nav ?? ''; ?>
<form class="form container <?= isset($errors) && !empty($errors) ? 'form--invalid' : ''; ?>"
  action="/login" method="post">
  <div class="form__item <?= getFieldErrorClass(isset($errors['email'])); ?>">
    <label for="email">E-mail <sup>*</sup></label>
    <input id="email" type="text" name="email" value="<?= getPostVal('email'); ?>" placeholder="Введите e-mail">
    <?php if (isset($errors['email'])): ?>
      <span class="form__error"><?= $errors['email']; ?></span>
    <?php endif; ?>
  </div>
  <div class="form__item form__item--last  <?= getFieldErrorClass(isset($errors['password'])); ?>">
    <label for="password">Пароль <sup>*</sup></label>
    <input id="password" type="password" name="password" placeholder="Введите пароль">
    <?php if (isset($errors['password'])): ?>
      <span class="form__error"><?= $errors['password']; ?></span>
    <?php endif; ?>
  </div>

  <?php if (isset($errors) && !empty($errors)): ?>
    <span class="form__error form__error--bottom"><?= $errors['global'] ?? 'Пожалуйста, исправьте ошибки в форме.'; ?></span>
  <?php endif; ?>

  <button type="submit" class="button">Войти</button>
</form>