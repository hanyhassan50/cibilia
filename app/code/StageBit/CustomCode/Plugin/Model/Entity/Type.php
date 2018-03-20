<?php
namespace StageBit\CustomCode\Plugin\Model\Entity;

class Type
{
    public function afterGetAttributeCollection(
        \Magento\Eav\Model\Entity\Type $subject,
        $result
    )
    {
        foreach ($result as $key => $value) {

            if (strpos($value->getAttributeCode(), 'cibilian') !== false) {

                //echo get_class($collection);
                $result->removeItemByKey($key);
                //$proceed();
            }

        }
        return $result;
    }
}