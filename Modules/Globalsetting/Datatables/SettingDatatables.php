<?php

namespace Modules\Globalsetting\Datatables;

use App\Models\Globalsetting;
use App\Models\Model;
use App\Models\Periode;
use App\Models\User;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingDatatables extends Datatables
{
    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'General Setting';

    /**
     * Setup query builder
     */
    public function __construct()
    {
        $this->query = Globalsetting::query();
    }

    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.globalsetting.setting.index', ['datatables']);
    }


    /**
     * DataTables using Eloquent Builder.
     *
     * @return DataTableAbstract|EloquentDataTable
     */
    public function handle()
    {
        $query = $this->query->where('type', 'general')->orderBy('order', 'ASC');
        return $this->eloquent($query)
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d-m-Y h:i');
                // date('d m Y h:i', strtotime($row->created_at));
            })
            ->addColumn('action', function ($row) {
                return $this->action($row);
            });
    }

    public function action($data)
    {
        return view('globalsetting::setting._partials._action', $data);
    }

    /**
     * Table headers
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Kode',
            'Nama Setting',
            'Nilai',
            'Order',
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
            ['data' => 'name', 'class' => 'text-center'],
            ['data' => 'value', 'class' => 'text-center'],
            ['data' => 'order', 'class' => 'text-center'],
            ['data' => 'updated_at', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
