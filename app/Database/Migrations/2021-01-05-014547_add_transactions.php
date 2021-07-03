<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransactions extends Migration
{
	public function up()
	{
        $this->forge->addField('transaction_id UUID PRIMARY KEY');
        $this->forge->addField([
            'user_id' => [
                'type' => 'UUID',
            ],
            'transaction_status' => [
                'type' => 'VARCHAR',
                'constraint' => 7
            ],
            'customer_money' => [
                'type' => 'NUMERIC',
                'constraint' => 10,
                'null' => true
            ],
            'created_at' => [
                'type' => 'TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP'
            ]
        ]);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'NO ACTION', 'RESTRICT');
        $this->forge->createTable('transactions');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('transactions');
	}
}
