<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    //
     public function index()
    {
        //
        $post=Post::with('user')
    ->where('is_draft', false)
    ->where('published_at', '<=', now())
    ->paginate(20);
        return response()->json([
            'data'=>$post->items(),
            'meta'=>[
                'current_page'=>$post->currentPage(),
                'last_page'=>$post->lastPage(),
                'per_page'=>$post->perPage(),
                'total'=>$post->total(),
            ]
        ]);
    }

     public function show(string $id)
    {
        $post = Post::with('user')
            ->where('is_draft', false)
            ->where('published_at', '<=', now())
            ->find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        };
         return response()->json([
        'data' => [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'author' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
            ],
            'published_at' => $post->published_at,
        ]
    ]);
    }
     public function create(){
        if(!Auth::check()){
            return response()->json(['message'=>'User is not authenticated'],401);
        }
        return response()->json(['message'=>'User is authenticated'],200);
    }
    public function store(Request $request)
    {
        if(!Auth::check()){
            return response()->json(['message'=>'User is not authenticated'],401);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_draft' => 'required|boolean',
            'published_at' => 'required|date',
        ]);
        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_draft' => $validated['is_draft'],
            'published_at' => $validated['published_at'],
        ]);
        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }
    public function update(Request $request, $id)
    {
        if(!Auth::check()){
            return response()->json(['message'=>'User is not authenticated'],401);
        }
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'is_draft' => 'sometimes|required|boolean',
            'published_at' => 'sometimes|required|date',
        ]);
        $post->update($validated);
        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }
    public function destroy($id){
        if(!Auth::check()){
            return response()->json(['message'=>'User is not authenticated'],401);
        }
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $post->delete();
        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}
