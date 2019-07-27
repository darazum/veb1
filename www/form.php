<html>
<head>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
</head>

<body>
<form action="index.php" method="post" enctype="multipart/form-data">
    userId: <input name="id" type="text" id="user_id"><br>
    <input name="action" type="hidden" value="register">
    <input type="file" name="photo">
    <input type="submit" value="Send">
    <input type="button" value="Send Ajax" onclick="sendAjax();">
</form>

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