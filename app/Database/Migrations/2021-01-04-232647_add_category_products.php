<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryProducts extends Migration
{
	public function up()
	{
        $this->forge->addField('category_product_id UUID PRIMARY KEY');
        $this->forge->addField([
            'category_product_name' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'created_at' => [
                'type' => 'TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP'
            ]
        ]);
        $this->forge->createTable('category_products');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('category_products');
	}
}
