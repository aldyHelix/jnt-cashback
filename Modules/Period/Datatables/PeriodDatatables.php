<?php

namespace Modules\Period\Datatables;

use App\Models\Model;
use App\Models\Periode;
use App\Models\User;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PeriodDatatables extends Datatables
{
    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'Period';

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
        return route('ladmin.period.index', ['datatables']);
    }


    /**
     * DataTables using Eloquent Builder.
     *
     * @return DataTableAbstract|EloquentDataTable
     */
    public function handle()
    {
        return $this->eloquent($this->query)
            ->addColumn('period', function ($row) {
                return $row->month . '/' . $row->year;
            })
            ->addColumn('total_biaya_kirim', function ($row) {
                // return Schema::hasTable('cashback_may_2023.sum_all_biaya_kirim');
                return 'Rp' . rupiah_format(DB::table($row->code . '.sum_all_biaya_kirim')->first()->sum ?? 0) ;

            })
            ->editColumn('inserted_row', function ($row) {
                return decimal_format(DB::table($row->code . '.data_mart')->count() ?? 0);
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d-m-Y h:i');
            })
            ->addColumn('action', function ($row) {
                return $this->viewDetail($row);
            });
    }

    public function viewDetail($data)
    {
        $data['code'] = $data->code;
        $data['row'] = $data;
        return view('period::_parts._view-detail', $data);
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
            'Month/Year',
            'Total Biaya Kirim',
            'Row Inserted',
            'Status',
            'Last Update',
            'Action' => ['class' => 'text-center'],
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
            ['data' => 'period', 'class' => 'text-center'],
            ['data' => 'total_biaya_kirim', 'class' => 'text-center'],
            ['data' => 'inserted_row', 'class' => 'text-center'],
            ['data' => 'status', 'class' => 'text-center'],
            ['data' => 'updated_at', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
