<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130000215CreateUserTable
 */
class M200130000215CreateUserTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "M200130000215CreateUserTable cannot be reverted.\n";

        return false;
    }

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'gender' => 'VARCHAR(1) NOT NULL',
            'phone' => 'VARCHAR(30) NOT NULL',
            'email' => 'VARCHAR(256) NOT NULL',
            'age' => 'INT NOT NULL',
            'verified' => 'BOOLEAN DEFAULT false',
            'signup_token' => 'VARCHAR(13)'
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
