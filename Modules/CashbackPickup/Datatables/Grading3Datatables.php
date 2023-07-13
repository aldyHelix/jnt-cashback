<?php

namespace Modules\CashbackPickup\Datatables;

use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use App\Models\Periode;
use App\Models\Denda;
use Modules\CollectionPoint\Models\CollectionPoint;

class Grading3Datatables extends Datatables
{

    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'Grading 3';

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
        return route('ladmin.cashbackpickup.index', ['datatables', 'grade' => 3]);
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
            ->addColumn('detail', function ($row) {
                return $this->viewDetail($row);
            })
            ->addColumn('action', function ($row) {
                return $this->action($row);
            });
    }

    public function setDenda($data) {
        $exist = Denda::where(['periode_id' => $data->id, 'grading_type' => 3])->first();
        $data['cp'] = CollectionPoint::get();
        $data['grading'] = 3;
        $data['denda'] = $exist ?? new Denda(); //find where peride id & grading if null new Denda if not null fill
        return view('cashbackpickup::_parts._form-denda', $data);
    }

    public function viewDetail($data) {
        $data['code'] = $data->code;
        $data['grading'] = 3;
        return view('cashbackpickup::_parts._view-detail', $data);
    }

    public function action($data)
    {
        $data['code'] = $data->code;
        $data['grading'] = 3;
        return view('cashbackpickup::_parts.table-action', $data);
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
            'Denda',
            'Status',
            'Update Terakhir',
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
            ['data' => 'denda', 'class' => 'text-center'],
            ['data' => 'status', 'class' => 'text-center'],
            ['data' => 'updated_at', 'class' => 'text-center'],
            ['data' => 'detail', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
