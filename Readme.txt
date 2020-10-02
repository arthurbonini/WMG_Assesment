Arthur Bonini
Warner Software Engineer Application - Technical Assesment

+------------------------------------------------------------------------------------------------------------------------------
| Thanks for taking the time to finish up the Multiple Location Ivnentory coding test.  
| Below are what we have from the interview.  It's your chocie to continue to work on that or rewrite it in different way.
|
| Requirements
| * You can write this module in any framework of your choice
| * This doesn't need to be a fully working module
| * Your code need to follow SOLID design principles
| * Fulfill the features of the following - Where should logic sits for each feature?
|       * Get quantity of a given sku at a specific warehouse
|       * Get quantity of a given sku at all warehouses
|       * Update quantity of a given sku at a specific warehouse
|       * Stock import. It should support CSV file or API
|  * Consider our feedbacks from the interview when building this
|  * Provide a short summary why you write it in the way that you did
|  * Describe any improvement that you can think of
|  * Describe the downside of your implmentation
| 
+------------------------------------------------------------------------------------------------------------------------------



This project consists of 3 Inventory classes, 3 parser classes, and the cron class
Here is a short description of the files included

The Inventory Classes:
    InventoryRecord:  represents a record in the database consisting of a warehouse_id, product_id, and quantity.
    InventoryRecordCollection: represents a collection of InventoryRecords.  Its convenient to have a class to contain records because it allows me to make specific functions to either get information about the records or apply some transformations
    Inventory: this class interfaces with the database (selecting, inserting, updating, etc)

The Parser Classes:
    iInventoryParser: an interface that requires a parseData function which returns an InventoryRecordsCollection object
    CsvParser: implements iInventoryParser - takes in a csv file handle, parses the data, creates an InventoryRecordCollection and returns it
    ApiParser: implements iInventoryParser - takes in a api endpoint url, fetches the data, maps the values to create an InventoryRecordCollection and returns it

The Cron Class:
    Cron: schedules the bulk imports via the Parser Claseses

Other Files:
    Readme.txt: this file
    interview code.txt: the file that we started during the interview



Questions:

* Get quantity of a given sku at a specific warehouse
    $sku = '12345';
    $warehouse = 'EAST_1';
    $inventory = new Inventory;
    $inventory->getProductQuantity(['warehouses' => $warehouse, 'products' => $sku]);


* Get quantity of a given sku at all warehouses
    $sku = '12345';
    $inventory = new Inventory;
    $inventory->getProductQuantity(['products' => $sku]);

* Update quantity of a given sku at a specific warehouse
    $sku = '12345';
    $warehouse = 'EAST_1';
    $quantity = 10;
    $record = new InventoryRecord($warehouse, $sku, $quantity);
    $collection = new InventoryRecordCollection([$record]);
    $inventory = new Inventory;
    $inventory->updateInventory($collection);

* Stock import. It should support CSV file or API
    \\see Cron Class

* Provide a short summary why you write it in the way that you did
    I chose to write classes for the Record and RecordCollection to simplify and normalize the data being passed to the updateInventory functions.  It is also very convenient to have a class that contains all the records so that I can write specific functions to work with the data
    For the bulkUpdateInventory function, I created the function so that the data and parser could be passed in directly.  This way new parser can be written for different bulk import scenarios.  To accomplish this I created an interface that requires the parseData function.  This function returns the data in an object known to the updateFunction already, which normalizes the process across all parsers

* Describe any improvement that you can think of
    I could make the collection class more generic so it could work with other data source models
    The subset() function in the collection class can definitely be optimized.  If you have a lot of records in it, you will use a lot of memory currently.

* Describe the downside of your implmentation
    The collection class is convenient, but creates a lot more overhead than if I just worked with arrays coming from the data source.  Additionally, the parser relies on having all the correct information from the data source.  If the datasource send the warehouse_id instead of the warehouse_name, there is no way for the parser to fix this issue currently