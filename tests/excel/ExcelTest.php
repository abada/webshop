<?php

class ExcelTest extends TestCase {

    protected $mock;
    protected $excel;

    public function setUp()
    {
        parent::setUp();

        $this->excel = new App\Droit\Generate\Excel\ExcelGenerator();

        $user = App\Droit\User\Entities\User::find(1);

        $this->actingAs($user);
    }

    public function tearDown()
    {
         \Mockery::close();
    }

    /**
	 *
	 * @return void
	 */
	public function testSetColloqueAndOptions()
	{

        $colloque = new \App\Droit\Colloque\Entities\Colloque();

        $inscriptions = factory(\App\Droit\Inscription\Entities\Inscription::class, 2)->make();
        $options      = factory(\App\Droit\Option\Entities\Option::class, 2)->make();

        $colloque->id = 1;
        $colloque->options      = $options;
        $colloque->inscriptions = $inscriptions;

        $actual = $this->excel->setColloque($colloque);

        $this->assertEquals(1, $actual->colloque->id);
        $this->assertEquals(2, $actual->options->count());
        $this->assertEquals(2, $actual->inscriptions->count());

	}

    public function testPrepareOptions()
    {
        $colloque = new \App\Droit\Colloque\Entities\Colloque();

        $inscriptions = factory(\App\Droit\Inscription\Entities\Inscription::class, 2)->make();
        $options      = factory(\App\Droit\Option\Entities\Option::class,2)->make();

        $options = $options->map(function ($item, $key) {
            $groupe       = factory(\App\Droit\Option\Entities\OptionGroupe::class, 2)->make();
            $item->groupe = $groupe;
            return $item;
        });

        $colloque->id = 1;
        $colloque->options      = $options;
        $colloque->inscriptions = $inscriptions;

        $this->excel->setColloque($colloque);

        $actual = $this->excel->getMainOptions();
        $groupe = $this->excel->getGroupeOptions();

        $expect_option = [ 1 => 'Option', 1 => 'Option' ];
        $expect_groupe = [
            1 => [1 => 'Groupe', 1 => 'Groupe']
        ];

        $this->assertEquals($expect_option, $actual);
        $this->assertEquals($expect_groupe, $groupe);
    }

    public function testRowInfosForInscription()
    {
        $inscription = factory(\App\Droit\Inscription\Entities\Inscription::class)->make();

        $this->excel->setColumns(['name','npa']);

        $actual = $this->excel->row($inscription);

        $i = array_search('participant', array_keys($actual));

        $this->assertEquals('2520', $actual['npa']);
        $this->assertEquals('Cindy Leschaud', $actual['name']);
        $this->assertTrue(isset($actual['participant']));
        $this->assertEquals(2,$i);
    }

    public function testDispatchInscriptionsGroupe()
    {
        $worker = new App\Droit\Generate\Excel\ExcelGenerator();
        // We assume the inscription has this option!
        $inscription = factory(\App\Droit\Inscription\Entities\Inscription::class)->make();

        $option  = [1 => [1,2,3]];

        $current = new \Illuminate\Database\Eloquent\Collection([
            ['groupe_id' => 1],
            ['groupe_id' => 3],
            ['groupe_id' => 4]
        ]);

        $worker->optionDispatch($option,1,$current,$inscription);

        $collection = $worker->dispatch[1][1];

        $this->assertEquals(1,count($collection));

    }

    public function testDispatchInscriptions()
    {
        $worker = new App\Droit\Generate\Excel\ExcelGenerator();
        // We assume the inscription has this option!
        $inscription = factory(\App\Droit\Inscription\Entities\Inscription::class)->make();

        $option  = [1 => [1,2,3]];

        $current = new \Illuminate\Database\Eloquent\Collection([
            ['groupe_id' => 1],
            ['groupe_id' => 2],
            ['groupe_id' => 4]
        ]);

        $worker->optionDispatch($option,1,$current,$inscription);

        $collection = $worker->dispatch[1];

        $this->assertEquals(1,count($collection));

    }

    public function testDispatchInscriptionsNoUserOption()
    {
        $worker = new App\Droit\Generate\Excel\ExcelGenerator();

        $inscriptions = factory(\App\Droit\Inscription\Entities\Inscription::class, 2)->make();

        $inscriptions = $inscriptions->map(function ($item, $key) {

            $item->user_options = new \Illuminate\Database\Eloquent\Collection([
                ['option_id' => 1, 'groupe_id' => 2],
                ['option_id' => 1, 'groupe_id' => 3],
                ['option_id' => 4, 'groupe_id' => 4]
            ]);

            return $item;
        });

        $options = [5 => 'une option'];

        $worker->dispatch($inscriptions, $options);

        $this->assertEquals(2,count($worker->dispatch[0]));

    }

    public function testDispatchInscriptionsOneSimpleUserOption()
    {
        $worker = new App\Droit\Generate\Excel\ExcelGenerator();

        $inscriptions = factory(\App\Droit\Inscription\Entities\Inscription::class, 2)->make();

        $inscription1 = $inscriptions->first();

        $inscription1->user_options = new \Illuminate\Database\Eloquent\Collection([
            ['option_id' => 1, 'groupe_id' => 2],
            ['option_id' => 1, 'groupe_id' => 3],
            ['option_id' => 4, 'groupe_id' => null]
        ]);

        $options = [4 => 'une option'];

        $worker->dispatch($inscriptions, $options);

        $this->assertEquals(1,count($worker->dispatch[4]));

    }

}