<?php
/**
 * Что хотим сегодня получить:
 *
 * 1. зарегистрировать пользователя по емейлу и паролю
 * 2. провалидировать его емейл, имя
 * 3. добавить пользователя в базу
 * 4. залогинить пользователя
 */
include "../init.php";
$db = DB::instance();

//$data = $db->fetchAll('SELECT * FROM users WHERE id = :id', __FILE__, ['id' => 2]);
//var_dump($data);

include "form.php";

var_dump($_FILES);
var_dump($_REQUEST);

echo $db->getLog();