<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200315144636InsertPropertyType
 */
class M200315144636InsertPropertyType extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert(
            'property_type',
            ['name'],
            [
                ['квартира'],
                ['не указан'],
                ['койко-место'],
                ['комната'],
                ['дом'],
                ['хостел']
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('rent_type');
    }
}
