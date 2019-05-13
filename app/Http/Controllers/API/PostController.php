<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use DB;
use Illuminate\Support\Facades\Hash;

class PostController extends Controller
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
            return 
            DB::table('posts')
            ->leftJoin('categories','categories.id','=','posts.category_id')
            ->selectRaw('posts.*,categories.name as category')
            ->orderBy('id','dsc')
            ->paginate(5);
        
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
            'title' => 'required|string|max:191|unique:posts',
        ]);

        $user = auth('api')->user();

        $post = new Post;
        $post->author_id = $user->id;
        $post->title = $request['title'];
        $post->body = $request['body'];
        $post->category_id = $request['category_id'];
        $post->save();
        return $post;
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
        $post = Post::findOrFail($id);

        $this->validate($request,[
            'title' => 'required|string|max:191',
        ]);

        $post = Post::find($id);
        $post->title = $request['title'];
        $post->body = $request['body'];
        $post->category_id = $request['category_id'];
        $post->update();
        return $post;
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

        $post = Post::findOrFail($id);
        // delete the Post
        $post->delete();
        return ['message' => 'Post Deleted'];
    }

    public function search(){
        if ($search = \Request::get('q')) {
            // $posts = Post::where(function($query) use ($search){
            //     $query->where('name','LIKE',"%$search%");
            // })->paginate(20);
            return DB::table('posts')
            ->leftJoin('categories','categories.id','=','posts.category_id')
            ->selectRaw('posts.*,categories.name as category')
            ->where('title','like',"%$search%")
            ->orderBy('id','dsc')
            ->paginate(5);

        }else{
            return DB::table('posts')
            ->leftJoin('categories','categories.id','=','posts.category_id')
            ->selectRaw('posts.*,categories.name as category')
            ->orderBy('id','dsc')
            ->paginate(5);
        }
        return $posts;
    }
}
