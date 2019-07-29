<?php
include "../init.php";
$db = DB::instance();

if (!isUserAuth()) {
    redirect('index.php');
}

$select = "SELECT * FROM posts ORDER BY id DESC LIMIT 20";
$posts = DB::instance()->fetchAll($select, __FILE__);
if ($posts) {
    $userIds = array_column($posts, 'user_id');
    $users = User::getUsersByIds($userIds);
}
?>

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>

<div class="posts">
    <? foreach ($posts as $post):?>
        <? $user = $users[$post['user_id']]; ?>
        <? include 'postTpl.php'; ?>
    <? endforeach;?>
</div>

Оставить комментарий:<br>
text: <input name="text" type="text" id="text"><br>
<input type="submit" value="Отправить" onclick="sendPost();">


<?=DB::instance()->getLog();?>

<script>
    function sendPost() {
        var text = $('#text').val();
        $.get('AjaxSendPost.php', {text: text}, function(resp){
            var curContent = $('.posts').html();
            $('.posts').html(resp.html + curContent);
        });
    }
</script>