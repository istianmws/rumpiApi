<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Comment;
use App\Models\Feed;
use App\Models\Like;
use Illuminate\Http\Request;
use App\Models\Users;

class FeedController extends Controller
{
    public function index()
    {
        $feeds = Feed::with('user')->latest()->get();
        return response([
            'feeds' => $feeds
        ], 200);
    }
    //
    public function store(PostRequest $request)
    {
        $request->validated();
        auth()->user()->feeds()->create([
            'content' => $request->content
        ]);

        return response([
            'message' => 'succes'
        ], 201);
    }

    public function likePost($feed_id)
    {
        //cari id feed
        $feed = Feed::whereId($feed_id)->first();
        //cek apakah feed ada
        if (!$feed) {
            return response([
                'message' => 'feed not found'
            ], 404);
        }

        //unlike post
        $unlikedPost = Like::where('user_id', auth()->id())->where('feed_id', $feed_id)->delete();
        if ($unlikedPost) {
            return response([
                'message' => 'batal like'
            ], 200);
        }

        //like post
        $likePost = Like::create([
            'user_id' => auth()->id(),
            'feed_id' => $feed_id
        ]);
        if ($likePost) {
            return response([
                'message' => 'like success'
            ], 200);
        }
        

        // if ($feed->user_id == auth()->id()) {
        //     Like::whereFeedId($feed_id)->delete();
        //     return response([
        //         'message' => 'batal like'
        //     ], 200);
        // } else {
        //     Like::create([
        //         'user_id' => auth()->id(),
        //         'feed_id' => $feed_id
        //     ]);
        //     return response([
        //         'message' => 'like success'
        //     ], 200);
        // }
    }

    public function comment(Request $request, $feed_id)
    {
        $request->validate([
            'body' => 'required'
        ]);

        // $feed = Feed::whereId($feed_id)->first();
        // if (!$feed) {
        //     return response([
        //         'message' => 'feed not found'
        //     ], 404);
        // }

        $comment= Comment::create([
            'user_id' => auth()->id(),
            'feed_id' => $feed_id,
            'body' => $request->body
        ]);

        return response([
            'message' => 'comment success'
        ], 201);
    }
    public function getComments($feed_id){
        $comments = Comment::with('feed')->with('user')->whereFeedId($feed_id)->latest()->get();

        return response([
            'comments' => $comments
        ], 200);
    }
}
