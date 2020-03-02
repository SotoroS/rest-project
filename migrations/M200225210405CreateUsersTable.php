<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225210405CreateUsersTable
 */
class M200225210405CreateUsersTable extends Migration
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
        echo "M200225210405CreateUsersTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'gender' => 'VARCHAR(1)',
            'phone' => 'VARCHAR(30)',
            'email' => 'VARCHAR(256)',
            'password' => 'VARCHAR(256)',
            'access_token' => 'VARCHAR(256)',
            'signup_token' => 'VARCHAR(256)',
            'age' => 'INT(19)',
            'verified' => 'BOOLEAN DEFAULT false',
            'notifications' => 'BOOLEAN DEFAULT false',
            'last_fetch' => 'DATETIME',
            'premium' => 'BOOLEAN DEFAULT false',
            'status' => 'BOOLEAN DEFAULT false',
            'created_at' => 'TIMESTAMP',
            'updated_at' => 'TIMESTAMP',
            'fcmToken' => 'VARCHAR(256)',
            'deviceType' => 'VARCHAR(256)'
        ]);
    }   

    public function down()
    {
        $this->dropTable('users');
    }
}
