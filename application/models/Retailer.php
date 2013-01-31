<?php

class Application_Model_Retailer extends Application_Model_Db_Row_Retailer
{

    public function getProcessorObject()
    {

        $className = '\\Bigbek\\Api\\Retailers\\'.$this->getProcessor();
        $class = null;
        eval('$class = new '.$className .';');

        $class->setRetailer($this);
        return $class;
    }


    public function getCategoryIds()
    {
        return(explode('|', $this->getCategoryId()));
    }
}