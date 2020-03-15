<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200315142619InsertRentType
 */
class M200315142619InsertRentType extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert(
            'rent_type',
            ['name'],
            [
                ['посуточно'],
                ['долгосрочная'],
                ['не указан'],
                ['хостел'],
                ['сниму'],
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
