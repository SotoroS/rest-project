<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225220506CreateImagesTable
 */
class M200225220506CreateImagesTable extends Migration
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
        echo "M200225220506CreateImagesTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('images', [
            'id' => $this->primaryKey(),
            'path' => 'VARCHAR(256)',
            'object_id' => 'INT(19) DEFAULT 1', //FK
            'position' => 'INT(19)'
        ]);
    }

    public function down()
    {
        $this->dropTable('images');
    }
}
