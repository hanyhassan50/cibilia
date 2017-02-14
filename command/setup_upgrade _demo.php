<?php 

// echo __DIR__;
//echo nl2br(shell_exec('php /home/sankhala/public_html/demo/cibilia/bin/magento setup:upgrade'));

echo nl2br(shell_exec('php /home/sankhala/public_html/demo/cibilia/bin/magento setup:static-content:deploy 2>&1;'));

/*echo nl2br(shell_exec('php /home/sankhala/public_html/demo/cibilia/bin/magento theme:uninstall frontend/Magento/ready_holster'));*/

/*echo nl2br(shell_exec('php /home/sankhala/public_html/demo/cibilia/bin/magento setup:db:status'));*/

//echo nl2br(shell_exec('php /home/sankhala/public_html/demo/cibilia/bin/magento indexer:reindex')); 

/*echo nl2br(shell_exec('php /web/bin/magento deploy:mode:show'));*/

//echo nl2br(shell_exec('php /var/www/tst.cibilia.com/web/bin/magento deploy:mode:set developer'));


//echo nl2br(shell_exec('php /home/sankhala/public_html/demo/cibilia/bin/magento cache:flush'));


echo "executed";
?>
