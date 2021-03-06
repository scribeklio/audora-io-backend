<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use Response;
use Illuminate\Support\Facades\Validator;
use Purifier;

class ArticlesController extends Controller
{
//index function -get list of articles
    public function index()
    {
      $articles = Article::all();

      return Response::json($articles);
    }

//store article - takes request param from front
    public function store(Request $request)
    {
      //create rules 4 req fields
      $rules = [
        'title' => 'required',
        'body' => 'required',
        'image' => 'required', //req in db so consist
      ];
      //pass data in
      $validator = Validator::make(Purifier::clean ($request->all()), $rules);

      if ($validator->fails()) {
        return Response::json(["error" => "all fields required"]);
      }
      /*some folks would put below in else statement but it's not req, more syntax/situ pref */
      $article = new Article;
      $article->title = $request->input('title');
      $article->body = $request->input('body');
      $article->blurb = $request->input('blurb'); 
      $article->slug = $request->input('slug'); 
      $article->time = $request->date('created_at'); 

      $image=$request->file('image');
      $imageName= $image->getClientOriginalName();
      // move image to public-storage
      $image->move('storage/', $imageName);
      //storing link on server
      $article->image = $request->root().'/storage/'.$imageName;

      //line that actually saves to db
      $article->save();

      //send back success or error
      return Response::json(['success' => 'yooo!']);
    }

//update function 2 params id & request
    public function update($id, Request $request)
    {
      //query one specific via id, article var to hold
      $article = Article::find($id);

      $article->title = $request->input('title');
      $article->body = $request->input('body');
       $article->blurb = $request->input('blurb'); 
      $article->slug = $request->input('slug'); 
      $article->time = $request->date('created_at'); 

      $image=$request->file('image');
      $imageName= $image->getClientOriginalName();
      $image->move('storage/', $imageName);
      $article->image = $request->root().'/storage/'.$imageName;

      $article->save();

      return Response::json(['success' => 'Article Updated.']);
    }

//show individual article
    public function show($id)
    {
      $article = Article::find($id);

      return Response::json($article);
    }

//fetch lastest 3 articles

  public function latest()
  { 
    $_latest = Article::orderBy('id', 'desc')->take(3)->get();

    $latest = array_reverse($_latest); 

    return Response::json($latest); 
  }

//delete an article
    public function delete($id)
    {
      $article = Article::find($id);

      $article->delete();

      return Response::json(['success' => "Farewell!"]);
    }

}
