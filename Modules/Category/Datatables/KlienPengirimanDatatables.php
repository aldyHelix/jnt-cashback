<?php

namespace Modules\Category\Datatables;

use App\Models\KlienPengiriman;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;

class KlienPengirimanDatatables extends Datatables
{
    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'Klien Pengiriman';

    /**
     * Setup query builder
     */
    public function __construct()
    {
        $this->query = KlienPengiriman::query();
    }
    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.category.index', ['datatables']);
    }


    /**
     * DataTables using Eloquent Builder.
     *
     * @return DataTableAbstract|EloquentDataTable
     */
    public function handle()
    {
        $query = $this->query->groupBy('klien_pengiriman');
        return $this->eloquent($query);
        // ->addColumn('action', function ($row) {
        //     return $this->action($row);
        // });
    }

    // public function action($data)
    // {
    //     return view('category::_parts.table-action', $data);
    // }
    /**
     * Table headers
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Klien Pengiriman',
            // 'Action' => ['class' => 'text-center'],
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
            ['data' => 'klien_pengiriman', 'class' => 'text-center'],
            // ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
