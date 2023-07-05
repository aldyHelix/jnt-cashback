<?php

namespace Modules\UploadFile\Datatables;

use Hexters\Ladmin\Datatables;
use Illuminate\Support\Facades\Blade;
use Modules\UploadFile\Models\UploadFile;

class UploadFileDatatables extends Datatables
{

    /**
     * Page title
     *
     * @var String
     */
    protected $title = 'File Uploaded';

    /**
     * Setup query builder
     */
    public function __construct()
    {
        $this->query = UploadFile::query()->orderBy('created_at', 'DESC');
    }

    /**
     * Custom route to fetch data from Datatables
     *
     * @return String
     */
    public function ajax()
    {
        return route('ladmin.uploadfile.welcome', ['datatables']);
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
                return $this->actionButton($row);
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d-m-Y h:i');
                // date('d m Y h:i', strtotime($row->created_at));
            })
            ->editColumn('type_file', function($row) {
                return intval($row->type_file) ? 'TTD' : 'Cashback';
            })
            ->addColumn('period', function ($row) {
                return $row->month_period.'-'.$row->year_period;
            });
    }

    public function actionButton($row){
        return view('uploadfile::_partial.table-action', $row);
    }

    /**
     * Table headers
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'File Name',
            'Period',
            'Row',
            'Date uploaded',
            'Status',
            'Type',
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
            ['data' => 'file_name', 'class' => 'text-center'],
            ['data' => 'period', 'class' => 'text-center'],
            ['data' => 'count_row', 'class' => 'text-center'],
            ['data' => 'created_at', 'class' => 'text-center'],
            ['data' => 'processing_status', 'class' => 'text-center'],
            ['data' => 'type_file', 'class' => 'text-center'],
            ['data' => 'action', 'class' => 'text-center', 'orderable' => false]
        ];
    }
}
