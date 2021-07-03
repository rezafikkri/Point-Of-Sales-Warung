<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProducts extends Migration
{
	public function up()
	{
        $this->forge->addField('product_id UUID PRIMARY KEY');
        $this->forge->addField([
            'category_product_id' => [
                'type' => 'UUID'
            ],
            'product_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'product_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 35
            ],
            'product_status' => [
                'type' => 'VARCHAR',
                'constraint' => 9
            ],
            'created_at' => [
                'type' => 'TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP'
            ]
        ]);
        $this->forge->addForeignKey('category_product_id', 'category_products', 'category_product_id', 'NO ACTION', 'RESTRICT');
        $this->forge->createTable('products');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('products');
	}
}
