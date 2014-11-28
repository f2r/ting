<?php
/***********************************************************************
 *
 * Ting - PHP Datamapper
 * ==========================================
 *
 * Copyright (C) 2014 CCM Benchmark Group. (http://www.ccmbenchmark.com)
 *
 ***********************************************************************
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you
 * may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 **********************************************************************/

namespace CCMBenchmark\Ting;

use CCMBenchmark\Ting\Repository\Metadata;

class MetadataRepository
{

    protected $metadataList    = array();

    /**
     * @param          $table
     * @param callable $callbackFound   called with applicable Metadata if applicable
     * @param callable $callbackNotFound called if unknown table - no parameter
     */
    public function findMetadataForTable($table, \Closure $callbackFound, \Closure $callbackNotFound)
    {
        $found = false;
        foreach ($this->metadataList as $metadata) {
            $found = $metadata->ifTableKnown(
                $table,
                function (Metadata $metadata) use ($callbackFound) {
                    $callbackFound($metadata);
                }
            );

            if ($found === true) {
                break;
            }
        }

        if ($found === false) {
            $callbackNotFound();
        }
    }

    /**
     * @param          $entity
     * @param callable $callbackFound Called with applicable Metadata if applicable
     * @param callable $callbackNotFound called if unknown entity - no parameter
     */
    public function findMetadataForEntity($entity, \Closure $callbackFound, \Closure $callbackNotFound = null)
    {
        $repository = get_class($entity) . 'Repository';
        if (isset($this->metadataList[$repository]) === true) {
            $callbackFound($this->metadataList[$repository]);
        } elseif ($callbackNotFound !== null) {
            $callbackNotFound();
        }
    }

    public function addMetadata($repositoryClass, Metadata $metadata)
    {
        if (isset($this->metadataList[$repositoryClass]) === false) {
            $this->metadataList[$repositoryClass] = $metadata;
        }

    }

    /**
     * Read every files from given globPattern and load in memory all metadatas
     * This method should be used to discover the files and then create cache,
     * because glob uses directory reading at every hit.
     *
     * @param $namespace
     * @param $globPattern
     * @return array
     */
    public function batchLoadMetadata($namespace, $globPattern)
    {
        $loaded = [];

        if (file_exists(dirname($globPattern)) === false) {
            return $loaded;
        }

        foreach (glob($globPattern) as $repositoryFile) {
            $repository = $namespace . '\\' . basename($repositoryFile, '.php');
            $this->addMetadata($repository, $repository::initMetadata());
            $loaded[] = $repository;
        }

        return $loaded;
    }


    /**
     * Read every classes (should be fully qualified namespaces) to load metadatas in memory.
     * This method is far more efficient than batchLoadMetadata : with opcache enabled, files
     * are not read from disk anymore.
     *
     * @param array $paths
     * @return array
     */
    public function batchLoadMetadataFromCache(array $paths)
    {
        $loaded = [];
        foreach ($paths as $repository) {
            $this->addMetadata($repository, $repository::initMetadata());
            $loaded[] = $repository;
        }

        return $loaded;
    }
}
