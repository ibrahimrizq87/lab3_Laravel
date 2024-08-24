<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon; 

use Illuminate\Http\Request;

use App\Http\Requests\createPostRequest;
use App\Http\Requests\updatePostRequest;

use Illuminate\Support\Facades\Auth;


class postsController extends Controller
{
  

    function __construct(){
        $this->middleware("auth")->only('create');
    }


    public function index()
    {
            return view("home");
        
    }

    public function main()
    {
        $posts = Post::all();
            return view("main", ["posts"=>$posts]);
        
    }


    public function showDetails()
    {
        $posts = Post::withTrashed()->paginate(1);
        return view('show', ['posts' => $posts]);
    }
    public function create()
    {

        // if (!Auth::check()) {
        //     return redirect()->route('login')->with('error', 'You need to be logged in to create a post.');
        // }

        $users = User::all();
        return view('create_post', compact('users'));

        
    }
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        $post->restore(); 
        return to_route("posts.details")->with('success', 'Post restored successfully.');

        
    }

    
    public function store(Request $request)
    {
        // dd($request);
        
        $my_path = '';
        $data = request()->all();

        if(request()->hasFile("image")){

            $image = request()->file("image");
            $my_path=$image->store('posts_images','posts_images');

        }


        $post = new Post();
        $post->creator_name =Auth::user()->name;
        $post->creator_id =Auth::id();
        $post->title = $data['title'];
        $post->create_date = Carbon::now()->toDateString();
        $post->content= $data['content'];
        $post->image= $my_path;
        $post->save();
        return  to_route("posts.details");

        
    }

    
    public function show(Post $post)
    {
        
  
        return view("get_post", ["post"=>$post ]);

    }

 
    public function edit(Post $post)
    {
        $users = User::all();
        // return view("edit_post", ["post"=>$post , "users"=> $users]);

        if (Auth::id() == $post->creator_id){
            return view("edit_post", ["post"=>$post , "users"=> $users]);

        }else{
            return redirect()->route('posts.details')
            ->with('error', 'You are not authorized to edit this post it is not yours.');        }
        
    }

   
  
   
    public function update(Request $request, Post $post)
    {

     
       


        $my_path = $post->image;
        $data = request()->all();

        if(request()->hasFile("image")){

            $image = request()->file("image");
            $my_path=$image->store('posts_images','posts_images');

        }
     



        $post->creator_name =Auth::user()->name;
        $post->creator_id =Auth::id();
        $post->title = $data['title'];
        $post->create_date = Carbon::now()->toDateString();
        $post->content= $data['content'];
        $post->image= $my_path;

      
        $post->save();

        return  to_route("posts.details");

        
    }
   

    public function destroy(Post $post)
    {
        $post->delete();
        return  to_route("posts.details");
    }
}
