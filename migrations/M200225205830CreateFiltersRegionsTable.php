<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200225205830CreateFiltersRegionsTable
 */
class M200225205830CreateFiltersRegionsTable extends Migration
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
        echo "M200225205830CreateFiltersRegionsTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createTable('filters_regions', [
            'id' => $this->primaryKey(),
            'regions_id' => 'INT(19) DEFAULT 1', // fk regions
            'filters_id' => 'INT(19) DEFAULT 1' // fk filters
        ]);
    }

    public function down()
    {
        $this->dropTable('filters_regions');
    }
}
