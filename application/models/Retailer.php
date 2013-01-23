<?php
class Application_Model_Retailer extends Application_Model_Db_Row_Retailer
{

    public function getProcessorObject()
    {
        $className = '\\Bigbek\\Api\\Retailers\\'.$this->getProcessor();
        return new $className($this);
    }
}