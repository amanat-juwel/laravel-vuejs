<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use Illuminate\Support\Facades\Hash;

class CategoryController extends Controller
{   
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');

        //apply ACL to all method
        //$this->authorize('isAdmin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize('isAdmin');
        if (\Gate::allows('isAdmin') || \Gate::allows('isAuthor')) {
            return Category::latest()->paginate(5);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request,[
            'name' => 'required|string|max:191|unique:categories',
        ]);

        $category = new Category;
        $category->name = $request['name'];
        $category->save();
        return $category;
    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $this->validate($request,[
            'name' => 'required|string|max:191',
        ]);

        $category = Category::find($id);
        $category->name = $request['name'];
        $category->update();
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');

        $category = Category::findOrFail($id);
        // delete the Category
        $category->delete();
        return ['message' => 'Category Deleted'];
    }

    public function search(){
        if ($search = \Request::get('q')) {
            $categories = Category::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $categories = Category::latest()->paginate(5);
        }
        return $categories;
    }
}
