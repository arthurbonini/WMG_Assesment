<?php
namespace \WMG\Inventory;

/**
 * Represents a collection of inventory records
 */
class InventoryRecordCollection {
    private $inventoryRecords = [];
    private $warehouses = [];
    private $products = [];


    public function __construct($records = []){
        $this->addInventoryRecords($records);
    }

    /**
     * Adds inventory records to the collection keyed on warehouse_id|product_id.  If duplicate warehouse_id|product_id are present, the later record will persist
     * 
     * @param array $records an array of data store records
     */
    public function addInventoryRecords($records){
        if (!is_array($records)) {
            return;
        }
        foreach($records as $r){
            $w = $p = '';
            $q = 0;
            $isRecord = false;
            if (is_a($r, 'InventoryRecord')) {
                $w = $r->warehouse_id;
                $p = $r->product_id;
                $isRecord = true;
            } else {
                $w = $r['warehouse_id'];
                $p = $r['product_id'];
                $q = $r['quantity'];
            }
            $this->inventoryRecords[$w . "|" . $p] = $isRecord? $r : new InventoryRecord($w, $p, $q);
            $this->warehouses[$w]++;
            $this->products[$p]++;
        }
    }

    /**
     * returns the sum of all record quantities
     * 
     * @return int
     */
    public function getCount(){
        return array_reduce($this->inventoryRecords, function($carry, $r){
            return $carry + $r->quantity;
        });
    }

    /**
     * returns an array of warehouses
     * 
     * @return array
     */
    public function getWarehouses(){
        return array_keys($this->warehouses);
    }

    /**
     * returns an array of products
     * 
     * @return array
     */
    public function getProducts(){
        return array_keys($this->products);
    }

    /**
     * returns an array of warehouse_id|product_ids
     * 
     * @return array
     */
    public function getRecordKeys(){
        return array_keys($this->inventoryRecords);
    }

    /**
     * removes and returns a subset of records
     *      an array of warehouse_ids|product_ids
     * 
     * @param array $conditions The conditions of the subset(warehouse_id, product_id)
     * 
     * @return InventoryRecordCollection
     */
    public function subset($conditions){
        $subset = [];

        //check for both warehouse_id && product_id - or just one or the other
        foreach ($conditions as $c) {
            if (isset($c['warehouse_id']) && $c['product_id']) {
                $key = $c['warehouse_id'] . '|' . $c['product_id'];
                if (isset($this->inventoryRecords[$key])) {
                    $subset[] = $this->inventoryRecords[$key];
                }
            } else if(isset($c['warehouse_id'])) {
                $w = $c['warehouse_id'];
                $filter = array_filter($this->inventoryRecords, function($r) use ($w){
                    return strpos($r, $w) === 0;
                }, ARRAY_FILTER_USE_KEY);
                $subset = array_merge($subset, $filter);
            } else if(isset($c['product_id'])) {
                $p = $c['product_id'];
                $filter = array_filter($this->inventoryRecords, function($r) use ($p){
                    $test = explode("|", $p)[1];
                    return $r === $p;
                }, ARRAY_FILTER_USE_KEY);
                $subset = array_merge($subset, $filter);
            }
        }

        foreach($subset as $key => $r){
            unset($this->inventoryRecords[$key]);
        }

        return new self($subset);
    }


    /**
     * generates an update query
     */
    public function getUpdateQuery(){
        //loop through records and return update query
    }

    /**
     * generates an insert query
     */
    public function getInsertQuery(){
        //loop through records and return insert query
    }
}