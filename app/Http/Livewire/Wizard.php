<?php

namespace App\Http\Livewire;

use App\Facades\fileProcessing;
use App\Facades\GeneratePivot;
use App\Facades\GeneratePivotTable;
use App\Models\Denda;
use App\Models\GlobalKlienPengiriman;
use App\Models\KlienPengiriman;
use App\Models\Periode;
use App\Models\PeriodeKlienPengiriman;
use App\Models\ProcessWizard;
use Livewire\Component;
use App\Models\Product;
use App\Models\SettingDpPeriode;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Modules\UploadFile\Models\UploadFile;

class Wizard extends Component
{

    use WithFileUploads;

    public $currentStep = 1;
    // public $name, $amount, $description,
    public $status = 1;
    public $successMessage = '';
    public $category;
    public $list_klien_pengiriman;
    public $dp = [];
    public $cp1, $cp2, $cp3;
    public $g1, $g2, $g3;

    public $month, $year, $item_category, $dp_setting, $periode ,$periode_id, $schema_name;
    public $files = [];
    public $global_klien_pengiriman, $global_drop_point;

    //process component
    public $file_processed, $row_total, $time_estimated, $work_load, $resi_error, $resi_tidak_terinput;
    public $state, $drop_point_setting, $grading_1_setting, $grading_2_setting, $grading_3_setting, $klien_pengiriman_setting;

