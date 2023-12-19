<?php

namespace Modules\Globalsetting\Datatables;

use App\Models\Denda;
use App\Models\DendaDelivery;
use App\Models\Periode;
use App\Models\PeriodeDelivery;
use App\Models\DeliveryZone;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use Modules\Collectionpoint\Models\Collectionpoint;

class DeliveryDatatables extends Datatables
{
    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'Delivery';

    /**
     * Setup query builder
     */
    public function __construct()
    {
        $this->query = DeliveryZone::query();
    }

    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.globalsetting.delivery.index', ['datatables']);
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
        return '';
    }

    /**
     * Table headers
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Collection Point',
            'DP',
            'TTD',
            'KPI Target',
            'KPI Reduce',
            'Status',
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
            ['data' => 'collection_point_id', 'class' => 'text-center'],
            ['data' => 'drop_point_outgoing', 'class' => 'text-center'],
            ['data' => 'drop_point_ttd', 'class' => 'text-center'],
            ['data' => 'kpi_target_count', 'class' => 'text-center'],
            ['data' => 'kpi_reduce_not_achievement', 'class' => 'text-center'],
            ['data' => 'is_show', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
