<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsers extends Migration
{
	public function up()
    {
        $this->forge->addField('user_id UUID PRIMARY KEY');
        $this->forge->addField([
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 32
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 32
            ],
            'level' => [
                'type' => 'VARCHAR',
                'constraint' => 7
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'last_sign_in' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'created_at' => [
                'type' => 'TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP'
            ]
        ]);
        $this->forge->createTable('users');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('users');
	}
}
