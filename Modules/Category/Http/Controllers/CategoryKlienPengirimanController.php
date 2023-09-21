<?php

namespace Modules\Category\Http\Controllers;

use App\Models\GlobalKlienPengiriman;
use App\Models\Periode;
use Illuminate\Http\Request;
use Modules\Category\Datatables\KlienPengirimanDatatables;
use DB;
use Modules\Category\Models\CategoryKlienPengiriman;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class CategoryKlienPengirimanController extends Controller
{
    public function syncKlienPengiriman(Request $request){
        $data = json_decode($request->data_not_sync);
        $data = get_object_vars($data);

        $data = array_map(function($data) {
            $check = GlobalKlienPengiriman::where('klien_pengiriman', $data)->first();
            if($check == 0) {
                return array(
                    'klien_pengiriman' => $data,
                );
            }

        }, $data);

        $insert = GlobalKlienPengiriman::insert($data);
        // dd($insert);
        //getall distinct each periode klien pengiriman
        //compact all klien pengiriman

        //compare current list klien pengiriman
        //list all klien pengiriman which is need to be imported
        return redirect()->back()->with('success', 'Sukses mengsinkronasikan klien pengiriman');
    }

    public function index(){
        ladmin()->allows(['ladmin.category.index']);

        // if( request()->has('datatables') ) {
        //     return KlienPengirimanDatatables::renderData();
        // }
        $periode = Periode::get();
        $klien_pengiriman_cashback = [];

        foreach($periode as $item) {
            //get all distict klien pengiriman
            $klien_pengiriman = DB::table($item->code.'.data_mart')->selectRaw("DISTINCT(klien_pengiriman)")->get()->pluck('klien_pengiriman')->toArray();
            $klien_pengiriman_cashback = array_merge($klien_pengiriman_cashback, $klien_pengiriman);
        }

        $klien_pengiriman_cashback = array_unique($klien_pengiriman_cashback);
        $data['klien_pengiriman'] = GlobalKlienPengiriman::orderBy('klien_pengiriman')->get()->pluck('klien_pengiriman');
        $data['list_klien_pengiriman'] = GlobalKlienPengiriman::orderBy('klien_pengiriman')->get();
        $data['not_sync'] = array_diff($klien_pengiriman_cashback, $data['klien_pengiriman']->toArray());
        $data['category'] = CategoryKlienPengiriman::with('klien_pengiriman')->get();

        return view('category::index', $data);
    }

    public function saveSetting(Request $request) {

        $setting = [];

        foreach($request->klien_pengiriman as $key => $item){
            // do while
            $x = 1;
            $length = count($item);
            foreach($item as $catId => $catItem){
                if(intval($catItem)){
                    $check = DB::table('category_klien_pengiriman')->where(['category_id' => $catId, 'klien_pengiriman_id' => $key])->get();
                    if($check){
                        $setting[] = [
                            'category_id' => $catId,
                            'klien_pengiriman_id' => $key
                        ];
                    }
                    //insert into category_klien_pengiriman
                }
            }
        }

        $saveSetting = DB::table('category_klien_pengiriman')->insert($setting);
         if($saveSetting) {
             toastr()->success('Data setting kategori klien pengiriman has been saved successfully!', 'Congrats');
         } else {
            toastr()->error('The Data not saved, please try again', 'Opps!');
         }

        return redirect()->back();
    }

    public function show(){

    }

    public function create(){

    }

    public function store() {

    }

    public function edit() {

    }

    public function update() {

    }

    public function destroy() {

    }
}
