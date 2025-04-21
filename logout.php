<?php
session_start();

// Delete User session
if (isset($_SESSION['user'])) {
  unset($_SESSION['user']);

  header('Location: /');
}
