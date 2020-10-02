<?php
namespace \WMG\Inventory;

/**
 * A class to hold inventory record information (Model)
 */
class InventoryRecord {
    public $warehouse_id;
    public $product_id;
    public $quantity;

    public function __construct($warehouse_id, $product_id, $quantity){
        $this->warehouse_id = $warehouse_id;
        $this->product_id = $product_id;
        $this->quantity = $quantity;
    }
}