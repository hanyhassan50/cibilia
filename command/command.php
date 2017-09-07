<?php

//echo nl2br(shell_exec('php /var/www/html/magento/ready_holster/bin/magento indexer:reindex'));
 
//echo nl2br(shell_exec('php /var/www/html/magento/cibilia/bin/magento deploy:mode:set developer'));

//echo nl2br(shell_exec('php /var/www/html/magento/cibilia/bin/magento setup:upgrade 2>&1;'));
//echo nl2br(shell_exec('php /var/www/html/magento/cibilia/bin/magento module:enable --clear-static-content Cibilia_Redemption;'));

//echo nl2br(shell_exec('php /var/www/html/magento/cibilia/bin/magento setup:static-content:deploy 2>&1;'));

// echo nl2br(shell_exec('php /var/www/html/magento/ready_holster/bin/magento setup:static-content:deploy 2>&1;'));

// echo nl2br(shell_exec('php /var/www/html/magento/ready_holster/bin/magento theme:uninstall frontend/Magento/ready_holster'));

// echo nl2br(shell_exec('php /var/www/html/magento/ready_holster/bin/magento setup:db:status'));

/* echo nl2br(shell_exec('php /var/www/html/magento/cibilia/bin/magento indexer:reindex'));*/

// echo nl2br(shell_exec('php /var/www/html/magento/ready_holster/bin/magento catalog:images:resize')); 

// echo nl2br(shell_exec('php /var/www/html/magento/ready_holster/bin/magento deploy:mode:show'));

// echo nl2br(shell_exec('php /var/www/html/magento/ready_holster/bin/magento deploy:mode:set developer'));
//echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento cache:flush'));

echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento setup:upgrade'));

//magento module:disable Magento_Weee
 
// if(isset($_REQUEST['command']) && $_REQUEST['command'] != '') { 	 }

//echo nl2br(shell_exec('php /var/www/html/magento/cibilia/bin/magento setup:static-content:deploy 2>&1;'));

echo "executed";

?>

