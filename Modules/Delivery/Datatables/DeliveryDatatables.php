<?php

namespace Modules\Delivery\Datatables;

use App\Models\Denda;
use App\Models\DendaDelivery;
use App\Models\Periode;
use App\Models\PeriodeDelivery;
use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use Modules\CollectionPoint\Models\CollectionPoint;

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
        $this->query = PeriodeDelivery::query();
    }

    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.delivery.index', ['datatables']);
    }

    /**
     * DataTables using Eloquent Builder.
     *
     * @return DataTableAbstract|EloquentDataTable
     */
    public function handle()
    {
        return $this->eloquent($this->query)
            ->addColumn('pickup_fee', function($row) {
                return $this->setDeliveryFee($row);
            })
            ->addColumn('periode', function ($row) {
                return $row->month.'/'.$row->year;
            })
            // ->addColumn('denda', function ($row) {
            //     return $this->setDenda($row);
            // })
            ->addColumn('detail', function ($row) {
                return $this->viewDetail($row);
            })
            ->addColumn('action', function ($row) {
                return $this->action($row);
            });
    }

    public function setDenda($data) {
        $exist = DendaDelivery::where(['periode_id' => $data->id])->first();
        $data['cp'] = CollectionPoint::get();
        $data['grading'] = 1;
        $data['denda'] = $exist ?? new Denda();
        return view('delivery::_parts._form-denda', $data);
    }

    public function setDeliveryFee($data) {
        return view('delivery::_parts._form-delivery-fee', $data);
    }

    public function viewDetail($data) {
        $data['code'] = $data->code;
        return view('delivery::_parts._view-detail', $data);
    }

    public function action($data)
    {
        $period_delivery = PeriodeDelivery::where('code', $data->code)->first();
        $cashback_schema = 'cashback_'.strtolower($period_delivery->month).'_'.$period_delivery->year;
        $period_cashback = Periode::where('code', $cashback_schema)->get();
        $data['code'] = $data->code;
        $data['is_locked'] = $data->is_locked;
        $data['process_available'] = $period_cashback->count() > 0 ? true : false;
        return view('delivery::_parts.table-action', $data);
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
            'Update Terakhir',
            'Status',
            // 'Denda',
            'Setting Pickup Fee',
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
            ['data' => 'pickup_fee', 'class' => 'text-center'],
            // ['data' => 'denda', 'class' => 'text-center'],
            ['data' => 'detail', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
