<?php 

// app/i18n/mageplaza

$lang = "it_IT";

$row = 1;
$arrMatchedData  = $arrUnMatchedData = array();      
if (($handle = fopen("app/i18n/mageplaza/".$lang."/".$lang.".csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
        if($row == 1){
        	$row++;
        	continue;	
        }


		if($data[0] == $data[1]){
			$arrMatchedData[] = $data;
        }else{
        	$arrUnMatchedData[] = $data;
        }

		$row++;

    }
    fclose($handle);
}

// create matched csv 
{
	$file = fopen("app/i18n/mageplaza/".$lang."/matched_".$lang.".csv","w");
	foreach ($arrMatchedData as $line)
  	{
  		fputcsv($file,$line);
  	}
	fclose($file);
}

// create unmatched csv
{
	$file = fopen("app/i18n/mageplaza/".$lang."/unmatched_".$lang.".csv","w");
	foreach ($arrUnMatchedData as $line)
  	{
  		fputcsv($file,$line);
  	}
	fclose($file);
}


echo "<br/>Language file changes Updated";
die;