<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductPrices extends Migration
{
	public function up()
	{
        $this->forge->addField('product_price_id UUID PRIMARY KEY');
        $this->forge->addField([
            'product_id' => [
                'type' => 'UUID'
            ],
            'product_magnitude' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'product_price' => [
                'type' => 'NUMERIC',
                'constraint' => 10
            ]
        ]);
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'NO ACTION', 'CASCADE');
        $this->forge->createTable('product_prices');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('product_prices');
	}
}
