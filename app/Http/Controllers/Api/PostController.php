<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $data['posts'] = Post::with('user:id,name')->get();
        return $this->api_response(true, 'Posts fetched successfully', $data);
    }
    public function getUserPosts($userId)
{
    $user = User::find($userId);

    if (!$user) {
        return $this->api_response(false, 'User not found', [], 404);
    }

    $posts = Post::with('user:id,name')
        ->where('user_id', $userId)
        ->get();

    return $this->api_response(true, 'Posts fetched successfully', ['user' => $user->name, 'posts' => $posts]);
}

    public function show($id)
    {
        $post = Post::with('user:id,name')->find($id);
        if (!$post) {
            return $this->api_response(false, 'Post not found', [], 404);
        }
        return $this->api_response(true, 'Post fetched successfully', ['post' => $post]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->api_response(false, 'Validation error', ['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = auth()->id();

        $post = Post::create($data);

        return $this->api_response(true, 'Post created successfully', ['post' => $post]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post || $post->user_id !== auth()->id()) {
            return $this->api_response(false, 'Post not found or unauthorized', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return $this->api_response(false, 'Validation error', ['errors' => $validator->errors()], 422);
        }

        $post->update($validator->validated());

        return $this->api_response(true, 'Post updated successfully', ['post' => $post]);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post || $post->user_id !== auth()->id()) {
            return $this->api_response(false, 'Post not found or unauthorized', [], 403);
        }

        $post->delete();

        return $this->api_response(true, 'Post deleted successfully',[]);
    }
}
