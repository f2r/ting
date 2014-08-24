<?php

namespace sample\src\model;

use fastorm\Query;
use fastorm\Entity\Hydrator;
use fastorm\Entity\Metadata;
use fastorm\Entity\MetadataRepository;

class CityRepository extends \fastorm\Entity\Repository
{

    public function getZCountryWithLotsPopulation()
    {

        $query = new Query(
            'select cit_id, cit_name, cou_code, cit_district, cit_population
            from t_city_cit as a where cit_name like :name and cit_population > :population limit 3',
            array('name' => 'Z%', 'population' => 200000)
        );

        return $this->execute($query)->hydrator(new Hydrator());
    }

    public static function initMetadata(MetadataRepository $metadataRepository = null, Metadata $metadata = null)
    {
        if ($metadataRepository === null) {
            $metadataRepository = MetadataRepository::getInstance();
        }

        if ($metadata === null) {
            $metadata = new Metadata();
        }

        $metadata->setClass(get_class());
        $metadata->setConnection('main');
        $metadata->setDatabase('world');
        $metadata->setTable('t_city_cit');

        $metadata->addField(array(
            'primary'    => true,
            'fieldName'  => 'id',
            'columnName' => 'cit_id',
            'type'       => 'int'
        ));

        $metadata->addField(array(
            'fieldName'  => 'name',
            'columnName' => 'cit_name',
            'type'       => 'string'
        ));

        $metadata->addField(array(
            'fieldName'  => 'countryCode',
            'columnName' => 'cou_code',
            'type'       => 'string'
        ));

        $metadata->addField(array(
            'fieldName'  => 'district',
            'columnName' => 'cit_district',
            'type'       => 'string'
        ));

        $metadata->addField(array(
            'fieldName'  => 'population',
            'columnName' => 'cit_population',
            'type'       => 'int'
        ));

        $metadata->addInto($metadataRepository);
    }
}
