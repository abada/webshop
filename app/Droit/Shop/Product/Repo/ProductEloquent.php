<?php namespace App\Droit\Shop\Product\Repo;

use App\Droit\Shop\Product\Repo\ProductInterface;
use App\Droit\Shop\Product\Entities\Product as M;

class ProductEloquent implements ProductInterface{

    protected $product;

    public function __construct(M $product)
    {
        $this->product = $product;
    }

    public function getAll(){

        return $this->product->with(array('categories','authors','domains','attributes'))->get();
    }

    public function getByCategorie($id){

        return $this->product->with(array('authors','attributes','categories'))->whereHas('categories', function($query) use ($id)
        {
            // Set the constraint on the tags
            $query->where('categorie_id', '=' ,$id);

        })->orderBy('created_at', 'DESC')->get();

    }

    public function find($id){

        return $this->product->where('id','=',$id)->with(array('categories','authors','domains','attributes'))->get()->first();
    }

    public function create(array $data){

        $product = $this->product->create(array(
            'title'           => $data['title'],
            'teaser'          => $data['teaser'],
            'image'           => $data['image'],
            'description'     => $data['description'],
            'weight'          => $data['weight'],
            'sku'             => $data['sku'],
            'price'           => $data['price'],
            'is_downloadable' => $data['is_downloadable'],
            'created_at'      => date('Y-m-d G:i:s'),
            'updated_at'      => date('Y-m-d G:i:s')
        ));

        if( ! $product )
        {
            return false;
        }

        return $product;

    }

    public function update(array $data){

        $data['hidden'] = (isset($data['hidden']) && $data['hidden'] ? 1 : 0);

        $product = $this->product->findOrFail($data['id']);

        if( ! $product )
        {
            return false;
        }

        $product->fill($data);

        $product->save();

        return $product;
    }

    public function delete($id){

        return $this->product->delete($id);

    }

}
