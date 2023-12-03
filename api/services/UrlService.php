<?php


class UrlService {
    private $connection;
    private $db;

    function __construct($connection, $db) {
        $this->connection = $connection;
        $this->db = $db;
    }

    function setNewUrl($data) {
        $result = [];

        $urlValidate = isset($data['url']) && filter_var($data['url'], FILTER_VALIDATE_URL);
        $shortUrlValidate = isset($data['shortUrl']) && strlen($data['shortUrl']) > 0;

        if ($urlValidate) {
            $url = $data['url'];
            $where = " url = '$url' ";
            if ($shortUrlValidate) {
                $shortUrl = $data['shortUrl'];
                $where .= $shortUrlValidate ? " OR short_url = '$shortUrl' " : "";
            }

            $res = $this->connection->query("SELECT id FROM $this->db.urls WHERE " . $where);
            if (!($res->rowCount() > 0)) {
                $count = $this->getCountURLs();
                $shortUrl = $shortUrlValidate ? $shortUrl : $this->generateShortURL($count);

                $sql = "INSERT INTO $this->db.urls (url, short_url)
                            VALUES ('$url', '$shortUrl');";
                $res = $this->connection->exec($sql);
                if ($res > 0) {
                    $result['shortUrl'] = $this->getFullURL($shortUrl);
                } else {
                    $result['error'] = true;
                    $result['message'] = "Ошибка базы данных, не удалось сгенерировать ссылку.";
                }
            } else {
                $result['error'] = true;
                $result['message'] = "Данная ссылка уже существует.";
            }
        } else {
            $result['error'] = true;
            $result['message'] = "Данная ссылка не прошла валидацию.";
        }
        return $result;
    }

    function getUrlList() {
        $result = [];

        $res = $this->connection->query("SELECT id, url, short_url, count FROM $this->db.urls ");

        if ($res != false) {
            $list = [];
            while ($row = $res->fetch()) {
                $row['short_url'] = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/s/' . $row['short_url'];
                array_push($list, $row);
            }
            $result['listUrls'] = $list;
        } else {
            $result['error'] = true;
            $result['message'] = "Данных нету.";
        }
        return $result;
    }

    function getURL($short_url) {
        $result = false;
        $res = $this->connection->query("SELECT id, url, count FROM $this->db.urls WHERE short_url='$short_url' ");
        if ($res != false) {
            $row = $res->fetch();
            $this->counterURL($row);
            $result = $row['url'];
        }
        return $result;
    }

    function counterURL($data) {
        try {
            $id = $data['id'];
            $count = $data['count'] + 1;
            $sql = "UPDATE $this->db.urls
                                SET count = $count
                                WHERE id = $id";
            $this->connection->exec($sql);
        } catch(PDOException $e) {
            $result['error'] = true;
            $result['message'] = "Не удалось изменить запись";
        }
    }

    function updateUrl($data) {
        $result = [];

        $urlValidate = isset($data['url']) && filter_var($data['url'], FILTER_VALIDATE_URL);
        $shortUrlValidate = isset($data['shortUrl']) && strlen($data['shortUrl']) > 0;

        if ($urlValidate && $shortUrlValidate && isset($data['id'])) {
            $id = $data['id'];
            $url = $data['url'];
            $shortUrl = $data['shortUrl'];
            $where = " id != $id AND (url = '$url' OR short_url = '$shortUrl')";

            $res = $this->connection->query("SELECT id FROM $this->db.urls WHERE " . $where);
            if (!($res->rowCount() > 0)) {

                try {
                    $sql = "UPDATE $this->db.urls
                                SET url = '$url', 
                                    short_url = '$shortUrl'
                                WHERE id = $id";
                    $res = $this->connection->exec($sql);
                    if ($res > 0) {
                        $result['status'] = true;
                    }
                } catch(PDOException $e) {
                    $result['error'] = true;
                    $result['message'] = "Не удалось изменить запись";
                }
            } else {
                $result['error'] = true;
                $result['message'] = "Данная ссылка уже существует.";
            }
        } else {
            $result['error'] = true;
            $result['message'] = "Данная ссылка не прошла валидацию.";
        }
        return $result;
    }

    function deleteUrl($id) {
        $result = [];
        try {
            $sql = "DELETE FROM $this->db.urls
                        WHERE id = $id;";
            $this->connection->exec($sql);
            $result['result'] = true;
        } catch(PDOException $e) {
            $result['error'] = true;
            $result['message'] = "Не удалось удалить запись";
        }
        return $result;
    }



    private function getCountURLs() {
        $result = $this->connection->query("SELECT COUNT(*) AS count FROM $this->db.urls");
        $row = $result->fetch();
        return $row['count'];
    }

    private function getFullURL($short) {
        return ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . $short;
    }

    private function generateShortURL($value) {
        $b64 = base64_encode(mb_chr($value, 'UTF-8'));
        return trim($b64, '/\=*$/gm');
    }
}