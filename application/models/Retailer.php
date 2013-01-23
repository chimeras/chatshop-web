<?php

class Application_Model_Retailer extends Application_Model_Db_Row_Retailer
{

    public function getProcessorObject()
    {

        $className = '\\Bigbek\\Api\\Retailers\\'.$this->getProcessor();
        return new \Bigbek\Api\Retailers\Common($this);//$className($this);
    }
}