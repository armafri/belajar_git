<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Blog;
class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return IlluminateHttpResponse
     */
    public function index()
    {
        // we will create index function
        // we need to show all data from "blog" table
        // $blogs = Blog::all();
        // first, pagination using query builder
        // $blogs = DB::table('blog_post')->paginate(2);

        // pagination using Eloquent
        $blogs = Blog::paginate(6);

        // show data to our view
        return view('blog.index',['blogs' => $blogs]);
    } 

    /**
     * Show the form for creating a new resource.
     *
     * @return IlluminateHttpResponse
     */
   public function create()
    {
        // we will return to our new views
        return view('blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  IlluminateHttpRequest  $request
     * @return IlluminateHttpResponse
     */
    public function store(Request $request)
    {
        // we will create validation function here
        $this->validate($request,[
            'title'=> 'required',
            'description' => 'required',
        ]);

        $blog = new Blog;
        $blog->title = $request->title;
        $blog->description = $request->description;
        // save all data
        $blog->save();
        //redirect page after save data
        return redirect('blog')->with('message','data hasbeen updated!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return IlluminateHttpResponse
     */
    public function show($id)
    {
        $blog = Blog::find($id);
        
        // return to 404 page
        if(!$blog){
          abort(404);
        }
        
        // display the article to single page
        return view('blog.detail')->with('blog',$blog);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return IlluminateHttpResponse
     */
    public function edit($id)
    {
        // edit function here
        $blog = Blog::find($id);

        // return to 404 page 
        if(!$blog){
          abort(404);
        }
        // display the article to single page
        return view('blog.edit')->with('blog',$blog);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  IlluminateHttpRequest  $request
     * @param  int  $id
     * @return IlluminateHttpResponse
     */
    public function update(Request $request, $id)
    {
      // we will create validation function here
      $this->validate($request,[
          'title'=> 'required',
          'description' => 'required',
      ]);

      $blog = Blog::find($id);
      $blog->title = $request->title;
      $blog->description = $request->description;
      // save all data
      $blog->save();
      //redirect page after save data
      return redirect('blog')->with('message','data hasbeen edited!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return IlluminateHttpResponse
     */
    public function destroy($id)
    {
        $blog = Blog::find($id);
        $blog->delete();
        return redirect('blog')->with('message','data hasbeen deleted!');
    }
}
