<?php

namespace Modules\Processwizard\Http\Controllers;

use App\Models\GlobalDroppointoutgoing;
use Illuminate\Http\Request;
use App\Models\GlobalKatResi;
use App\Models\GlobalKlienPengiriman;
use App\Models\GlobalMetodePembayaran;
use App\Models\KlienPengiriman;
use App\Models\Periode;
use Modules\Category\Datatables\KlienPengirimanDatatables;
use DB;
use Modules\Category\Models\CategoryKlienPengiriman;
use Modules\Collectionpoint\Models\Collectionpoint;

class WizardController extends Controller
{
    public function index()
    {
        ladmin()->allows(['ladmin.processwizard.index']);

        return view('processwizard::index');
    }

    public function create()
    {
        $periode = Periode::get();
        $klien_pengiriman_cashback = [];

        foreach($periode as $item) {
            //get all distict klien pengiriman
            $klien_pengiriman = DB::table($item->code . '.data_mart')->selectRaw("DISTINCT(klien_pengiriman)")->get()->pluck('klien_pengiriman')->toArray();
            $klien_pengiriman_cashback = array_merge($klien_pengiriman_cashback, $klien_pengiriman);
        }

        $klien_pengiriman_cashback = array_unique($klien_pengiriman_cashback);

        $data['klien_pengiriman'] = GlobalKlienPengiriman::orderBy('klien_pengiriman')->get()->pluck('klien_pengiriman');
        $data['dp'] = GlobalDroppointoutgoing::orderBy('drop_point_outgoing')->get();
        $data['list_klien_pengiriman'] = GlobalKlienPengiriman::orderBy('klien_pengiriman')->get();
        $data['category'] = CategoryKlienPengiriman::with('klien_pengiriman')->get();
        $data['cp1'] = Collectionpoint::where('grading_pickup', grading_map(1))->orderBy('drop_point_outgoing', 'ASC')->get();
        $data['cp2'] = Collectionpoint::where('grading_pickup', grading_map(2))->orderBy('drop_point_outgoing', 'ASC')->get();
        $data['cp3'] = Collectionpoint::where('grading_pickup', grading_map(3))->orderBy('drop_point_outgoing', 'ASC')->get();

        $compact = ['data' => $data];

        return view('processwizard::create', $compact);
    }
}
