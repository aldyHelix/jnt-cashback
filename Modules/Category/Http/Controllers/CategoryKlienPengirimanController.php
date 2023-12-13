<?php

namespace Modules\Category\Http\Controllers;

use App\Models\GlobalKatResi;
use App\Models\GlobalKlienPengiriman;
use App\Models\GlobalMetodePembayaran;
use App\Models\KlienPengiriman;
use App\Models\Periode;
use App\Models\PeriodeKlienPengiriman;
use Illuminate\Http\Request;
use Modules\Category\Datatables\KlienPengirimanDatatables;
use DB;
use Modules\Category\Models\CategoryKlienPengiriman;

class CategoryKlienPengirimanController extends Controller
{
    public function syncKlienPengiriman(Request $request)
    {
        $data = json_decode($request->data_not_sync);
        $data = get_object_vars($data);

        $data = array_map(function ($data) {
            $check = GlobalKlienPengiriman::where('klien_pengiriman', $data)->first();
            if($check == 0) {
                return array(
                    'klien_pengiriman' => $data,
                );
            }

        }, $data);

        $insert = GlobalKlienPengiriman::insert($data);
        return redirect()->back()->with('success', 'Sukses mengsinkronasikan klien pengiriman');
    }

    public function syncMetodePembayaran(Request $request)
    {
        $data = json_decode($request->data_not_sync);
        $data = get_object_vars($data);

        $data = array_map(function ($data) {
            $check = GlobalMetodePembayaran::where('metode_pembayaran', $data)->first();
            if($check == 0) {
                return array(
                    'metode_pembayaran' => $data,
                );
            }

        }, $data);

        $insert = GlobalMetodePembayaran::insert($data);
        return redirect()->back()->with('success', 'Sukses mengsinkronasikan klien pengiriman');
    }

    public function syncKategoriResi(Request $request)
    {
        $data = json_decode($request->data_not_sync);
        $data = get_object_vars($data);

        $data = array_map(function ($data) {
            $check = GlobalKatResi::where('kat', $data)->first();
            if($check == 0) {
                return array(
                    'kat' => $data,
                );
            }

        }, $data);

        $insert = GlobalKatResi::insert($data);
        return redirect()->back()->with('success', 'Sukses mengsinkronasikan klien pengiriman');
    }

    public function importKlienPengiriman(Request $request)
    {
        $periode_id = $request->periode_id;
        $get_global_klien_pengiriman = DB::table('category_klien_pengiriman')->get();

        $periode_klien_pengiriman = $get_global_klien_pengiriman->map(function ($data) use ($periode_id) {
            return [
                'periode_id' => intval($periode_id),
                'category_id' => $data->category_id,
                'klien_pengiriman_id' => $data->klien_pengiriman_id
            ];
        });

        PeriodeKlienPengiriman::insert($periode_klien_pengiriman->toArray());

        return redirect()->back()->with('success', 'Sukses import klien pengiriman');
    }