    protected $listeners = ['refreshComponent' => '$refresh'];
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function render()
    {
        $data['period'] = Periode::get()->pluck('month', 'year');

        $data_klien_pengiriman = [];

        foreach($this->list_klien_pengiriman as $item) {
            foreach($item->category as $i){
                $data_klien_pengiriman[$item->id][$i->id] = $i->id;
            }
        }

        //error di viewnya

        foreach($this->dp as $key => $item) {
            $this->dp_setting[$key]['drop_point_outgoing'] = $item->drop_point_outgoing;
        }

        foreach($this->cp1 as $key => $item) {
            $this->g1[$key]['id'] = $item->id;
            $this->g1[$key]['kode_cp'] = $item->kode_cp;
        }

        foreach($this->cp2 as $key => $item) {
            $this->g2[$key]['id'] = $item->id;
            $this->g2[$key]['kode_cp'] = $item->kode_cp;
        }

        foreach($this->cp3 as $key => $item) {
            $this->g3[$key]['id'] = $item->id;
            $this->g3[$key]['kode_cp'] = $item->kode_cp;
        }

        $this->global_klien_pengiriman = $data_klien_pengiriman;

        return view('livewire.wizard', $data);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function firstStepSubmit()
    {

        $validatedData = $this->validate([
            'month' => 'required',
            'year' => 'required',
        ]);

        $check_periode = Periode::where(['month'=> $this->month, 'year' => $this->year])->first();
        if($check_periode) {
            //with error periode sudah ada
            $this->back(1);
        } else {
            $this->currentStep = 2;
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function secondStepSubmit()
    {
        $validatedData = $this->validate([
            'files.*' => 'required|mimes:csv,txt|max:200400',
        ]);

        $this->currentStep = 3;
    }

    public function thirdStepSubmit()
    {

        //setting klien pengiriman

        $this->currentStep = 4;
    }

    public function fourthStepSubmit()
    {
        //setting drop point
        $this->currentStep = 5;
    }

    public function fifthStepSubmit()
    {
        // grading 1

        $this->currentStep = 6;
    }

    public function sixthStepSubmit()
    {
        //grading 2

        $this->currentStep = 7;
    }

    public function seventhStepSubmit()
    {
        //grading 3

        // process page
        //create periode
        $create_periode = Periode::create([
            'code' => 'cashback_'.strtolower($this->month).'_'.$this->year,
            'month' => $this->month,
            'year' => $this->year,
            'status' => 'CREATED WIZARD',
            // 'processed_by' => n
        ]);

        $this->periode = $create_periode;

        // //create the state too
        $create_state = ProcessWizard::create([
            'periode_id' => $create_periode->id,
            'file_count' => count($this->files),
        ]);

        $this->periode_id = $create_periode->id;

        $klien_pengiriman_batch = [];

        foreach($this->global_klien_pengiriman as $key => $gkp) {
            $klien_pengiriman_id = $key;

            foreach($gkp as $i) {
                $category_id = $i;
            }

            $klien_pengiriman_batch[] = [
                'periode_id' => $this->periode_id,
                'klien_pengiriman_id' => $klien_pengiriman_id,
                'category_id' => $category_id
            ];
        }

        //create klien pengiriman setting
        $insert_klien_pengiriman = PeriodeKlienPengiriman::insert($klien_pengiriman_batch);

        $this->klien_pengiriman_setting = $gkp;
        //create drop point

        $drop_point_batch = [];

        foreach($this->dp_setting as $key => $ds) {
            $drop_point_batch[] = [
                'periode_id' => $this->periode_id,
                'drop_point_outgoing' => $ds['drop_point_outgoing'],
                'pengurangan_total' => isset($ds['pengurangan_total']) ? intval($ds['pengurangan_total']) : 0,
                'penambahan_total' => isset($ds['penambahan_total']) ? intval($ds['penambahan_total']) : 0,
                'diskon_cod' => isset($ds['diskon_cod']) ? intval($ds['diskon_cod']) : 0,
                'grouping' => '',
            ];
        }

        $insert_drop_point_setting = SettingDpPeriode::insert($drop_point_batch);
        $this->drop_point_setting = $drop_point_batch;

        $grading_1_batch = [];

        foreach($this->g1 as $key => $g1) {
            $grading_1_batch[] = [
                'periode_id' => $this->periode_id,
                'sprinter_pickup' => $g1['id'],
                'grading_type' => 1,
                'transit_fee' => isset($g1['transit_fee']) ? intval($g1['transit_fee']) : 0,
                'denda_void' => isset($g1['denda_void']) ? intval($g1['denda_void']) : 0,
                'denda_dfod' => isset($g1['denda_dfod']) ? intval($g1['denda_dfod']) : 0,
                'denda_pusat' => isset($g1['denda_pusat']) ? intval($g1['denda_pusat']) : 0,
                'denda_selisih_berat' => isset($g1['denda_selisih_berat']) ? intval($g1['denda_selisih_berat']) : 0,
                'denda_lost_scan_kirim' => isset($g1['denda_lost_scan_kirim']) ? intval($g1['denda_lost_scan_kirim']) : 0,
                'denda_auto_claim' => isset($g1['denda_auto_claim']) ? intval($g1['denda_auto_claim']) : 0,
                'denda_sponsorship' => isset($g1['denda_sponsorship']) ? intval($g1['denda_sponsorship']) : 0,
                'denda_late_pickup_ecommerce' => isset($g1['denda_late_pickup_ecommerce']) ? intval($g1['denda_late_pickup_ecommerce']) : 0,
                'potongan_pop' => isset($g1['potongan_pop']) ? intval($g1['potongan_pop']) : 0,
                'denda_lainnya' => isset($g1['denda_lainnya']) ? intval($g1['denda_lainnya']) : 0,
            ];
        }

        $insert_grading_1 = Denda::insert($grading_1_batch);
        $this->grading_1_setting = $this->g1;

        $grading_2_batch = [];

        foreach($this->g2 as $key => $g2) {
            $grading_2_batch[] = [
                'periode_id' => $this->periode_id,
                'sprinter_pickup' => $g2['id'],
                'grading_type' => 2,
                'transit_fee' => isset($g2['transit_fee']) ? intval($g2['transit_fee']) : 0,
                'denda_void' => isset($g2['denda_void']) ? intval($g2['denda_void']) : 0,
                'denda_dfod' => isset($g2['denda_dfod']) ? intval($g2['denda_dfod']) : 0,
                'denda_pusat' => isset($g2['denda_pusat']) ? intval($g2['denda_pusat']) : 0,
                'denda_selisih_berat' => isset($g2['denda_selisih_berat']) ? intval($g2['denda_selisih_berat']) : 0,
                'denda_lost_scan_kirim' => isset($g2['denda_lost_scan_kirim']) ? intval($g2['denda_lost_scan_kirim']) : 0,
                'denda_auto_claim' => isset($g2['denda_auto_claim']) ? intval($g2['denda_auto_claim']) : 0,
                'denda_sponsorship' => isset($g2['denda_sponsorship']) ? intval($g2['denda_sponsorship']) : 0,
                'denda_late_pickup_ecommerce' => isset($g2['denda_late_pickup_ecommerce']) ? intval($g2['denda_late_pickup_ecommerce']) : 0,
                'potongan_pop' => isset($g2['potongan_pop']) ? intval($g2['potongan_pop']) : 0,
                'denda_lainnya' => isset($g2['denda_lainnya']) ? intval($g1['denda_lainnya']) : 0,
            ];
        }

        $insert_grading_2 = Denda::insert($grading_2_batch);
        $this->grading_2_setting = $this->g2;

        $grading_3_batch = [];

        foreach($this->g3 as $key => $g3) {
            $grading_3_batch[] = [
                'periode_id' => $this->periode_id,
                'sprinter_pickup' => $g3['id'],
                'grading_type' => 3,
                'transit_fee' => isset($g3['transit_fee']) ? intval($g3['transit_fee']) : 0,
                'denda_void' => isset($g3['denda_void']) ? intval($g3['denda_void']) : 0,
                'denda_dfod' => isset($g3['denda_dfod']) ? intval($g3['denda_dfod']) : 0,
                'denda_pusat' => isset($g3['denda_pusat']) ? intval($g3['denda_pusat']) : 0,
                'denda_selisih_berat' => isset($g3['denda_selisih_berat']) ? intval($g3['denda_selisih_berat']) : 0,
                'denda_lost_scan_kirim' => isset($g3['denda_lost_scan_kirim']) ? intval($g3['denda_lost_scan_kirim']) : 0,
                'denda_auto_claim' => isset($g3['denda_auto_claim']) ? intval($g3['denda_auto_claim']) : 0,
                'denda_sponsorship' => isset($g3['denda_sponsorship']) ? intval($g3['denda_sponsorship']) : 0,
                'denda_late_pickup_ecommerce' => isset($g3['denda_late_pickup_ecommerce']) ? intval($g3['denda_late_pickup_ecommerce']) : 0,
                'potongan_pop' => isset($g3['potongan_pop']) ? intval($g3['potongan_pop']) : 0,
                'denda_lainnya' => isset($g3['denda_lainnya']) ? intval($g3['denda_lainnya']) : 0,
            ];
        }

        $insert_grading_3 = Denda::insert($grading_3_batch);
        $this->grading_3_setting = $this->g3;

       //create file upload, save to storage for read

       $schema_name = 'cashback_'.strtolower($this->month).'_'.$this->year;
       $this->schema_name = $schema_name;

       foreach($this->files as $file) {
            $file_uploaded = $file->store('file_upload', 'public');

            $uploaded_file = UploadFile::create([
                'file_name' => $file->getClientOriginalName(),
                'month_period' => $this->month,
                'year_period' => $this->year,
                'count_row' => 0,
                'file_size' => $file->getSize(),
                'table_name' => $schema_name.'.'.'data_mart',
                'is_pivot_processing_done' => 0,
                // 'processed_by' => auth()->user()->id,
                'type_file' => 0, //0 cashback; 1 ttd;
                'processing_status' => 'UPLOADED',
            ]);

            FileProcessing::fileProcessing($uploaded_file, $file_uploaded, $file ,$schema_name, $this->periode);
       }

       $this->state = $create_state;

        $this->currentStep = 8;
    }

    public function eighthStepSubmit()
    {


        $this->currentStep = 9;
    }

    public function ninthStepSubmit()
    {
        // $validatedData = $this->validate([
        //     'stock' => 'required',
        //     'status' => 'required',
        // ]);

        $this->currentStep = 10;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitForm()
    {

        // Product::create([
        //     'name' => $this->name,
        //     'amount' => $this->amount,
        //     'description' => $this->description,
        //     'stock' => $this->stock,
        //     'status' => $this->status,
        // ]);


        $this->successMessage = 'Proses berhasil di kerjakan!';

        $this->clearForm();

        $this->currentStep = 1;
    }

    public function process() {

        // process queue import
        // process pivot

        GeneratePivot::createOrReplacePivot($this->schema_name, $this->periode_id);
        // process grading
        // process report
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function back($step)
    {
        $this->currentStep = $step;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function clearForm()
    {

    }
}
