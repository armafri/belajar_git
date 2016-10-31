<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use File;

Use App\Salesman;

use Datatables;

use Session;

use Illuminate\Support\Facades\Input;

use Validator;

use Auth;


class SalesmanController extends Controller
{
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/

    public function index(){
        $hasil = Salesman::all();
        return view('salesman.index', ['salesman' => $hasil]);
    }
    
    public function getDataSalesman(request $request){
        DB::statement(DB::raw('set @rownum=0'));
        $salesman = Salesman::select([
            DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id',
            'city', 
            'name',
            'address',
            'state',
            'zip_code',
            'phone_office',
            'phone_fax',
            ]);
        $datatables = Datatables::of($salesman);
        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', 'whereRaw', '@rownum  + 1 like ?', ["%{$keyword}%"]);
        }/*<a href="salesman/'.$salesman->id.'/edit" class="btn btn-xs btn-primary">
                    <i class="glyphicon glyphicon-edit"></i> Edit
                </a>*/
            return $datatables
            ->addColumn('action', function ($salesman) {
                return '
                
                <a href="#" data-action="salesman/action/remove/'.$salesman->id.'" class="btn btn-danger btn-xs"
                    data-city ="'.$salesman->city.'" data-name ="'.$salesman->name.'"
                    data-toggle="modal" data-target="#confirm-delete">
                    <i class="glyphicon glyphicon-trash"></i>Delete 
                </a>

                <script>
                    $(function(){
                        $("#confirm-delete").on("show.bs.modal", function(e) {
                            $(this).find("#form_delete").attr("action", $(e.relatedTarget).data("action"));
                            $(this).find(".data_city").text($(e.relatedTarget).data("city"));
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
    public function create(){
        $hasil = Salesman::all();
        return view('salesman.create')->with(array('salesman'=>$hasil));
    }
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    /*public function store(Request $request){
        $this->validate($request,[
            'city'=> 'required',
            'name' => 'required',
            'address'=> 'required',
            'state'=> 'required',
            'zip_code'=> 'required',
            'phone_office'=> 'required|numeric',
            'phone_fax'=> 'required|numeric',
            'email'=> 'required|email',
            'fax'=> 'required',
            'logo'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $salesman = new Salesman;
        $salesman->city = $request->city;
        $salesman->name = $request->name;
        $salesman->address = $request->address;
        $salesman->state = $request->state;
        $salesman->zip_code = $request->zip_code;
        $salesman->phone_office = $request->phone_office;
        $salesman->phone_fax = $request->phone_fax;
        $salesman->email = $request->email;
        $salesman->fax = $request->fax;
        if (isset($request->logo)) {
            $imageName = time().'.'.$request->logo->getClientOriginalExtension();
            $request->logo->move(public_path('images'), $imageName);
            $salesman->logo = $imageName;
        }
        $salesman->created_by = 'dummy1';
        // save all data
        $salesman->save();
        //redirect page after save data
        Session::flash('flash_notification', ['level' => 'success', 'message' => 'Salesman Successfully Added']);
        
        return $this->htmlOption($salesman->id);
    }*/

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id){
        $salesman = Salesman::find($id);

        // return to 404 page
        if(!$salesman){
            abort(404);
    }

    // display the article to single page
    return view('agen.detail')->with('agen',$salesman);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    /*public function edit($id){
    // edit function here
        $salesman = Salesman::find($id);

        // return to 404 page 
        if(!$salesman){
            abort(404);
        }
    // display the article to single page
        return view('salesman.edit')->with('salesman',$salesman);
    }*/

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    /*public function update(Request $request, $id){
    // we will create validation function here
        $this->validate($request,[
            'city'=> 'required',
            'name' => 'required',
            'address'=> 'required',
            'state'=> 'required',
            'zip_code'=> 'required',
            'phone_office'=> 'required|numeric',
            'phone_fax'=> 'required|numeric',
            'email'=> 'required|email',
            'fax'=> 'required',
            'logo'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $salesman = Salesman::find($id);
        $salesman->city = $request->city;
        $salesman->name = $request->name;
        $salesman->address = $request->address;
        $salesman->state = $request->state;
        $salesman->zip_code = $request->zip_code;
        $salesman->phone_office = $request->phone_office;
        $salesman->phone_fax = $request->phone_fax;
        $salesman->email = $request->email;
        $salesman->fax = $request->fax;
        // ==Proses Gambar== //
        if (file_exists('images/salesman_logo/'.$salesman->logo)) {
            File::delete('images/'.$salesman->logo);//menghapus gambar lama
            $imageName = time().'.'.$request->logo->getClientOriginalExtension();//membuat nama gambar baru
            $request->logo->move(public_path('images'), $imageName);//memindahkan gambar ke folder
            $salesman->logo = $imageName;//mengirim nama gambar ke database
        }
        // ==Proses Gambar== //

        $salesman->updated_by = Auth::user()->name;;
        // save all data
        $salesman->save();
        //redirect page after save data
        Session::flash('flash_notification', ['level' => 'info', 'message' => 'Salesman Successfully Edited']);
        return redirect('salesman');
    }*/

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id){
        $salesman = Salesman::find($id);
        File::delete('images/salesman_logo'.$salesman->logo);
        $salesman->delete();
        Session::flash('flash_notification', ['level' => 'danger', 'message' => 'Salesman Successfully Deleted']);
        return redirect('salesman');
    }

    // ==> INI BUAT YANG DI MODAL <== //
    private function htmlOption($idSalesman) {
        #Get all salesman
        $DBSalesman = Salesman::select([
            'id',
            'city',
            'name',
            'address',
            'state',
            'zip_code',
            'phone_office',
            'phone_fax',
            'email',
            'fax',
            'logo'
        ])
       ->orderBy('id', 'asc')
       ->get();
        
        #Get All Sales
        $html = null;
        
        $html .= '<select name="salesman_id" class="form-control " id="salesman_id">';

        if (count($DBSalesman) > 0) {
            foreach ($DBSalesman as $isi) {
                if ($isi->id == $idSalesman) {
                    $html .= '<option value="' . $isi->id . '" selected>' . $isi->name . '</option>';
                } else {
                    $html .= '<option value="' . $isi->id . '">' . $isi->name . '</option>';
                }
            }
        }
        
        $html .= '</select>';
        
        return $html;
    }

    public function createFormModal() {
        $hasil = Salesman::all();
        
        return view('salesman.create_modal');
    }
    public function insertModal(Request $request) {
        $rules = array(
            'city'=> 'required',
            'name' => 'required',
            'address'=> 'required',
            'state'=> 'required',
            'zip_code'=> 'required',
            'phone_office'=> 'required|numeric',
            'phone_fax'=> 'required|numeric',
            'email'=> 'required|email',
            'fax'=> 'required',
            'logo'=>'required|image|mimes:jpg,jpeg,png,gif'
        );
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            #Sent Form Validation Error
            return response()->json($validator->messages(), 500);
        }

        $salesman = new Salesman;
        $salesman->city = $request->city;
        $salesman->name = $request->name;
        $salesman->address = $request->address;
        $salesman->state = $request->state;
        $salesman->zip_code = $request->zip_code;
        $salesman->phone_office = $request->phone_office;
        $salesman->phone_fax = $request->phone_fax;
        $salesman->email = $request->email;
        $salesman->fax = $request->fax;
        $imageName = time().'_'.$request->logo->getClientOriginalName();
        $request->logo->move(public_path('images/salesman_logo'), $imageName);
        $salesman->logo = $imageName;
        $salesman->created_by = Auth::user()->name;
        $salesman->updated_by = Auth::user()->name;
        $salesman->save();
        return $this->htmlOption($salesman->id);
    }
     public function detailModal($id) {
        
        $salesman = Salesman::findOrFail($id);
    
        if (!$salesman) {
            return response()->json(array('Data Not Found'), 500);
        }
        
        return view('salesman.detail_modal')->with(array(
            'salesman' => $salesman));
    }
    public function updateFormModal($id) {
        $salesman    = Salesman::find($id);
    
        if (!$salesman) {
            return response()->json(array('Data Not Found'), 500);
        }
        
        return view('salesman.edit_modal')->with([
            'salesman' => $salesman
        ]);
    }
    
    /**
     * Update Modal Process
     *
     * @param updateRequest|Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateModal(Request $request) {

        
        $salesmanId = Input::get('salesman_id');
        
        $rules = array(
            'city'=> 'required',
            'name' => 'required',
            'address'=> 'required',
            'state'=> 'required',
            'zip_code'=> 'required',
            'phone_office'=> 'required|numeric',
            'phone_fax'=> 'required|numeric',
            'email'=> 'required|email',
            'fax'=> 'required',
            'logo'=>'image|mimes:jpg,jpeg,png,gif'
        );
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            #Sent Form Validation Error
            return response()->json($validator->messages(), 500);
        }
        
        $salesman = Salesman::find($salesmanId);
        
        if (!$salesman) {
            return response()->json(array('Data Not Found'), 500);
        }
        
        $salesman->city = $request->city;
        $salesman->name = $request->name;
        $salesman->address = $request->address;
        $salesman->state = $request->state;
        $salesman->zip_code = $request->zip_code;
        $salesman->phone_office = $request->phone_office;
        $salesman->phone_fax = $request->phone_fax;
        $salesman->email = $request->email;
        $salesman->fax = $request->fax;
        if (!empty($request->logo)) {
            $imageName = time().'_'.$request->logo->getClientOriginalName();
            $request->logo->move(public_path('images/salesman_logo'), $imageName);
            $salesman->logo = $imageName;
            if (file_exists('images/salesman_logo/'.$salesman->logo)) {
               File::delete('images/salesman_logo/'.$salesman->logo);
            }
        }
        $salesman->updated_by = Auth::user()->name;
        $salesman->save();
        return $this->htmlOption($salesman->id);
    }
}
