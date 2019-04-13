<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;

class PostController extends Controller
{

  /**
   * @param \Illuminate\Http\Request
   * 
   * @return \Illuminate\Http\Response
   */
  function index() {
    $posts = Post::all();

    return response()->json([
      'error' => false,
      'message' => 'all posts',
      'posts'   => $posts,
    ]);
  }

  function view($id) {
    $post = Post::find($id);

    if ($post) {
      return response()->json([
        'error' => false,
        'message' => 'Found your post',
        'post' => $post
      ]);
    }

    return response()->json([
      'error' => true,
      'message' => 'There is no post with id '.$id,
      'post' => null,
    ]);
  }

  /**
   * Create a Post
   *
   * @param  \Illuminate\Http\Request  $request
   * 
   * @return \Illuminate\Http\Response
   **/
  function create(Request $request) {
    $request->validate([
      'title' => 'required|max:250',
      'body' =>  'required',
    ]);

    $post = $request->only('title', 'body');
    $post['author_id'] = auth()->user()->id;

    $createdPost = Post::create($post);

    if ($createdPost) {
      return response()->json([
        'error' => false,
        'message' => 'Post created successfully',
        'post' => $createdPost,
      ], 201);
    }

    return response()->json([
      'error' => true,
      'message' => 'Could not create post. Make sure your request is correctly formed',
      'post' => null,
    ], 422);
  }

  /**
   * Update a post
   * 
   * @param \Illuminate\Http\Request $request
   * 
   * @return \Illuminate\Http\Response
   */
  function update(Request $request, $id) {
    $request->validate([
      'title' => 'required|max:250',
      'body' => 'required'
    ]);

    $post = Post::find($id);

    if (!$post) {
      return response()->json([
        'error' => true,
        'message' => 'Post with id '.$id.' does not exist',
        'post' => $request->all(),
      ]);
    }

    $post['title'] = $request->get('title');
    $post['body'] = $request->get('body');

    if ($post->save()) {
      return response()->json([
        'error' => false,
        'message' => 'Post updated successfully',
        'post' => $post,
      ]);
    }

    return response()->json([
      'error' => true,
      'message' => 'Something went wrong while trying to update the post',
      'post' => $post
    ], 500);
  }

  /**
   * Delete a post
   * 
   * @param int $id
   * 
   * @return \Illuminate\Http\Response
   */
  function delete($id) {
    $post = Post::find($id);

    if (!$post) {
      return response()->json([
        'error' => true,
        'message' => 'Delete failed! Post with id '.$id.' was not found.',
        'postId' => $id,
      ], 404);
    }

    $post->delete();

    return response()->json([
      'error' => false,
      'message' => 'deleted',
      'postId' => $id,
    ]);
  }
}
