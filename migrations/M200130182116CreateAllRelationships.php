<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200130182116CreateAllRelationships
 */
class M200130182116CreateAllRelationships extends Migration
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
        echo "M200130182116CreateAllRelationships cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        //---REQUEST_OBJECT---//

        // creates index for column `user_id` in table `request_object`
        $this->createIndex(
            'idx-request_object-user_id',
            'request_object',
            'user_id'
        );
        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-request_object-user_id',
            'request_object',
            'user_id',
            'user',
            'id'
        );

        // creates index for column `city_id` in table `request_object`
        $this->createIndex(
            'idx-request_object-city_id',
            'request_object',
            'city_id'
        );
        // add foreign key for table `city`
        $this->addForeignKey(
            'fk-request_object-city_id',
            'request_object',
            'city_id',
            'city',
            'id'
        );

        // creates index for column `request_type_id` in table `request_object`
        $this->createIndex(
            'idx-request_object-request_type_id',
            'request_object',
            'request_type_id'
        );
        // add foreign key for table `request_type`
        $this->addForeignKey(
            'fk-request_object-request_type_id',
            'request_object',
            'request_type_id',
            'request_type',
            'id'
        );

        //---ESTATE_OBJECT---//

        // creates index for column `address_id` in table `estate_object`
        $this->createIndex(
            'idx-estate_object-address_id',
            'estate_object',
            'address_id'
        );
        // add foreign key for table `address`
        $this->addForeignKey(
            'fk-estate_object-address_id',
            'estate_object',
            'address_id',
            'address',
            'id'
        );

        // creates index for column `building_type_id` in table `estate_object`
        $this->createIndex(
            'idx-estate_object-building_type_id',
            'estate_object',
            'building_type_id'
        );
        // add foreign key for table `building_type`
        $this->addForeignKey(
            'fk-estate_object-building_type_id',
            'estate_object',
            'building_type_id',
            'building_type',
            'id'
        );

        // creates index for column `rent_type_id` in table `estate_object`
        $this->createIndex(
            'idx-estate_object-rent_type_id',
            'estate_object',
            'rent_type_id'
        );
        // add foreign key for table `rent_type`
        $this->addForeignKey(
            'fk-estate_object-rent_type_id',
            'estate_object',
            'rent_type_id',
            'rent_type',
            'id'
        );

        // creates index for column `property_type_id` in table `estate_object`
        $this->createIndex(
            'idx-estate_object-property_type_id',
            'estate_object',
            'property_type_id'
        );
        // add foreign key for table `property_type`
        $this->addForeignKey(
            'fk-estate_object-property_type_id',
            'estate_object',
            'property_type_id',
            'property_type',
            'id'
        );

        // creates index for column `metro_id` in table `estate_object`
        $this->createIndex(
            'idx-estate_object-metro_id',
            'estate_object',
            'metro_id'
        );
        // add foreign key for table `metro`
        $this->addForeignKey(
            'fk-estate_object-metro_id',
            'estate_object',
            'metro_id',
            'metro',
            'id'
        );

        // creates index for column `user_id` in table `estate_object`
        $this->createIndex(
            'idx-estate_object-user_id',
            'estate_object',
            'user_id'
        );
        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-estate_object-user_id',
            'estate_object',
            'user_id',
            'user',
            'id'
        );

        //---ADDRESS---//

        // creates index for column `region_id` in table `address`
        $this->createIndex(
            'idx-address-region_id',
            'address',
            'region_id'
        );
        // add foreign key for table `region`
        $this->addForeignKey(
            'fk-address-region_id',
            'address',
            'region_id',
            'region',
            'id'
        );

        // creates index for column `city_id` in table `address`
        $this->createIndex(
            'idx-address-city_id',
            'address',
            'city_id'
        );
        // add foreign key for table `city`
        $this->addForeignKey(
            'fk-address-city_id',
            'address',
            'city_id',
            'city',
            'id'
        );

        // creates index for column `city_area_id` in table `address`
        $this->createIndex(
            'idx-address-city_area_id',
            'address',
            'city_area_id'
        );
        // add foreign key for table `city_area`
        $this->addForeignKey(
            'fk-address-city_area_id',
            'address',
            'city_area_id',
            'city_area',
            'id'
        );

        // creates index for column `street_id` in table `address`
        $this->createIndex(
            'idx-address-street_id',
            'address',
            'street_id'
        );
        // add foreign key for table `street`
        $this->addForeignKey(
            'fk-address-street_id',
            'address',
            'street_id',
            'street',
            'id'
        );

        //---REQUEST_ADDRESS---//

        // creates index for column `address_id` in table `request_address`
        $this->createIndex(
            'idx-request_address-address_id',
            'request_address',
            'address_id'
        );
        // add foreign key for table `address`
        $this->addForeignKey(
            'fk-request_address-address_id',
            'request_address',
            'address_id',
            'address',
            'id'
        );

        // creates index for column `request_object_id` in table `request_address`
        $this->createIndex(
            'idx-request_address-request_object_id',
            'request_address',
            'request_object_id'
        );
        // add foreign key for table `request_object`
        $this->addForeignKey(
            'fk-request_address-request_object_id',
            'request_address',
            'request_object_id',
            'request_object',
            'id'
        );

        //---STREET---//

        // creates index for column `city_area_id` in table `street`
        $this->createIndex(
            'idx-street-city_area_id',
            'street',
            'city_area_id'
        );
        // add foreign key for table `city_area`
        $this->addForeignKey(
            'fk-street-city_area_id',
            'street',
            'city_area_id',
            'city_area',
            'id'
        );

        //---CITY_AREA---//

        // creates index for column `city_id` in table `city_area`
        $this->createIndex(
            'idx-city_area-city_id',
            'city_area',
            'city_id'
        );
        // add foreign key for table `city`
        $this->addForeignKey(
            'fk-city_area-city_id',
            'city_area',
            'city_id',
            'city',
            'id'
        );

        //---CITY---//

        // creates index for column `region_id` in table `city`
        $this->createIndex(
            'idx-city-region_id',
            'city',
            'region_id'
        );
        // add foreign key for table `region`
        $this->addForeignKey(
            'fk-city-region_id',
            'city',
            'region_id',
            'region',
            'id'
        );
    }

    public function down()
    {
        //---REQUEST_OBJECT---//

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-request_object-user_id',
            'request_object'
        );
        // drops index for column `user_id`
        $this->dropIndex(
            'idx-request_object-user_id',
            'request_object'
        );

        // drops foreign key for table `city`
        $this->dropForeignKey(
            'fk-request_object-city_id',
            'request_object'
        );
        // drops index for column `city_id`
        $this->dropIndex(
            'idx-request_object-city_id',
            'request_object'
        );

        // drops foreign key for table `request_type`
        $this->dropForeignKey(
            'fk-request_object-request_type_id',
            'request_object'
        );
        // drops index for column `request_type_id`
        $this->dropIndex(
            'idx-request_object-request_type_id',
            'request_object'
        );

        //---ESTATE_OBJECT---//

        // drops foreign key for table `address`
        $this->dropForeignKey(
            'fk-estate_object-address_id',
            'estate_object'
        );
        // drops index for column `address_id`
        $this->dropIndex(
            'idx-estate_object-address_id',
            'estate_object'
        );

        // drops foreign key for table `building_type`
        $this->dropForeignKey(
            'fk-estate_object-building_type_id',
            'estate_object'
        );
        // drops index for column `building_type_id`
        $this->dropIndex(
            'idx-estate_object-building_type_id',
            'estate_object'
        );

        // drops foreign key for table `rent_type`
        $this->dropForeignKey(
            'fk-estate_object-rent_type_id',
            'estate_object'
        );
        // drops index for column `rent_type_id`
        $this->dropIndex(
            'idx-estate_object-rent_type_id',
            'estate_object'
        );

        // drops foreign key for table `property_type`
        $this->dropForeignKey(
            'fk-estate_object-property_type_id',
            'estate_object'
        );
        // drops index for column `property_type_id`
        $this->dropIndex(
            'idx-estate_object-property_type_id',
            'estate_object'
        );

        // drops foreign key for table `metro`
        $this->dropForeignKey(
            'fk-estate_object-metro_id',
            'estate_object'
        );
        // drops index for column `metro_id`
        $this->dropIndex(
            'idx-estate_object-metro_id',
            'estate_object'
        );

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-estate_object-user_id',
            'estate_object'
        );
        // drops index for column `user_id`
        $this->dropIndex(
            'idx-estate_object-user_id',
            'estate_object'
        );

        //---ADDRESS---//

        // drops foreign key for table `region`
        $this->dropForeignKey(
            'fk-address-region_id',
            'address'
        );
        // drops index for column `region_id`
        $this->dropIndex(
            'idx-address-region_id',
            'address'
        );

        // drops foreign key for table `city`
        $this->dropForeignKey(
            'fk-address-city_id',
            'address'
        );
        // drops index for column `city_id`
        $this->dropIndex(
            'idx-address-city_id',
            'address'
        );

        // drops foreign key for table `city_area`
        $this->dropForeignKey(
            'fk-address-city_area_id',
            'address'
        );
        // drops index for column `city_area_id`
        $this->dropIndex(
            'idx-address-city_area_id',
            'address'
        );

        // drops foreign key for table `street`
        $this->dropForeignKey(
            'fk-address-street_id',
            'address'
        );
        // drops index for column `treet_id`
        $this->dropIndex(
            'idx-address-street_id',
            'address'
        );

        //---REQUEST_ADDRESS---//

        // drops foreign key for table `address`
        $this->dropForeignKey(
            'fk-request_address-address_id',
            'request_address'
        );
        // drops index for column `address_id`
        $this->dropIndex(
            'idx-request_address-address_id',
            'request_address'
        );

        // drops foreign key for table `request_object`
        $this->dropForeignKey(
            'fk-request_address-request_object_id',
            'request_address'
        );
        // drops index for column `request_object_id`
        $this->dropIndex(
            'idx-request_address-request_object_id',
            'request_address'
        );

        //---STREET---//

        // drops foreign key for table `city_area`
        $this->dropForeignKey(
            'fk-street-city_area_id',
            'street'
        );
        // drops index for column `city_area_id`
        $this->dropIndex(
            'idx-street-city_area_id',
            'street'
        );

        //---CITY_AREA---//

        // drops foreign key for table `city`
        $this->dropForeignKey(
            'fk-city_area-city_id',
            'city_area'
        );
        // drops index for column `city_id`
        $this->dropIndex(
            'idx-city_area-city_id',
            'city_area'
        );

        //---CITY---//

        // drops foreign key for table `region`
        $this->dropForeignKey(
            'fk-city-region_id',
            'city'
        );
        // drops index for column `region_id`
        $this->dropIndex(
            'idx-city-region_id',
            'city'
        );
    }
}
