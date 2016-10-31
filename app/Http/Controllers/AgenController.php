<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;

Use App\Agen;

use Datatables;

use Session;

class AgenController extends Controller
{
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/

	public function index()
	{
		$hasil = Agen::paginate(6);
		return view('agen.index', ['Agen' => $hasil]);
	}

	// public function getAgen() 
	// {
	// 	return view('agen.index');
	// }
	
	public function getDataAgen(request $request)
	{
		DB::statement(DB::raw('set @rownum=0'));
		$agen = Agen::select([
			DB::raw('@rownum  := @rownum  + 1 AS rownum'),
			'id',
			'agent_code', 
			'name',
			'address',
			'country',
			'email',
			'phone']);
		$datatables = Datatables::of($agen);
		if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        }
			return $datatables
			->addColumn('action', function ($agen) {
				return '
				<a href="agen/'.$agen->id.'/edit" class="btn btn-xs btn-primary">
					<i class="glyphicon glyphicon-edit"></i> Edit
				</a>
				<a href="#" data-action="agen/'.$agen->id.'" class="btn btn-danger btn-xs"
					data-code ="'.$agen->agent_code.'" data-name ="'.$agen->name.'"
					data-toggle="modal" data-target="#confirm-delete">
					<i class="glyphicon glyphicon-trash"></i>Delete 
				</a>

				<script>
					$(function(){
						$("#confirm-delete").on("show.bs.modal", function(e) {
							$(this).find("#form_delete").attr("action", $(e.relatedTarget).data("action"));
							$(this).find(".data_code").text($(e.relatedTarget).data("code"));
							$(this).find(".data_name").text($(e.relatedTarget).data("name"));
						});
						$("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
							$("#success-alert").slideUp(500);
						});
					});
				</script>
				';
				})
				->make(true);
	}
		/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		return view('agen.create');
	}

	/**
	* Store a newly created resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function store(Request $request)
	{
		$this->validate($request,[
			'agent_code'=> 'required|unique:agent,agent_code,'.$request->agent_code,
			'name' => 'required',
			'address'=> 'required',
			'country' => 'required',
			'email' => 'required|email|unique:agent,email,'.$request->email,
			'phone' => 'required|numeric|digits_between:0,14',
		]);

		$agen = new Agen;
		$agen->agent_code = $request->agent_code;
		$agen->name = $request->name;
		$agen->address = $request->address;
		$agen->country = $request->country;
		$agen->email = $request->email;
		$agen->phone = $request->phone;
		// save all data
		$agen->save();
		//redirect page after save data
		Session::flash('flash_notification', ['level' => 'success', 'message' => 'Agen Successfully Added']);
		return redirect('agen');
	}

	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function show($id)
	{
		$agen = Agen::find($id);

		// return to 404 page
		if(!$agen){
			abort(404);
	}

	// display the article to single page
	return view('agen.detail')->with('agen',$agen);
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id)
	{
	// edit function here
		$agen = Agen::find($id);

		// return to 404 page 
		if(!$agen){
			abort(404);
		}
	// display the article to single page
		return view('agen.edit')->with('agen',$agen);
	}

	/**
	* Update the specified resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function update(Request $request, $id)
	{
	// we will create validation function here
		$this->validate($request,[
			'agent_code'=> 'required',
			'name' => 'required',
			'address'=> 'required',
			'country' => 'required',
			'email' => 'required|email',
			'phone' => 'required|numeric|digits_between:0,14',
		]);

		$agen = Agen::find($id);
		$agen->agent_code = $request->agent_code;
		$agen->name = $request->name;
		$agen->address = $request->address;
		$agen->country = $request->country;
		$agen->email = $request->email;
		$agen->phone = $request->phone;
		// save all data
		$agen->save();
		//redirect page after save data
		Session::flash('flash_notification', ['level' => 'info', 'message' => 'Agen Successfully Edited']);
		return redirect('agen');
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function destroy($id)
	{
		$agen = Agen::find($id);
		$agen->delete();
		Session::flash('flash_notification', ['level' => 'danger', 'message' => 'Agen Successfully Deleted']);
		return redirect('agen')->with('message','data hasbeen deleted!');
	}
}
