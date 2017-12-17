<?php

/*
 * Database connection.
 */
$pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', HOST, PORT, DATABASE, CHARSET)
        , USERNAME
        , PASSWORD
        , [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => FALSE,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
