<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DB::table('categories')
                      ->where('parent', '=', 0)
                      ->get();
        return $categories;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function typeCheck($parentId){
        $result = $this->show($parentId); // Call your function

        $categories = $result['categories'];
        $length = count($categories);
        $type = null;
        if($length>0){
            foreach ($categories as $category) {
                $type = $category->type;
                break;
            }
        }
        return $type;

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'data' => 'required|string',
            'parent' => 'required|integer',
            'isSelect' => 'required|integer',
            'discount' => 'required|integer',
            'type' => 'required|in:category,item', // Add your allowed enum values here
        ]);

        $type=$this->typeCheck($request->parent);

        $this->levelCheckReverse($request->parent,0,$highestLevel);

        // return $highestLevel;

        if($highestLevel>=4){
            throw new \Exception("Four levels are occupied, can not create any sub category or item");
        }

        if(isset($type) && $type!=$request->type){
            throw new \Exception("Can not create, type '{$request->type}' is not accepted");
        }

        $categories = new Categories;
        $categories->data = $request->data;
        $categories->parent = $request->parent;
        $categories->isSelect = $request->isSelect;
        $categories->discount = $request->discount;
        $categories->type = $request->type;
        $categories->save();
        
        return new PostResource($categories);
    }

    public function levelCheckReverse($categoryId, $currentLevel, &$highestLevel){
        $categories = DB::table('categories')
        ->where('id', '=', $categoryId)
        ->get();

        $highestLevel = max($highestLevel, $currentLevel);

        foreach ($categories as $category) {
            $this->levelCheckReverse($category->parent,$currentLevel + 1, $highestLevel);
        }
    }

    public function levelCheck($categoryId, $currentLevel, &$highestLevel){
        $categories = DB::table('categories')
        ->where('parent', '=', $categoryId)
        ->get();

        $highestLevel = max($highestLevel, $currentLevel);

        foreach ($categories as $category) {
            $this->levelCheck($category->id,$currentLevel + 1, $highestLevel);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($categoryId)
    {
        $categories = DB::table('categories')
                      ->where('parent', '=', $categoryId)
                      ->get();
        $this->levelCheck($categoryId,1,$highestLevel);

        $subCategories=true;
        if($highestLevel>=4){
            $subCategories=false;
        }
       
        // not calling for all the subtrees because of larger dataset
        // foreach ($categories as $category) {
        //     $category->subcategories = $this->show($category->id);
        // }
        
        return ['categories' => $categories, 'subCategories' => $subCategories];
    }

    public function updateDiscount($categoryId,$discount){
        $categories = DB::table('categories')
                      ->where('parent', '=', $categoryId)
                      ->get();

        foreach ($categories as $category) {
            $subCategories = Categories::find($category->id);
            if($subCategories->isSelect == 0){
                $subCategories->discount+=$discount;
                $subCategories->isSelect=1;
                $subCategories->save();
            }
            $this->updateDiscount($category->id,$discount);
        }
    }

    public function discount(Request $request,$itemId){
        $categories = DB::table('categories')
                      ->where('id', '=', $itemId)
                      ->get();

        if($categories[0]->type=='item'){
            $categories = Categories::find($itemId);
            $categories->discount=$request->discount;
            if($request->discount==0){
                $categories->isSelect=0;
            }else{
                $categories->isSelect=1;
            }
            $categories->save();
        }else{
            if($categories[0]->parent==0){ //it is a root item
                $categories = Categories::find($itemId);
                $categories->discount=$request->discount;
                $categories->save();
                $this->updateDiscount($itemId,$request->discount);
            }else{
                $categories = Categories::find($itemId);
                $categories->discount+=$request->discount;
                $categories->save();
                $this->updateDiscount($itemId,$request->discount);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Discount added Successfully',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categories $categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $categoryId)
    {
        $categories = Categories::find($categoryId);
        
        if(isset($request->data)){
            $categories->data = $request->data;
        }
        if(isset($request->parent)){
            $categories->parent = $request->parent;
        }
        if(isset($request->isSelect)){
            $categories->isSelect = $request->isSelect;
        }
        if(isset($request->discount)){
            $categories->discount = $request->discount;
        }

        if(isset($request->type)){
            $categories->type = $request->type;
        }
        
        $categories->save();
        // $categories= Categories::update($request->all());
        
        return new PostResource($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($categoryId)
    {
        $categories = Categories::find($categoryId);
        $categories->delete();
        // return $categoryID;
        return response("deleted", 200);
    }
}