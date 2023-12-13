<?php

namespace Modules\Ratesetting\Datatables;

use App\Models\Model;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use Modules\Ratesetting\Models\DeliveryFee;

class DeliveryFeeDatatables extends Datatables
{
    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'Master Delivery Fee';

    /**
     * Setup query builder
     */
    public function __construct()
    {
        $this->query = DeliveryFee::query();
    }

    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.ratesetting.delivery.index', ['datatables']);
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
        return view('ratesetting::_parts.table-action-delivery', $data);
    }

    /**
     * Table headers
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Zona',
            'Tarif',
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
            ['data' => 'zona', 'class' => 'text-center'],
            ['data' => 'tarif', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
