<?php

namespace tests\units\fastorm\Entity;

use \mageekguy\atoum;

class Collection extends atoum
{

    public function testHydrateNullShouldReturnNull()
    {
        $this
            ->if($collection = new \fastorm\Entity\Collection())
            ->variable($collection->hydrate(null))
                ->isNull();
    }

    public function testHydrateShouldDoNothingWithoutHydrator()
    {
        $data = array('Bouh' => array());

        $this
            ->if($collection = new \fastorm\Entity\Collection())
            ->array($collection->hydrate($data))
                ->isIdenticalTo($data);
    }

    public function testHydrateWithHydratorShouldCallHydratorHydrate()
    {
        $mockHydrator = new \mock\fastorm\Entity\Hydrator();
        $data = array(
            array(
                'name'     => 'name',
                'orgName'  => 'BOO_NAME',
                'table'    => 'bouh',
                'orgTable' => 'T_BOUH_BOO',
                'value'    => 'Sylvain'
            )
        );

        $this
            ->if($collection = new \fastorm\Entity\Collection())
            ->then($collection->hydrator($mockHydrator))
            ->then($collection->hydrate($data))
            ->mock($mockHydrator)
                ->call('hydrate')
                    ->withIdenticalArguments($data)->once();
    }

    public function testIterator()
    {
        $mockMysqliResult = new \mock\tests\fixtures\FakeDriver\MysqliResult(array('Bouh'));
        $result = new \fastorm\Driver\Mysqli\Result($mockMysqliResult);

        $this
            ->if($collection = new \fastorm\Entity\Collection())
            ->then($collection->set($result))
            ->then($collection->rewind())
            ->mock($mockMysqliResult)
                ->call('rewind')->once()
                ->call('valid')->once()
            ->then($collection->key())
            ->mock($mockMysqliResult)
                ->call('key')->once()
            ->then($collection->next())
            ->mock($mockMysqliResult)
                ->call('next')->once()
            ->then($collection->valid())
            ->mock($mockMysqliResult)
                ->call('valid')->twice()
            ->then($collection->current())
            ->mock($mockMysqliResult)
                ->call('current')->once();
    }
}
