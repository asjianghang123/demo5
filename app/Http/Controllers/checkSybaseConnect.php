<?php

/**
 * checkSybaseConnect.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
$items = [];
$pdo = new PDO("mysql:host=127.0.0.1;dbname=mongs", "root", "mongs");
$res = $pdo->query("select id from users_group;");
$qr = $res->fetchall(PDO::FETCH_ASSOC);
$group = '';
$array = [];
foreach ($qr as $value) {
    array_push($array, $value['id']);
}

$group = implode(',', $array);
date_default_timezone_set("PRC");
$time = date('YmdHis');
$res = $pdo->query("select * from ftpServerInfo order by type;");
while ($qr = $res->fetch(PDO::FETCH_ASSOC)) {
    $externalAddress = $qr['externalAddress'];
    $userName = $qr['userName'];
    $password = $qr['password'];
    $fileDir = $qr['fileDir'];
    $conn = ftp_connect($externalAddress);
    if ($conn) {
        $ftp_login = @ftp_login($conn, $userName, $password);
        if ($ftp_login) {
            $ftp_rawlist = @ftp_rawlist($conn, $fileDir);
            if ($ftp_rawlist) {
                array_push($items, $qr);
            } else {
                $publisher = 'admin';
                $title = strtoupper($qr['type']) . 'cannot read directories';
                $content = 'type:' . strtoupper($qr['type']) . ' city:' . $qr['city'] . ' externalAddress:' . $qr['externalAddress'];
                $p = $pdo->query("insert into notification (publishTime,publisher,title,content,userGroup) values ('$time', '$publisher', '$title', '$content', '$group');");
            }
        } else {
            $publisher = 'admin';
            $title = strtoupper($qr['type']) . 'cannot login';
            $content = 'type:' . strtoupper($qr['type']) . ' city:' . $qr['city'] . ' externalAddress:' . $qr['externalAddress'];
            $p = $pdo->query("insert into notification (publishTime,publisher,title,content,userGroup) values ('$time', '$publisher', '$title', '$content', '$group');");
        }
    } else {
        $publisher = 'admin';
        $title = strtoupper($qr['type']) . 'cannot connect';
        $content = 'type:' . strtoupper($qr['type']) . ' city:' . $qr['city'] . ' externalAddress:' . $qr['externalAddress'];
        $p = $pdo->query("insert into notification (publishTime,publisher,title,content,userGroup) values ('$time', '$publisher', '$title', '$content', '$group');");
    }//end if
}//end while
