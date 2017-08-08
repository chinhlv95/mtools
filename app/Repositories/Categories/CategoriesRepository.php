<?php
namespace App\Repositories\Categories;
use App\Models\Category;

class CategoriesRepository implements CategoriesRepositoryInterface
{
    public function all()
    {
        return Category::all();
    }

    public function paginate($quantity)
    {
        return Category::paginate($quantity);
    }

    public function find($id)
    {
        return Category::find($id);
    }

    public function save($data)
    {
        $category = new Category();
        return $category->id;
    }

    public function delete($id)
    {
        Category::find($id)->delete();
    }

    public function update($data, $id)
    {
        $category = Category::find($id);
        // save project
        $category->save();
    }
}