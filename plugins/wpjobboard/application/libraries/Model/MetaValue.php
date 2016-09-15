<?php

/**
 * Description of Meta Value
 *
 * @author Grzegorz Winiarski
 * @package WPJB.Model
 */

class Wpjb_Model_MetaValue extends Daq_Db_OrmAbstract
{
    protected $_name = "wpjb_meta_value";

    protected function _init()
    {
    }
    
    public static function import($meta)
    {
        $query = new Daq_Db_Query;
        $query->from("Wpjb_Model_Meta t");
        $query->where("object_id = ?", $meta->object_id);
        $query->where("object = ?", $meta->object);
        $query->limit(1);
        
        $result = $query->execute();
        
        if(!isset($result[0])) {
            throw new Exception("Meta for '{$meta->object}' with ID {$meta->object_id} does not exist.");
        }
        
        $vlist = $result[0]->getValues();
        
        if($meta->values) {
            $varr = $meta->values;
        } else {
            $varr = (array)$meta->value;
        }
        
        $c = count($varr);

        for($i=0; $i<$c; $i++) {
            if(isset($vlist[$i])) {
                $vlist[$i]->value = $varr[$i];
                $vlist[$i]->save();
            } else {
                $mv = new Wpjb_Model_MetaValue;
                $mv->meta_id = $result[0]->getId();
                $mv->object_id = $meta->object_id;
                $mv->value = $varr[$i];
                $mv->save();
            }
        }
    }
}

?>