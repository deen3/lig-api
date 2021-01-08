<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Comments;
use App\Models\Posts;

class CommentsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $post)
    {
        $body_content = json_decode($request->getContent());
          
        $array_data = (array)$body_content;
        $validator = Validator::make($array_data, [
            'body' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages()
            ], 422);
        }

        $post_id = Posts::where('slug', $post)->firstOrFail('id');

        if (!$post_id) {
            return response()->json(['error' => $post_id], 404);
        } else {
            $comment = new Comments;
            $comment->body = $body_content->body;
            $comment->commentable_id = $post_id->id;
            $comment->commentable_type = 'App\Models\Posts';
            $comment->creator_id = $request->user()->id;
            
            if(!$comment->save()){
                return response()->json(['error' => "Error in saving"], 500);
            } else {
                $comment = Comments::find($comment->id);
                return response()->json(['data' => $comment], 200);
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
        $post = Posts::where('slug', $slug)->first('id');
        if ($post)
            return response()->json(['data' => $post->comments], 200);
        else
            return response()->json(['data' => $post], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $post, $comment_id)
    {
        $body_content = json_decode($request->getContent());

        $comment = Comments::find($comment_id);
        $comment->body = $body_content->body;
        if(!$comment->save()){
            return response()->json(['error' => "Error in saving"], 500);
        } else {
            $comment = Comments::find($comment_id);
            return response()->json(['data' => $comment], 200);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($post, $comment)
    {
        $comment = Comments::find($comment);

        if ($comment) {
            if(!$comment->delete()){
                return response()->json(['error' => "Error in saving"], 500);
            } else {
                return response()->json(['status' => 'record deleted successfully'], 200);
            }
        } else {
            return response()->json(['error' => "Cannot find data to delete"], 422);
        }
         
    }
}
