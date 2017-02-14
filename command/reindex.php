<?php
//echo nl2br(shell_exec('sh reindex.sh'));
passthru("/bin/bash reindex.sh");
echo "executed";
?>