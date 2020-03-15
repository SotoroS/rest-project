<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200315141332InsertBuildingType
 */
class M200315141332InsertBuildingType extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert(
            'building_type',
            ['name'],
            [
                ['кирпичный'],
                ['монолитный'],
                ['панельный'],
                ['деревянный'],
                ['блочный']
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('building_type');
    }
}
