<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Posts;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = Posts::paginate(
            5, 
            ['*'], 
            'page', 
            $_GET['page']
        );

        return $posts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $body_content = json_decode($request->getContent());
          
        $array_data = (array)$body_content;
        $validator = Validator::make($array_data, [
            'title' => 'required',
            'content' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages()
            ], 422);
        }
        
        $post = new Posts;
        $post->title = $body_content->title;
        $post->slug = str_slug($body_content->title, '-');;
        $post->content = $body_content->content;
        $post->image = $body_content->image;
        $post->user_id = $request->user()->id;
        
        try {
            $post->save();
            return response()->json(['data' => $post], 200);
        } catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1062'){
                return response()->json(['errors' => $e], 200);
            }
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post =  Posts::where('slug', $slug)->firstOrFail();

        if ($post) {
            return $post;
        } else {
            return response()->json(['data' => $post], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $post = Posts::where('slug', $slug)->firstOrFail();
        $post->title = $request->title;
        $post->slug = str_slug($request->title, '-');

        if(!$post->save()){
            return response()->json(['error' => "Error in saving"], 500);
        } else {
            return response()->json(['data' => $post], 200);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Posts::find($id);

        if ($post) {
            if(!$post->delete()){
                return response()->json(['error' => "Error in saving"], 500);
            } else {
                return response()->json(['status' => 'record deleted successfully'], 200);
            }     
        } else {
            return response()->json(['error' => "Cannot find record to delete"], 422);
        }

        
    }
}
