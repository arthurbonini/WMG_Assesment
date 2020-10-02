<?php

namespace \WMG\Inventory\Parsers;
use \WMG\Inventory\InventoryRecordCollection;

/*
CSV Schema
- warehouseName
- productSku
- quantity
*/

/**
 * a parser to read a csv and return an inventorycollection
 */
class CsvParser implements iInventoryParser {
    /**
     * Parses a file handle and returns an InventoryRecordCollection
     * 
     * @param string $data a csv filehandle
     * 
     * @return InventoryRecordCollection
     */
    public static function parseData($data) {
        try {
            //read the file to an array and return it as an inventoryCollection
            $array = str_getcsv($data);
            $mapped = array_map($array, function($r){
                return [
                    'warehouse_id' => $r[0],
                    'product_id' => $r[1],
                    'quantity' => $r[2],
                ];
            });
            return new InventoryRecordCollection($mapped);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}