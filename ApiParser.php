<?php

namespace \WMG\Inventory\Parsers;
use \WMG\Inventory\InventoryRecordCollection;

/*
JSON Schema from api endpoint
{
    "warehouseName": string,
    "productSku": string,
    "quantity": int
}
*/

/**
 * a parser to read data from an api return an inventorycollection
 */
class ApiParser implements iInventoryParser {
    /**
     * Parses an http endpoint and returns an InventoryRecordCollection
     * 
     * @param string $data an api endpoint url
     * 
     * @return InventoryRecordCollection
     */
    public static function parseData($data) {
        try {
            //use library to get data from api
            try {
                //get data as json
                $json = API::getData();
            } catch (\Throwable $th) {
                throw $th;
            }

            $json = is_string($data) ? json_decode($data, true) : $data;
            if (!is_array($json)) {
                throw new \Exception("Bad Data", 1);
            }

            $mapped = array_map($json, function($r){
                return [
                    'warehouse_id' => $r['warehouseName'],
                    'product_id' => $r['productSku'],
                    'quantity' => $r['quantity'],
                ];
            });
            return new InventoryRecordCollection($mapped);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}