    public function index()
    {
        ladmin()->allows(['ladmin.category.index']);

        // if( request()->has('datatables') ) {
        //     return KlienPengirimanDatatables::renderData();
        // }
        $periode = Periode::get();
        $klien_pengiriman_cashback = [];
        $metode_pembayaran = [];
        $kat_resi = [];

        foreach($periode as $item) {
            //get all distict klien pengiriman
            $klien_pengiriman = DB::table($item->code . '.data_mart')->selectRaw("DISTINCT(klien_pengiriman)")->get()->pluck('klien_pengiriman')->toArray();
            $klien_pengiriman_cashback = array_merge($klien_pengiriman_cashback, $klien_pengiriman);

            //get all distict metode pembayaran
            $metode_pembayaran_list = DB::table($item->code . '.data_mart')->selectRaw("DISTINCT(metode_pembayaran)")->get()->pluck('metode_pembayaran')->toArray();
            $metode_pembayaran = array_merge($metode_pembayaran, $metode_pembayaran_list);

            //get all distict resi
            $kat_list = DB::table($item->code . '.data_mart')->selectRaw("DISTINCT(kat)")->get()->pluck('kat')->toArray();
            $kat_resi = array_merge($kat_resi, $kat_list);
        }

        $klien_pengiriman_cashback = array_unique($klien_pengiriman_cashback);
        $kat_resi = array_unique($kat_resi);
        $metode_pembayaran = array_unique($metode_pembayaran);
        $data['klien_pengiriman'] = GlobalKlienPengiriman::orderBy('klien_pengiriman')->get()->pluck('klien_pengiriman');
        $data['metode_pembayaran_list'] = GlobalMetodePembayaran::orderBy('metode_pembayaran')->get()->pluck('metode_pembayaran');
        $data['kat_list'] = GlobalKatResi::orderBy('kat')->get()->pluck('kat');
        $data['list_klien_pengiriman'] = GlobalKlienPengiriman::orderBy('klien_pengiriman')->get();
        $data['not_sync'] = array_diff($klien_pengiriman_cashback, $data['klien_pengiriman']->toArray());
        $data['metode_pembayaran_not_sync'] = array_diff($metode_pembayaran, $data['metode_pembayaran_list']->toArray());
        $data['kat_not_sync'] = array_diff($kat_resi, $data['kat_list']->toArray());
        $data['category'] = CategoryKlienPengiriman::with('klien_pengiriman')->get();
        $data['periode'] = $periode;

        return view('category::index', $data);
    }

    public function saveSetting(Request $request)
    {

        $setting = [];

        foreach($request->klien_pengiriman as $key => $item) {
            // do while
            $x = 1;
            $length = count($item);
            foreach($item as $catId => $catItem) {
                if(intval($catItem)) {
                    $check = DB::table('category_klien_pengiriman')->where(['category_id' => $catId, 'klien_pengiriman_id' => $key])->first();
                    if(!$check) {
                        $setting[] = [
                            'category_id' => $catId,
                            'klien_pengiriman_id' => $key
                        ];
                    }
                //insert into category_klien_pengiriman
                } else {
                    $query = DB::table('category_klien_pengiriman')->where(['category_id' => $catId, 'klien_pengiriman_id' => $key]);
                    $check = $query->first();
                    if($check) {
                        $query->delete();
                    }
                    //kalau 1 jadi 0
                    //atau dari dicentang ke tidak dicentang
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

    public function show() {}

    public function storeKategori(Request $request)
    {
        $saveSetting = CategoryKlienPengiriman::create([
            'nama_kategori' => strtoupper($request->nama_kategori),
            'kode_kategori' => strtolower(str_replace(" ", "_", $request->nama_kategori)),
            'metode_pembayaran' => implode(";", $request->metode_pembayaran),
            'kat' => implode(";", $request->kat)
        ]);

        if($saveSetting) {
            toastr()->success('Data setting kategori klien pengiriman has been saved successfully!', 'Congrats');
        } else {
            toastr()->error('The Data not saved, please try again', 'Opps!');
        }

        return redirect()->back();
    }

    public function updateKategori(Request $request, $id)
    {
        $data = CategoryKlienPengiriman::where('id', $id)->first();

        $saveSetting = $data->update([
            'nama_kategori' => strtoupper($request->nama_kategori),
            'kode_kategori' => strtolower(str_replace(" ", "_", $request->nama_kategori)),
            'metode_pembayaran' => implode(";", $request->metode_pembayaran),
            'kat' => implode(";", $request->kat)
        ]);

        if($saveSetting) {
            toastr()->success('Data setting kategori klien pengiriman has been saved successfully!', 'Congrats');
        } else {
            toastr()->error('The Data not saved, please try again', 'Opps!');
        }

        return redirect()->back();
    }

    public function storeKlienPengiriman(Request $request)
    {
        $saveSetting = KlienPengiriman::create([
            'klien_pengiriman' => $request->klien_pengiriman,
        ]);

        if($saveSetting) {
            toastr()->success('Data setting klien pengiriman has been saved successfully!', 'Congrats');
        } else {
            toastr()->error('The Data not saved, please try again', 'Opps!');
        }

        return redirect()->back();
    }

    public function importToPeride($periodId)
    {

        return redirect()->back();
    }

    public function store() {}

    public function edit() {}

    public function update() {}

    public function destroy() {}
}
