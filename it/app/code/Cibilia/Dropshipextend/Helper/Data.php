<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Cibilia\Dropshipextend\Helper;
/**
 * Description of Data
 *
 * @author webclues
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    public function getFormAction(){
        return $this->_getUrl('dropshipextend/index/index');
    }
}
