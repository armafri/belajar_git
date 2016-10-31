<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;

use App\Product;
use App\Warehouse;
use App\Location;
use App\Unit;
use App\Stock;
use App\ListStock;

use Session;
use Carbon\Carbon;
use Auth;
use Datatables;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('stock.index');
    }


    /**
     * Datatables Ajax Data
     *
     * @return mixed
     */
    public function anyData() {

        /*$stockDb = Stock::select([
            'id',
            'product_id',
            'created_at',
            'updated_at'
        ])  
            ->with('product')
            ->with('stock')->select('log_add_stock.*')
            
            ->get();

        //dd($stockDb);


        return Datatables::of($stockDb)
                        ->addColumn('action', function ($stockDb) {
                                                 return ' <a href="' . url('stock/form/update', $stockDb->id) . '" id="tooltip" title="edit"><span class="label label-warning"><i class="fa fa-edit"></i></span></a>
                                                    | <a href="#" data-href="' . url('stock/action/remove', $stockDb->id) . '" id="tooltip" data-title="Delete" data-toggle="modal" data-target="#delete" title="delete"><span class="label label-danger"><i class="fa fa-trash-o"></i></span></a>';
                        })

                         ->addColumn('location', function ($stockDb) {
                                return $stockDb->stock->map(function($stock) {
                                    return '<a href="'.$stock->location_id.'" class="btn btn-primary">'.$stock->location_id.'</a>';
                                })->implode('<br>');
                            })
                        
                        ->make(true);*/

        $stockDb = ListStock::select([
            'product_id',
            'location_id',
            'qty',
            'unit_id',
            'log_add_stock_id'
        ])  
            ->with('product')
            ->with('location')
            ->get();

        //dd($stockDb);

             return Datatables::of($stockDb)
                        ->addColumn('action', function ($stockDb) {
                                                 return ' <a href="#" id="tooltip" title="edit"><span class="label label-warning"><i class="fa fa-edit"></i></span></a>
                                                    | <a href="#" data-href="#" id="tooltip" data-title="Delete" data-toggle="modal" data-target="#delete" title="delete"><span class="label label-danger"><i class="fa fa-trash-o"></i></span></a>';
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
    	//Get Product
        $DBproduct = Product::select(['id','code','name','descr','spec'])
                            ->orderBy('code','asc')
                            ->get();
        //Get Warehouse
        $DBwarehouse = Warehouse::select([
            'id',
            'name',
            'code',
            'country_id',
            'created_at',
            'updated_at'
        ])  

        	->orderBy('name','asc')
        	->orderBy('code','asc')
            ->get();


        /*//Get Location
        $DBlocation = Location::select([
            'id',
            'warehouse_id',
            'name',
            'created_at',
            'updated_at',
        ])  
        	->orderBy('name','asc')
            ->get();
*/
        $warehouseID = Input::get('warehouseID');

        if($warehouseID){
            $locationDb = Location::where('warehouse_id', $warehouseID)
                                  ->get();
        }else{
            $locationDb = Location::select([
                'id',
                'warehouse_id',
                'name',
                'created_at',
                'updated_at',
            ])  
                ->orderBy('name','asc')
                ->get();
        }
        


        //Get Unit
        $DBunit = Unit::select([
            'id',
            'iso',
            'name',
            'created_by',
            'updated_by',
        ])
            ->orderBy('name','asc')
            ->get();


        return view('stock.create')->with(array(
        	'DBproduct'		=>	$DBproduct,
        	'DBwarehouse'	=>	$DBwarehouse,
            'locationDb'    =>  $locationDb,
        	'DBunit'	=>	$DBunit,
        ));


        
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function move()
    {
       

        return view('stock.move');


        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'location_id.*' => 'required',
            'quantity.*' => 'required',
            'unit_id.*' => 'required',
        ]);

        $data = new Stock();
        $data->product_id = $request->product_id;
        $data->created_by = Auth::user()->name;
        $data->updated_by = Auth::user()->name;

        $data->save();

        if($data->save())
        {
            $lastID = $data->id;

            $jumlah = count($request->location_id);

            for ($i=0; $i < $jumlah; $i++) { 

                $locationID = $request->location_id[$i];
                $quantityXX = $request->quantity[$i];
                $unitID = $request->unit_id[$i];

                $listStock = new ListStock();
                $listStock->location_id = $locationID;
                $listStock->qty = $quantityXX;
                $listStock->unit_id = $unitID;
                $listStock->log_add_stock_id = $lastID;
                $listStock->product_id = $request->product_id;

                $listStock->save();

            }
        }

        Session::flash("flash_notification", [
                "level" => "success",
                "message" => "Data has been saved",
        ]);

        return redirect('stock');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update Form
     *
     * @param $id
     *
     * @return $this
     */
    public function updateForm($id) {
        $data = Warehouse::find($id);
        
        if (empty($data)) {
            return $this->failed('warehouse');
        }
        
        return view('warehouse.update')->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:warehouse,id,:id',
            'code' => 'required|unique:warehouse,id,:id',
            'country_id' => 'required',
        ]);

        $data = Warehouse::find($request->warehouse_id);
        
        if (empty($data)) {
            return $this->failed('warehouse');
        }
        
        $data->name = $request->name;
        $data->code = $request->code;
        $data->country_id = $request->country_id;
        $data->save();
        
        Session::flash("flash_notification", [
                "level" => "success",
                "message" => "Data has been updated",
        ]);
        
        return redirect('warehouse');
    }


    /**
     * Ajax to get sales name
     *
     * @param $id
     *
     * @return null
     */
    public function get_name($id) {
        $data = Warehouse::find($id);
        
        //Empty Data Get User Response
        if ($data) {
            return $data->name;
        } else {
            return null;
        }
    }


    /**
     * Remove Process
     *
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function remove($id) {
        $data = Warehouse::find($id);
        
        if (empty($data)) {
            return $this->failed('warehouse');
        }
        
        
        $data->delete();
        
        Session::flash("flash_notification", [
                "level" => "success",
                "message" => "Data has been updated",
        ]);
        
        return redirect('warehouse');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Ajax to get location
     *
     * @param $id
     *
     * @return null
     */
    public function get_location() {
        $warehouseID = Input::get('warehouseID');


        $html = null;
        if($warehouseID) {

            $locationDb = Location::where('warehouse_id', $warehouseID)
                                  ->get();

            return $locationDb;
        }


    }
}
