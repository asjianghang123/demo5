<?php
    function test() {
        try{
            $pdo = new PDO("mysql:host=127.0.0.1;dbname=mongs", "root", "mongs", array(PDO::ATTR_TIMEOUT => 3));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch (PDOException $e) {
            print_r ("连接超时");
        }
        
    }