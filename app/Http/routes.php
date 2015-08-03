<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

//Route::resource('product', 'ProductController');
Route::get('colloque', 'Frontend\Colloque\ColloqueController@index');
//Route::resource('colloque', 'Frontend\Colloque\ColloqueController');

Route::get('inscription/colloque/{id}', 'InscriptionController@index');
Route::resource('inscription', 'InscriptionController');

/* *
 * Shop routes for frontend shop
 * */
Route::get('shop', 'Frontend\Shop\ShopController@index');
Route::get('shop/product/{id}', 'Frontend\Shop\ShopController@show');

/* *
 * Checkout routes for frontend shop
 * */
Route::get('checkout/resume', 'Frontend\Shop\CheckoutController@resume');
Route::get('checkout/confirm', 'Frontend\Shop\CheckoutController@confirm');
Route::match(['get', 'post'],'checkout/send', 'Frontend\Shop\CheckoutController@send');

Route::resource('adresse', 'AdresseController');
Route::post('ajax/adresse/{id}', 'AdresseController@ajaxUpdate');

/* *
 * User profile routes
 * */
Route::get('profil', 'ProfileController@index');
Route::get('profil/orders', 'ProfileController@orders');
Route::get('profil/colloques', 'ProfileController@colloques');
Route::get('profil/inscription/{id}', 'ProfileController@inscription');

/* *
 * Cart routes for frontend shop
 * */
Route::post('cart/addProduct', 'Frontend\Shop\CartController@addProduct');
Route::post('cart/removeProduct', 'Frontend\Shop\CartController@removeProduct');
Route::post('cart/quantityProduct', 'Frontend\Shop\CartController@quantityProduct');
Route::post('cart/applyCoupon', 'Frontend\Shop\CartController@applyCoupon');

//Route::get('home', 'HomeController@index');


// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');
// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');
// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');
// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

/*
 * Test routes
 * */

Route::get('cartworker', function()
{
    $worker = \App::make('App\Droit\Shop\Cart\Worker\CartWorker');
    $coupon = \App::make('App\Droit\Shop\Coupon\Repo\CouponInterface');
    $order  = \App::make('App\Droit\Shop\Order\Repo\OrderInterface');
    $user   = \App::make('App\Droit\User\Repo\UserInterface');
    $inscription   = \App::make('App\Droit\Inscription\Repo\InscriptionInterface');


    $inscrit = $inscription->find(1);

/*    $generator = new \App\Droit\Generate\Pdf\PdfGenerator();

    $annexes   = $inscrit->colloque->annexe;
    // Generate annexes
    if(!empty($annexes))
    {
        foreach($annexes as $annexe)
        {
            $doc = $annexe.'Event';
            $generator->$doc($inscrit);
        }
    }*/

    //$pdf    = new App\Droit\Generate\Pdf\PdfGenerator();

    //$order_no = $order->find(21);
    //$create = new App\Jobs\CreateOrderInvoice($order_no);

    //$order_no = $order->find(6);
    //print_r($create->handle());
    //return $pdf->factureOrder(6,true);

    //return $pdf->bvEvent($inscrit,true);

    event(new App\Events\InscriptionWasRegistered($inscrit));

});

Route::get('notification', function()
{
    setlocale(LC_ALL, 'fr_FR.UTF-8');
    $date   = \Carbon\Carbon::now()->formatLocalized('%d %B %Y');
    $title  = 'Votre commande sur publications-droit.ch';
    $logo   = 'facdroit.png';
    $orders  = \App::make('App\Droit\Shop\Order\Repo\OrderInterface');

    $order = $orders->find(8);
    $order->load('products','user','shipping','payement');

    $duDate = $order->created_at->addDays(30)->formatLocalized('%d %B %Y');

    $products = $order->products->groupBy('id');

    $data = [
        'title'     => $title,
        'logo'      => $logo,
        'concerne'  => 'Commande',
        'order'     => $order,
        'products'  => $products,
        'date'      => $date,
        'duDate'    => $duDate
    ];

    return View::make('emails.shop.confirmation', $data);

});

