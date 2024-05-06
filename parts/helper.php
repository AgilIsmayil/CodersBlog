<?php

function post($key)
{
    return trim($_POST[$key] ?? '') ?: null;
}

$domain = 'http://localhost/Coders/project';
