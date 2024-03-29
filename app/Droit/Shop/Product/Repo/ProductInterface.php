<?php namespace App\Droit\Shop\Product\Repo;

interface ProductInterface {

    public function getAll();
    public function getSome($ids);
	public function getByCategorie($id);
	public function find($data);
	public function create(array $data);
	public function update(array $data);
	public function delete($id);

}

