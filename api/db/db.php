<?php
    /*
     * Файл запуска базы данных через PDO
     */

$host = 'localhost';
$db   = 'short_url';
$user = 'root';
$pass = '';

$tables = 1;

$dsn = "mysql:host=$host;charset=utf8";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$connection = null;

// Создание соединения и исключения
try {
    while (true) {
        include_once('createdb.php');
        $connection = new PDO($dsn, $user, $pass, $opt);
        $result = $connection->query("SHOW DATABASES LIKE '$db';");
        $data = $result->fetch();

        if ($data != false) {
            $sql = "SET @@session.time_zone = '+03:00';";
            $connection->exec($sql);
            $sql = "USE $db";
            $connection->exec($sql);

            $result = $connection->query("SHOW TABLES;");
            if ($result->rowCount() < $tables) {
                createTablesDB($connection);
            }
            break;

        } else {
            createDB($connection, $db);
//            echo "База данных создана!";
            continue;
        }
    }

}
catch(PDOException $e) {
    echo  "<br>" . $e->getMessage();
}