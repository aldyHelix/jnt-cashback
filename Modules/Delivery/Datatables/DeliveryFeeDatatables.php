<?php

namespace Modules\Delivery\Datatables;

use App\Models\DirectFee;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use DB;
use Modules\Delivery\Models\DeliveryFee;

class DeliveryFeeDatatables extends Datatables
{
    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'Delivery Fee';

    /**
     * Setup query builder
     */
    public function __construct()
    {
        $this->query = new DeliveryFee();
    }

    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.delivery.detail', ['datatables', 'code' => $this->data['code']]);
    }

    /**
     * DataTables using Eloquent Builder.
     *
     * @return DataTableAbstract|EloquentDataTable
     */
    public function handle()
    {
        $this->query = $this->query->setTable($this->data['code'] . '.direct_fee')->query();
        return $this->eloquent($this->query);
    }

    /**
     * Table headers
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Drop Point Outgoing',
            'Count',
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
            ['data' => 'drop_point_outgoing', 'class' => 'text-center'],
            ['data' => 'count', 'class' => 'text-center']
        ];
    }
}
