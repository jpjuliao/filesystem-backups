<?php
require 'plugin.php';
use PHPUnit\Framework\TestCase;

class Folder_To_Database_Test extends TestCase
{
    public function test_get_folder_tree()
    {
        $folder_data = new Folder_To_Database;
        print_r( $folder_data->data );
        $this->assertIsArray( $folder_data->data );
    }

}