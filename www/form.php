<html>
<head>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
</head>

<?
$name = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';
$email = $_POST['email'] ?? '';
?>

<body>
Зарегистрироваться:<br>
<form action="index.php" method="post" enctype="multipart/form-data">
    name: <input name="login" type="text" value="<?=$name;?>"><br>
    password: <input name="password" type="password" value="<?=$password;?>"><br>
    email: <input name="email" type="text" value="<?=$email;?>"><br>
    <input name="action" type="hidden" value="<?=ACTION_REGISTER;?>">
    avatar: <input type="file" name="photo"><br>
    <input type="submit" value="Зарегистрироваться">
</form>

<br>
<br>
<br>

Войти: <br>
<form action="index.php" method="post">
    email: <input name="email" type="text" value="<?=$email;?>"><br>
    password: <input name="password" type="password"><br>
    <input name="action" type="hidden" value="<?=ACTION_LOGIN;?>">
    <input type="submit" value="Войти">
</form>

<? if(!empty($error)): ?>
    <div style="color: red"><?=$error;?></div>
<? endif;?>

<div id="result">

</div>
</body>
</html>

<script>
    function sendAjax() {
        var userId = $('#user_id').val();
        $.get('index.php', {id: userId, ajax: 1}, function(resp){
            console.log(resp);
            $('#result').html(resp.id + ': ' + resp.name);
        });
    }
</script>