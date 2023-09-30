<?php

namespace Modules\DropPointOutgoing\Http\Controllers;

use App\Models\GlobalDropPointOutgoing;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DropPointController extends Controller
{
    public function index(){
        ladmin()->allows(['ladmin.droppointoutgoing.index']);

        $periode = Periode::get();
        $drop_point_outgoing = [];

        foreach($periode as $item) {
            //get all distict klien pengiriman
            $drop_point = DB::table($item->code.'.data_mart')->selectRaw("DISTINCT(drop_point_outgoing)")->get()->pluck('drop_point_outgoing')->toArray();
            $drop_point_outgoing = array_merge($drop_point_outgoing, $drop_point);
        }

        $drop_point_outgoing = array_unique($drop_point_outgoing);
        $data['drop_point_outgoing'] = GlobalDropPointOutgoing::orderBy('drop_point_outgoing')->get()->pluck('drop_point_outgoing');
        $data['not_sync'] = array_diff($drop_point_outgoing, $data['drop_point_outgoing']->toArray());

        return view('droppointoutgoing::index', $data);
    }

    public function syncDropPointOutgoing(Request $request){
        $data = json_decode($request->data_not_sync);
        $data = get_object_vars($data);

        $data = array_map(function($data) {
            $check = GlobalDropPointOutgoing::where('drop_point_outgoing', $data)->first();
            if($check == 0) {
                return array(
                    'drop_point_outgoing' => $data,
                );
            }

        }, $data);

        $insert = GlobalDropPointOutgoing::insert($data);
        return redirect()->back()->with('success', 'Sukses mengsinkronasikan drop point outgoing');
    }
}
