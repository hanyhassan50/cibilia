<?php 

// echo __DIR__;
//echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento setup:upgrade 2>&1;'));

//echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento setup:static-content:deploy 2>&1;'));

//echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:enable --clear-static-content Cibilia_Redemption;'));

/*echo nl2br(shell_exec('php /web/bin/magento theme:uninstall frontend/Magento/ready_holster'));*/

/*echo nl2br(shell_exec('php /web/bin/magento setup:db:status'));*/

//echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento indexer:reindex')); 

/*echo nl2br(shell_exec('php /web/bin/magento deploy:mode:show'));*/

//echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento deploy:mode:set developer'));

/*echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:disable Cibilia_Commission'));
echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:disable Cibilia_Idproofs'));
echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:disable Cibilia_Redemption'));
echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:disable Cibilia_Summary'));*/


/*echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:enable Cibilia_Commission'));
echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:enable Cibilia_Idproofs'));
echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:enable Cibilia_Redemption'));
echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:enable Cibilia_Summary'));*/


echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento cache:flush'));
//echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento module:status'));

//echo nl2br(shell_exec('php /home/cibiliac/cibilia.com/html/tst/bin/magento cron:run [--group="cibilian_commission_group"]'));

echo "executed";
?>
