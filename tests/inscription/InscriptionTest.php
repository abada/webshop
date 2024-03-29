<?php

class InscriptionTest extends TestCase {

    protected $mock;
    protected $groupe;
    protected $interface;
    protected $worker;

    public function setUp()
    {
        parent::setUp();

        $this->mock = Mockery::mock('App\Droit\Inscription\Repo\InscriptionInterface');
        $this->app->instance('App\Droit\Inscription\Repo\InscriptionInterface', $this->mock);

        $this->groupe = Mockery::mock('App\Droit\Inscription\Repo\GroupeInterface');
        $this->app->instance('App\Droit\Inscription\Repo\GroupeInterface', $this->groupe);

        $this->worker = new App\Droit\Inscription\Worker\InscriptionWorker();

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
	public function testRegisterNewInscription()
	{
        $this->WithoutEvents();

        $input = ['type' => 'simple', 'colloque_id' => 71, 'user_id' => 1, 'inscription_no' => '71-2015/1', 'price_id' => 290];

        $inscription = new \App\Droit\Inscription\Entities\Inscription();

        $this->mock->shouldReceive('getByUser')->once();
        $this->mock->shouldReceive('create')->once()->with($input)->andReturn($inscription);

        $response = $this->call('POST', '/admin/inscription', $input);

        $this->assertRedirectedTo('/admin/inscription/colloque/71');

	}

    /**
     *
     * @return void
     */
    public function testRegisterMultipleNewInscription()
    {

        $this->WithoutEvents();

        $input = ['type' => 'multiple', 'colloque_id' => 71, 'user_id' => 1, 'participant' => ['Jane Doe', 'John Doa'], 'price_id' => [290, 290] ];

        $inscription = new \App\Droit\Inscription\Entities\Inscription();
        $group       = new \App\Droit\Inscription\Entities\Groupe();
        $group->id   = 1;

        $this->groupe->shouldReceive('create')->andReturn($group);
        $this->mock->shouldReceive('create')->times(2)->andReturn($inscription);

        $response = $this->call('POST', '/admin/inscription',$input);

        $this->assertRedirectedTo('/admin/inscription/colloque/71');

    }

    public function testLastInscriptions()
    {
        $inscriptions = factory(\App\Droit\Inscription\Entities\Inscription::class, 2)->make();

        $this->mock->shouldReceive('getAll')->once()->andReturn($inscriptions);

        $response = $this->call('GET', 'admin/inscription');

        $this->assertViewHas('inscriptions');
    }


}
