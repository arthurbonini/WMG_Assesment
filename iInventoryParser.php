<?php

namespace \WMG\Inventory\Parsers;

interface iInventoryParser {

    /**
     * function to parse data and return the results as a InventoryRecordCollection
     * 
     * @param mixed $data the data source to be parsed
     * 
     * @return InventoryRecordCollection
     */
    public static function parseData($data);
}