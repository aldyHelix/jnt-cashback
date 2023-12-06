<?php

namespace Modules\CashbackPickup\Datatables;

use App\Models\Denda;
use App\Models\Periode;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use Modules\CollectionPoint\Models\CollectionPoint;

class DPFDatatables extends Datatables
{

    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'Grading DPF Cashback';

    /**
     * Setup query builder
     */
    public function __construct()
    {
        $this->query = Periode::query();
    }

    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.cashbackpickup.dpf.index', ['datatables']);
    }

    /**
     * DataTables using Eloquent Builder.
     *
     * @return DataTableAbstract|EloquentDataTable
     */
    public function handle()
    {
        return $this->eloquent($this->query)
            ->addColumn('periode', function ($row) {
                return $row->month.'/'.$row->year;
            })
            ->addColumn('denda', function ($row) {
                return $this->setDenda($row);
            })
            ->addColumn('setting', function ($row) {
                return $this->viewSetting($row);
            })
            ->addColumn('detail', function ($row) {
                return $this->viewDetail($row);
            })
            ->addColumn('action', function ($row) {
                return $this->action($row);
            });
    }

    public function setDenda($data) {
        $data['grading'] = "DPF";
        $data['id'] = $data->id;
        return view('cashbackpickup::dpf._parts._denda-action', $data);
    }

    public function viewDetail($data) {
        $data['code'] = $data->code;
        $data['grading'] = "DPF";
        return view('cashbackpickup::dpf._parts._view-detail', $data);
    }

    public function viewSetting($data) {
        $data['code'] = $data->code;
        $data['grading'] = "DPF";
        return view('cashbackpickup::dpf._parts._view-setting', $data);
    }

    public function action($data)
    {
        $data['has_denda'] = false;
        $denda = Denda::where(['periode_id' => $data->id, 'grading_type' => "DPF"])->get();
        if($denda->count() > 0) {
            $data['has_denda'] = true;
        }
        $data['is_locked'] = $data->is_locked;
        $data['code'] = $data->code;
        $data['grading'] = "dpf";

        return view('cashbackpickup::dpf._parts.table-action', $data);
    }

    /**
     * Table headers
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Code',
            'Periode',
            'Status',
            'Update Terakhir',
            'Denda',
            'Setting',
            'Tampilkan Detail',
            'Aksi' => ['class' => 'text-center'],
        ];
    }

    /**
     * Datatables Data column
     * Visit Doc: https://datatables.net/reference/option/columns.data#Default
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            ['data' => 'code', 'class' => 'text-center'],
            ['data' => 'periode', 'class' => 'text-center'],
            ['data' => 'status', 'class' => 'text-center'],
            ['data' => 'updated_at', 'class' => 'text-center'],
            ['data' => 'denda', 'class' => 'text-center'],
            ['data' => 'setting', 'class' => 'text-center'],
            ['data' => 'detail', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
