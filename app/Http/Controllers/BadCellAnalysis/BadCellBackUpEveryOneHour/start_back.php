<?php
    exec("sh/Start_Backup.sh", $res);
    print_r($res);
    print_r("SUCCESS");
    system("sh/remainderData.sh");
    print_r("SUCCESS");
    exec("sh/insert_table.sh");
    print_r("SUCCESS");