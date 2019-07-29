<?php
include "../init.php";
$db = DB::instance();

if (!isUserAuth()) {
    die;
}

$userId = $_SESSION['id'];
if(!empty($_REQUEST['text'])) {
    $date = date('Y-m-d H:i:s');
    $insert = "INSERT INTO posts (user_id, text, created_at)
                VALUES(:id, :text, '$date')";
    DB::instance()->exec($insert, __FILE__, ['id' => $userId, 'text' => $_REQUEST['text']]);

    $postId = DB::instance()->getLastInsertId();
    $post = DB::instance()->fetchOne("SELECT * FROM posts WHERE id = $postId", __FILE__);
    $user = User::getUsersByIds([$userId])[$userId];

    ob_start();
    include 'postTpl.php';
    $html = ob_get_clean();
    header('content-type: application/json');
    echo json_encode(['html' => $html, 'counter' => 123, 'post_data' => $post]);
}
