<?php
set_time_limit(0);
echo 'Running csv2db<br>';
echo passthru('php ./csv2db.php');
echo 'Running scan_csv<br>';
echo passthru('php ./scan_csv.php');
echo 'Running gen_ini<br>';
echo passthru('php ./gen_ini.php');
echo 'Running csv2gz<br>';
echo passthru('php ./csv2gz.php');
echo 'Running ini2gz<br>';
echo passthru('php ./ini2gz.php');
echo '<br>--------------END ALL---------------<br>';