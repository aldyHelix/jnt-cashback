<?php

namespace Modules\Collectionpoint\Datatables;

use App\Models\Model;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use Modules\Collectionpoint\Models\Collectionpoint;

class CollectionpointDatatables extends Datatables
{
    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'Collection Point';

    /**
     * Setup query builder
     */
    public function __construct()
    {
        $this->query = Collectionpoint::query();
    }
    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.collectionpoint.index', ['datatables']);
    }


    /**
     * DataTables using Eloquent Builder.
     *
     * @return DataTableAbstract|EloquentDataTable
     */
    public function handle()
    {
        return $this->eloquent($this->query)
        ->addColumn('action', function ($row) {
            return $this->action($row);
        });
    }

    public function action($data)
    {
        return view('collectionpoint::_parts.table-action', $data);
    }
    /**
     * Table headers
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Kode CP',
            'Nama CP',
            'Nama PT',
            'DP outgoing',
            'Zona',
            'Grading',
            'Nomor Rekening',
            'Nama Rekening',
            'Nama bank',
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
            ['data' => 'kode_cp', 'class' => 'text-center'],
            ['data' => 'nama_cp', 'class' => 'text-center'],
            ['data' => 'nama_pt', 'class' => 'text-center'],
            ['data' => 'drop_point_outgoing', 'class' => 'text-center'],
            ['data' => 'grading_pickup', 'class' => 'text-center'],
            ['data' => 'zona_delivery', 'class' => 'text-center'],
            ['data' => 'nomor_rekening', 'class' => 'text-center'],
            ['data' => 'nama_rekening', 'class' => 'text-center'],
            ['data' => 'nama_bank', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
