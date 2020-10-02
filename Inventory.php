<?php

namespace \WMG\Inventory;

/**
 * Class that interfaces with the data storage Model
 */
class Inventory {

    //array to map conditions to data store column identifiers
    private $inventoryConditions = [
        'warehouses' => 'warehouse_id',
        'products' => 'product_id',
    ];


    /**
     * Function to query the data store
     * 
     * @param array|null $conditions Conditions to filter the query by.
     *      warehouse_id to filter by warehouse,
     *      product_id to filter by product (sku) 
     * 
     * @return InventoryRecordCollection
     */
    public function getInventory($conditions = []){
        
        //get conditions for select query
        $cond = $this->parseConditions($conditions);

        //search data store using conditions and return the records as an inventoryrecordcollection
        return new InventoryRecordCollection(\App\Models\Inventory::where($cond)->toArray());
    }


    /**
     * Function to updated the data store
     * 
     * @param InventoryRecordCollection $updates a collection of records to update 
     * 
     * @return Collection inventory objects (data records)
     */
    public function updateInventory($updates){
        $keys = implode(",", $updates->getRecordKeys());
        $conditions = \App\Models\Inventory::query("SELECT warehouse_id, product_id FROM warehouse_product WHERE CONCAT(warehouse_id, "|", product_id) IN ($keys)")->toArray();
        
        //after running this function, existing will consist of all the records that can be updated
        //  $updates will consist of all the records that need to be inserted
        $existing = $updates->subset($conditions);

        //update existing records
        \App\Models\Inventory::update($existing->getUpdateQuery());

        //confirm that product_ids and warehouse_ids exist before inserting new inventory information
        $productList = implode(",", $updates->getProducts());
        $warehouseList = implode(",", $updates->getWarehouses());
        $existingWarehouses = \App\Models\Inventory::query("SELECT id FROM warehouse WHERE id IN ($warehouseList)")->toArray();
        $existingProducts = \App\Models\Inventory::query("SELECT sku FROM product WHERE sku IN ($productList)")->toArray();

        //anything left in the updates collection after this call cannot be inserted (warehouse_id not valid)
        $warehouseExists = $updates->subset($existingWarehouses);

        //log out missing warehouses
        \Log('inventory_error')->error('warehouses missing:'. $updates->getWarehouses());

        //anything left in the warehouseExists collection after this call cannot be inserted (product_id not valid)
        $productsExists = $warehouseExists->subset($existingProducts);

        //log out missing products
        \Log('inventory_error')->error('products missing:'. $warehouseExists->getProducts());


        //insert records
        \App\Models\Inventory::insert($existing->getInsertQuery());
    }

    /**
     * Updates inventory from bulk using specified parser
     * 
     * @param mixed $data the data to pass to the parser
     * @param iInventoryParser $inventoryParser a class that implements the iInventoryParse interface
     */
    public function bulkUpdateInventory($data, $inventoryParser) {
        $interfaces = class_implements($inventoryParser);
        if (!isset($interfaces['iInventoryParser'])) {
            throw new \Exception("Bad Parser", 1);
        }

        try {
            $this->updateInventory($inventoryParser::parseData($data));
        } catch (\Throwable $th) {
            //Log errors
        }
    }


    /**
     * A helper function to normalize conditions.  Uses the helper array defined in the class to map conditions
     * 
     * @param array|null $conditions An array of conditions
     * 
     * @return array $cond
     */
    private function parseConditions($conditions){
        $cond = [];
        if (is_array($conditions)) {
            foreach($this->inventoryConditions as $key => $column){
                $val = $conditions[$key] ?? null;
                if ($val) {
                    $cond[$column] = is_array($val) ? $val : [$val];
                }
            }
            
        }
        return $cond;
    }

    /**
     * Get the quantity of a certain sku.  Can specify specific warehouse or array of warehouses
     * 
     * @param string|array $sku The product identifier
     * @param string|array|null $warehouse the warehouse to check for inventory.  if null, searches all warehouses
     * 
     * @return int
     */
    public function getProductQuantity($sku, $warehouse = null) {
        $inventory = $this->getInventory(['products' => $sku, 'warehouses' => null]);
        return $inventory->getCount();
    }


}