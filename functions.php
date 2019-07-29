<?php
function redirect(string $url)
{
    header('Location: ' . $url);
}

function isUserAuth()
{
    return !empty($_SESSION['id']);
}