<?php

/*
 * Создает базу данных
 * @param object $connect - соединение с базой через PDO
 * @param string $name - имя создаваемой БД
 */
function createDB($connect, $name) {
    $sql = "CREATE DATABASE $name CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
    $connect->exec($sql);

    $sql = "USE $name";
    $connect->exec($sql);

    createTablesDB($connect, $name);
}

function checkTable($conn, $name) {
    $result = $conn->query("SHOW TABLES LIKE '$name';");
    return ($result->rowCount() > 0) ? true : false;
}

function createTablesDB($connect) {

    if ( !checkTable($connect, 'urls') ) {
        $sql = "CREATE TABLE urls (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            url VARCHAR(2048) NOT NULL,
            short_url VARCHAR(255) NOT NULL,
            count BIGINT NOT NULL DEFAULT '0'
        ) ENGINE=InnoDB;";
        $connect->exec($sql);
    }

}