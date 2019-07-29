<?php
/**
 * Что хотим сегодня получить:
 *
 * 1. зарегистрировать пользователя по емейлу и паролю с аватаркой
 * 2. провалидировать его емейл, имя
 * 3. добавить пользователя в базу
 * 4. залогинить пользователя
 * 5. если успеем - добавление постов аяксом
 */
include "../init.php";
$db = DB::instance();

if (isUserAuth()) {
    redirect('blog.php');
}

$password = $_POST['password'] ?? '';
$email = $_POST['email'] ?? '';
$action = $_POST['action'] ?? false;

$error = '';

switch ($action) {
    case ACTION_REGISTER:
        $name = $_POST['login'] ?? '';
        $user = new User($name, $password, $email);
        if (!$user->checkRegister($error)) {

        } else {
            $user->addToDb();
            if (!empty($_FILES['photo'])) {
                $photoData = file_get_contents($_FILES['photo']['tmp_name']);
                if ($photoData) {
                    file_put_contents('images/' . $user->getId() . '.jpg', $photoData);
                }
            }
            echo 'Успешно зарегистрирован пользователь с id = ' . $user->getId();
        }
        break;

    case ACTION_LOGIN:
        $user = User::getByEmail($email);
        if (!$user) {
            $error = 'Пользователя не существует';
        } elseif($user->getPassword() != User::getPasswordHash($password)) {
            $error = 'Неверный пароль';
        } else {
            $_SESSION['id'] = $user->getId();
            redirect('blog.php/?from=register');
        }
        break;
}



include "form.php";


echo $db->getLog();