<?php

namespace Modules\RateSetting\Datatables;

use App\Models\Model;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use Modules\RateSetting\Models\RateSetting;

class RateSettingGradeBDatatables extends Datatables
{

    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'Rate Setting Grade B';

    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.ratesetting.grade-b.index', ['datatables']);
    }

    /**
     * Setup query builder
     */
    public function __construct()
    {
        $this->query = RateSetting::query()->where('grading_type', 'B');
    }

     /**
     * DataTables using Eloquent Builder.
     *
     * @return DataTableAbstract|EloquentDataTable
     */
    public function handle()
    {
        return $this->eloquent($this->query)
            ->editColumn('diskon_persen', function ($row) {
                return $row->diskon_persen.'%';
            })
            ->addColumn('action', function ($row) {
                return $this->action($row);
            });
    }

    public function action($data)
    {
        return view('ratesetting::_parts.table-action', $data);
    }

    /**
     * Table headers
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Sumber Waybill',
            'Diskon',
            'Fee',
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
            ['data' => 'sumber_waybill', 'class' => 'text-center'],
            ['data' => 'diskon_persen', 'class' => 'text-center'],
            ['data' => 'fee', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