Route::get('registration', function()
{
    setlocale(LC_ALL, 'fr_FR.UTF-8');

    $date   = \Carbon\Carbon::now()->formatLocalized('%d %B %Y');
    $title  = 'Votre inscription sur publications-droit.ch';
    $logo   = 'facdroit.png';

    $inscription = \App::make('App\Droit\Inscription\Repo\InscriptionInterface');
    $inscrit     = $inscription->find(1);

    $data = [
        'title'       => $title,
        'concerne'    => 'Inscription',
        'logo'        => $logo,
        'inscription' => $inscrit,
        'annexes'     => $inscrit->colloque->annexe,
        'date'        => $date,
    ];

    return View::make('emails.colloque.confirmation', $data);

});


Route::get('factory', function()
{

    $fakerobj = new Faker\Factory;
    $faker = $fakerobj::create();

    $repo = \App::make('App\Droit\Shop\Product\Repo\ProductInterface');

    for( $x = 1 ; $x < 11; $x++ )
    {
        $product = $repo->create(array(
            'title'           => $faker->sentence,
            'teaser'          => $faker->paragraph,
            'description'     => $faker->text,
            'image'           => 'img'.$x.'.jpg',
            'price'           => $faker->numberBetween(2000, 40000),
            'weight'          => $faker->numberBetween(200, 1000),
            'sku'             => $faker->numberBetween(5, 50),
            'is_downloadable' => (($x % 2) == 0 ? 1 : 0)
        ));
        
        echo '<pre>';
        print_r($product);
        echo '</pre>';
    }

});

Route::get('otherfactory', function()
{

    $users = factory(App\Droit\User\Entities\User::class, 10)->create();

    foreach($users as $user)
    {
        $addresse = factory(App\Droit\Adresse\Entities\Adresse::class)->create([
            'user_id'    => $user->id,
            'first_name' => $user->firstName,
            'last_name'  => $user->lastName,
            'email'      => $user->email,
        ]);
    }

    echo '<pre>';
    print_r($users);
    echo '</pre>';exit;

});


Route::get('convert', function()
{

    $temp = new \App\Droit\Colloque\Entities\Colloque_temp();

    $colloques = $temp->all();

    foreach($colloques as $colloque)
    {

        $new  = new \App\Droit\Colloque\Entities\Colloque();

        $new->id              = $colloque->id;
        $new->titre           = $colloque->titre;
        $new->soustitre       = $colloque->soustitre;
        $new->sujet           = $colloque->sujet;
        $new->start_at        = $colloque->start_at;
        $new->end_at          = $colloque->end_at;
        $new->active_at       = $colloque->active_at;
        $new->registration_at = $colloque->registration_at;
        $new->remarques       = $colloque->remarques;
        $new->visible         = $colloque->visible;
        $new->url             = $colloque->url;
        $new->compte_id       = $colloque->compte_id;

        if($colloque->typeColloque == 1){
            $new->bon = 1;
            $new->facture = 1;
        }

        if($colloque->typeColloque == 2){
            $new->bon = 0;
            $new->facture = 1;
        }

        if($colloque->typeColloque == 0){
            $new->bon = 0;
            $new->facture = 0;
        }

        $new->save();
    }

});


Route::get('myaddress', function()
{
    $repo = \App::make('App\Droit\Adresse\Repo\AdresseInterface');

    $adresse = $repo->create(array(
        'civilite_id'   => 2,
        'first_name'    => 'Cindy',
        'last_name'     => 'Leschaud',
        'email'         => 'cindy.leschaud@unine.ch',
        'company'       => 'Unine',
        'role'          => '',
        'profession_id' => 1,
        'telephone'     => '032 751 38 07',
        'mobile'        => '078 690 00 23',
        'fax'           => '',
        'adresse'       => 'Ruelle de l\'hôtel de ville 3',
        'cp'            => '',
        'complement'    => '',
        'npa'           => '2520',
        'ville'         => 'La Neuveville',
        'canton_id'     => 6,
        'pays_id'       => 208,
        'type'          => 1,
        'user_id'       => 1,
        'livraison'     => 1,
    ));
    
    echo '<pre>';
    print_r($adresse);
    echo '</pre>';

});

Event::listen('illuminate.query', function($query, $bindings, $time, $name)
{
    $data = compact('bindings', 'time', 'name');

    // Format binding data for sql insertion
    foreach ($bindings as $i => $binding)
    {
        if ($binding instanceof \DateTime)
        {
            $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
        }
        else if (is_string($binding))
        {
            $bindings[$i] = "'$binding'";
        }
    }

    // Insert bindings into query
    $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
    $query = vsprintf($query, $bindings);

    Log::info($query, $data);
});

