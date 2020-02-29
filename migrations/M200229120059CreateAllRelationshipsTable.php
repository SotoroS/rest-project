<?php

namespace micro\migrations;

use yii\db\Migration;

/**
 * Class M200229120059CreateAllRelationshipsTable
 */
class M200229120059CreateAllRelationshipsTable extends Migration
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
        echo "M200229120059CreateAllRelationshipsTable cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        // ---OBJECTS--- //

        // creates index for column `address_id` in table `objects`
        $this->createIndex(
            'idx-objects-address_id',
            'objects',
            'address_id'
        );
        // add foreign key for table `address`
        $this->addForeignKey(
            'fk-objects-address_id',
            'objects',
            'address_id',
            'address',
            'id'
        );

        // creates index for column `building_type_id` in table `objects`
        $this->createIndex(
            'idx-objects-building_type_id',
            'objects',
            'building_type_id'
        );
        // add foreign key for table `building_type`
        $this->addForeignKey(
            'fk-objects-building_type_id',
            'objects',
            'building_type_id',
            'building_type',
            'id'
        );

        // creates index for column `rent_type` in table `objects`
        $this->createIndex(
            'idx-objects-rent_type',
            'objects',
            'rent_type'
        );
        // add foreign key for table `rent_type`
        $this->addForeignKey(
            'fk-objects-rent_type',
            'objects',
            'rent_type',
            'rent_type',
            'id'
        );

        // creates index for column `property_type` in table `objects`
        $this->createIndex(
            'idx-objects-property_type',
            'objects',
            'property_type'
        );
        // add foreign key for table `property_type`
        $this->addForeignKey(
            'fk-objects-property_type',
            'objects',
            'property_type',
            'property_type',
            'id'
        );

        // creates index for column `metro_id` in table `objects`
        $this->createIndex(
            'idx-objects-metro_id',
            'objects',
            'metro_id'
        );
        // add foreign key for table `metro`
        $this->addForeignKey(
            'fk-objects-metro_id',
            'objects',
            'metro_id',
            'metro',
            'id'
        );

        // creates index for column `user_id` in table `objects`
        $this->createIndex(
            'idx-objects-user_id',
            'objects',
            'user_id'
        );
        // add foreign key for table `users`
        $this->addForeignKey(
            'fk-objects-user_id',
            'objects',
            'user_id',
            'users',
            'id'
        );

        // creates index for column `city_id` in table `objects`
        $this->createIndex(
            'idx-objects-city_id',
            'objects',
            'city_id'
        );
        // add foreign key for table `cities`
        $this->addForeignKey(
            'fk-objects-city_id',
            'objects',
            'city_id',
            'cities',
            'id'
        );

        // creates index for column `region_id` in table `objects`
        $this->createIndex(
            'idx-objects-region_id',
            'objects',
            'region_id'
        );
        // add foreign key for table `regions`
        $this->addForeignKey(
            'fk-objects-region_id',
            'objects',
            'region_id',
            'regions',
            'id'
        );

        // creates index for column `city_area_id` in table `objects`
        $this->createIndex(
            'idx-objects-city_area_id',
            'objects',
            'city_area_id'
        );
        // add foreign key for table `city_areas`
        $this->addForeignKey(
            'fk-objects-city_area_id',
            'objects',
            'city_area_id',
            'city_areas',
            'id'
        );

        // ---ADDRESS--- //

        // creates index for column `city_id` in table `address`
        $this->createIndex(
            'idx-address-city_id',
            'address',
            'city_id'
        );
        // add foreign key for table `cities`
        $this->addForeignKey(
            'fk-address-city_id',
            'address',
            'city_id',
            'cities',
            'id'
        );

        // creates index for column `region_id` in table `address`
        $this->createIndex(
            'idx-address-region_id',
            'address',
            'region_id'
        );
        // add foreign key for table `regions`
        $this->addForeignKey(
            'fk-address-region_id',
            'address',
            'region_id',
            'regions',
            'id'
        );

        // creates index for column `city_area_id` in table `address`
        $this->createIndex(
            'idx-address-city_area_id',
            'address',
            'city_area_id'
        );
        // add foreign key for table `city_areas`
        $this->addForeignKey(
            'fk-address-city_area_id',
            'address',
            'city_area_id',
            'city_areas',
            'id'
        );

        // creates index for column `street_id` in table `address`
        $this->createIndex(
            'idx-address-street_id',
            'address',
            'street_id'
        );
        // add foreign key for table `streets`
        $this->addForeignKey(
            'fk-address-street_id',
            'address',
            'street_id',
            'streets',
            'id'
        );

        // ---STREETS--- //

        // creates index for column `city_area_id` in table `streets`
        $this->createIndex(
            'idx-streets-city_area_id',
            'streets',
            'city_area_id'
        );
        // add foreign key for table `city_areas`
        $this->addForeignKey(
            'fk-streets-city_area_id',
            'streets',
            'city_area_id',
            'city_areas',
            'id'
        );

        // ---CITY_AREAS--- //

        // creates index for column `city_id` in table `city_areas`
        $this->createIndex(
            'idx-city_areas-city_id',
            'city_areas',
            'city_id'
        );
        // add foreign key for table `cities`
        $this->addForeignKey(
            'fk-city_areas-city_id',
            'city_areas',
            'city_id',
            'cities',
            'id'
        );

        // ---REGIONS--- //

        // creates index for column `area_id` in table `regions`
        $this->createIndex(
            'idx-regions-area_id',
            'regions',
            'area_id'
        );
        // add foreign key for table `country_areas`
        $this->addForeignKey(
            'fk-regions-area_id',
            'regions',
            'area_id',
            'country_areas',
            'id'
        );

        // creates index for column `city_id` in table `regions`
        $this->createIndex(
            'idx-regions-city_id',
            'regions',
            'city_id'
        );
        // add foreign key for table `cities`
        $this->addForeignKey(
            'fk-regions-city_id',
            'regions',
            'city_id',
            'cities',
            'id'
        );

        // creates index for column `city_area_id` in table `regions`
        $this->createIndex(
            'idx-regions-city_area_id',
            'regions',
            'city_area_id'
        );
        // add foreign key for table `city_areas`
        $this->addForeignKey(
            'fk-regions-city_area_id',
            'regions',
            'city_area_id',
            'city_areas',
            'id'
        );

        // creates index for column `street_id` in table `regions`
        $this->createIndex(
            'idx-regions-street_id',
            'regions',
            'street_id'
        );
        // add foreign key for table `streets`
        $this->addForeignKey(
            'fk-regions-street_id',
            'regions',
            'street_id',
            'streets',
            'id'
        );

        // ---FILTERS--- //

        // creates index for column `user_id` in table `filters`
        $this->createIndex(
            'idx-filters-user_id',
            'filters',
            'user_id'
        );
        // add foreign key for table `users`
        $this->addForeignKey(
            'fk-filters-user_id',
            'filters',
            'user_id',
            'users',
            'id'
        );

        // creates index for column `request_type_id` in table `filters`
        $this->createIndex(
            'idx-filters-request_type_id',
            'filters',
            'request_type_id'
        );
        // add foreign key for table `request_type`
        $this->addForeignKey(
            'fk-filters-request_type_id',
            'filters',
            'request_type_id',
            'request_type',
            'id'
        );

        // creates index for column `city_id` in table `filters`
        $this->createIndex(
            'idx-filters-city_id',
            'filters',
            'city_id'
        );
        // add foreign key for table `cities`
        $this->addForeignKey(
            'fk-filters-city_id',
            'filters',
            'city_id',
            'cities',
            'id'
        );

        // creates index for column `city_area_id` in table `filters`
        $this->createIndex(
            'idx-filters-city_area_id',
            'filters',
            'city_area_id'
        );
        // add foreign key for table `city_areas`
        $this->addForeignKey(
            'fk-filters-city_area_id',
            'filters',
            'city_area_id',
            'city_areas',
            'id'
        );

        // ---FILTERS_ADDRESS--- //

        // creates index for column `regions_id` in table `filters_address`
        $this->createIndex(
            'idx-filters_address-address_id',
            'filters_address',
            'address_id'
        );
        // add foreign key for table `address`
        $this->addForeignKey(
            'fk-filters_address-address_id',
            'filters_address',
            'address_id',
            'address',
            'id'
        );

        // creates index for column `filters_id` in table `filters_address`
        $this->createIndex(
            'idx-filters_address-filters_id',
            'filters_address',
            'filters_id'
        );
        // add foreign key for table `filters`
        $this->addForeignKey(
            'fk-filters_address-filters_id',
            'filters_address',
            'filters_id',
            'filters',
            'id'
        );

        // ---IMAGES--- //

        // creates index for column `object_id` in table `images`
        $this->createIndex(
            'idx-images-object_id',
            'images',
            'object_id'
        );
        // add foreign key for table `objects`
        $this->addForeignKey(
            'fk-images-object_id',
            'images',
            'object_id',
            'objects',
            'id'
        );

        // ---PHONES--- //

        // creates index for column `object_id` in table `phones`
        $this->createIndex(
            'idx-phones-object_id',
            'phones',
            'object_id'
        );
        // add foreign key for table `objects`
        $this->addForeignKey(
            'fk-phones-object_id',
            'phones',
            'object_id',
            'objects',
            'id'
        );
    }

    public function down()
    {
        // ---OBJECTS--- //

        // drops foreign key for table `address`
        $this->dropForeignKey(
            'fk-objects-address_id',
            'objects'
        );
        // drops index for column `address_id`
        $this->dropIndex(
            'idx-objects-address_id',
            'objects'
        );

        // drops foreign key for table `building_type`
         $this->dropForeignKey(
            'fk-objects-building_type_id',
            'objects'
        );
        // drops index for column `building_type_id`
        $this->dropIndex(
            'idx-objects-building_type_id',
            'objects'
        );

        // drops foreign key for table `rent_type`
         $this->dropForeignKey(
            'fk-objects-rent_type',
            'objects'
        );
        // drops index for column `rent_type`
        $this->dropIndex(
            'idx-objects-rent_type',
            'objects'
        );

        // drops foreign key for table `property_type`
        $this->dropForeignKey(
            'fk-objects-property_type',
            'objects'
        );
        // drops index for column `property_type`
        $this->dropIndex(
            'idx-objects-property_type',
            'objects'
        );

        // drops foreign key for table `metro`
        $this->dropForeignKey(
            'fk-objects-metro_id',
            'objects'
        );
        // drops index for column `metro_id`
        $this->dropIndex(
            'idx-objects-metro_id',
            'objects'
        );

        // drops foreign key for table `users`
        $this->dropForeignKey(
            'fk-objects-user_id',
            'objects'
        );
        // drops index for column `user_id`
        $this->dropIndex(
            'idx-objects-user_id',
            'objects'
        );

        // drops foreign key for table `cities`
        $this->dropForeignKey(
            'fk-objects-city_id',
            'objects'
        );
        // drops index for column `city_id`
        $this->dropIndex(
            'idx-objects-city_id',
            'objects'
        );

        // drops foreign key for table `regions`
        $this->dropForeignKey(
            'fk-objects-region_id',
            'objects'
        );
        // drops index for column `region_id`
        $this->dropIndex(
            'idx-objects-region_id',
            'objects'
        );

        // drops foreign key for table `city_areas`
        $this->dropForeignKey(
            'fk-objects-city_area_id',
            'objects'
        );
        // drops index for column `city_area_id`
        $this->dropIndex(
            'idx-objects-city_area_id',
            'objects'
        );

        // ---ADDRESS--- //

        // drops foreign key for table `regions`
        $this->dropForeignKey(
            'fk-address-region_id',
            'address'
        );
        // drops index for column `region_id`
        $this->dropIndex(
            'idx-address-region_id',
            'address'
        );

        // drops foreign key for table `cities`
        $this->dropForeignKey(
            'fk-address-city_id',
            'address'
        );
        // drops index for column `city_id`
        $this->dropIndex(
            'idx-address-city_id',
            'address'
        );

        // drops foreign key for table `city_areas`
        $this->dropForeignKey(
            'fk-address-city_area_id',
            'address'
        );
        // drops index for column `city_area_id`
        $this->dropIndex(
            'idx-address-city_area_id',
            'address'
        );

        // drops foreign key for table `streets`
        $this->dropForeignKey(
            'fk-address-street_id',
            'address'
        );
        // drops index for column `street_id`
        $this->dropIndex(
            'idx-address-street_id',
            'address'
        );

        // ---STREETS--- //

        // drops foreign key for table `city_areas`
        $this->dropForeignKey(
            'fk-streets-city_area_id',
            'streets'
        );
        // drops index for column `city_area_id`
        $this->dropIndex(
            'idx-streets-city_area_id',
            'streets'
        );

        // ---CITY_AREAS--- //

        // drops foreign key for table `cities`
        $this->dropForeignKey(
            'fk-city_areas-city_id',
            'city_areas'
        );
        // drops index for column `city_id`
        $this->dropIndex(
            'idx-city_areas-city_id',
            'city_areas'
        );

        // ---REGIONS--- //

        // drops foreign key for table `country_areas`
        $this->dropForeignKey(
            'fk-regions-area_id',
            'regions'
        );
        // drops index for column `area_id`
        $this->dropIndex(
            'idx-regions-area_id',
            'regions'
        );

        // drops foreign key for table `cities`
        $this->dropForeignKey(
            'fk-regions-city_id',
            'regions'
        );
        // drops index for column `city_id`
        $this->dropIndex(
            'idx-regions-city_id',
            'regions'
        );

        // drops foreign key for table `city_areas`
        $this->dropForeignKey(
            'fk-regions-city_area_id',
            'regions'
        );
        // drops index for column `city_area_id`
        $this->dropIndex(
            'idx-regions-city_area_id',
            'regions'
        );

        // drops foreign key for table `streets`
        $this->dropForeignKey(
            'fk-regions-street_id',
            'regions'
        );
        // drops index for column `street_id`
        $this->dropIndex(
            'idx-regions-street_id',
            'regions'
        );

        // ---FILTERS--- //

        // drops foreign key for table `users`
        $this->dropForeignKey(
            'fk-filters-user_id',
            'filters'
        );
        // drops index for column `user_id`
        $this->dropIndex(
            'idx-filters-user_id',
            'filters'
        );

        // drops foreign key for table `request_type`
        $this->dropForeignKey(
            'fk-filters-request_type_id',
            'filters'
        );
        // drops index for column `request_type_id`
        $this->dropIndex(
            'idx-filters-request_type_id',
            'filters'
        );

        // drops foreign key for table `cities`
        $this->dropForeignKey(
            'fk-filters-city_id',
            'filters'
        );
        // drops index for column `city_id`
        $this->dropIndex(
            'idx-filters-city_id',
            'filters'
        );

        // drops foreign key for table `city_areas`
        $this->dropForeignKey(
            'fk-filters-city_area_id',
            'filters'
        );
        // drops index for column `city_area_id`
        $this->dropIndex(
            'idx-filters-city_area_id',
            'filters'
        );

        // ---FILTERS_ADDRESS--- //

        // drops foreign key for table `address`
        $this->dropForeignKey(
            'fk-filters_address-address_id',
            'filters_address'
        );
        // drops index for column `address_id`
        $this->dropIndex(
            'idx-filters_address-address_id',
            'filters_address'
        );

        // drops foreign key for table `filters`
        $this->dropForeignKey(
            'fk-filters_address-filters_id',
            'filters_address'
        );
        // drops index for column `filters_id`
        $this->dropIndex(
            'idx-filters_address-filters_id',
            'filters_address'
        );

        // ---IMAGES--- //

        // drops foreign key for table `objects`
        $this->dropForeignKey(
            'fk-images-object_id',
            'images'
        );
        // drops index for column `object_id`
        $this->dropIndex(
            'idx-images-object_id',
            'images'
        );

        // ---PHONES--- //

        // drops foreign key for table `objects`
        $this->dropForeignKey(
            'fk-phones-object_id',
            'phones'
        );
        // drops index for column `object_id`
        $this->dropIndex(
            'idx-phones-object_id',
            'phones'
        );
    }
}
