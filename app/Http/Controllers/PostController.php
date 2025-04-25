<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Requests\StorePostRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;
    public $posts = [];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Post::class);
        $posts = Post::with('comments')->get();
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        // validation
        $data = $request->validated();
        // $request->input()
        $newPost = [
            'id' => count($this->posts) + 1,
            ...$data
        ];
        array_push($this->posts, $newPost);
        return response()->json([
            'message' => 'Post created successfully',
            'data' => $this->posts
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = (object)[
            'id' => 1,
            'title' => 'Welcome to API Resources',
            'content' => 'This is a static post used to demonstrate Laravel resources.'
        ];
        return new PostResource($post);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id)
    {
        $this->authorize('update', Post::class);

        // validation
        $data = $request->validated();
        $selectedpost = array_filter($this->posts, function ($filterdpost) use ($id) {
            return $filterdpost['id'] == $id;
        });
        $post = array_values($selectedpost)[0];
        $post['title'] = $data['title'];
        $post['content'] = $data['content'];
        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Post::class);

        $this->posts = array_filter($this->posts, function ($filterdpost) use ($id) {
            return $filterdpost['id'] != $id; // ignore the post with the id
        });
        return response()->json([
            'message' => 'Post deleted successfully',
            'data' => $this->posts
        ]);
    }

    public function addcomment(Request $request, string $id){
        $data = $request->validate([
            'content' => 'required|string',
        ]);
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }
        $post->comments()->create([
            'content' => $data['content']
        ]);
        return response()->json([
            'message' => 'Comment added successfully',
            'data' => $post->comments,
        ]);
    }
}
