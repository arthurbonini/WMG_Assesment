<?php

use \WMG\Inventory\Parsers\CsvParser;
use \WMG\Inventory\Parsers\ApiParser;
use \WMG\Inventory\Inventory;

class Cron {
    public function execute() {
        $inventory = new Inventory();

        $files = Storage('csv')::all();

        foreach ($files as $f) {
            $inventory->bulkUpdateInventory($f, CsvParser::class);
        }
        
        $endpoints = [
            '/api/warehouse1/inventory',
            '/api/warehouse2/inventory',
            '/api/warehouse3/inventory',
        ];

        foreach ($endpoints as $e) {
            $inventory->bulkUpdateInventory($e, ApiParser::class);
        }
    }
}