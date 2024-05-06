<?php

function sqlConnetion()
{
    try {
        return new PDO("mysql:host=localhost;dbname=project", "root", "");
    } catch (PDOException $error) {
        print $error->getMessage();
    }
}
