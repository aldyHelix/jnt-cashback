<?php

namespace Modules\Ladmin\Http\Controllers;

use App\Models\Periode;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Ladmin\Engine\Models\Admin;
use Modules\Collectionpoint\Models\Collectionpoint;
use Modules\Ladmin\Http\Controllers\Controller;
use Modules\Ladmin\Http\Requests\ProfileRequest;
use Modules\Uploadfile\Models\Uploadfile;
use Ramsey\Uuid\Type\Integer;
use App\Facades\GeneratePivot;
use App\Models\FileJobs;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->has('ajax')) {
            return $this->ajaxRoute($request);
        }

        $data['user'] = auth()->user();
        $data['inspire'] = Inspiring::quote();

        $data['period'] = [];
        $data_periode = Periode::get();
        foreach($data_periode as $periode) {
            $sum = DB::table($periode->code . '.data_mart')->select('biaya_kirim')->sum('biaya_kirim');
            $data['period'][$periode->month . '-' . $periode->year] = $sum;
        }

        return ladmin()->view('profile.index', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $data['user'] = auth()->user();
        return ladmin()->view('profile.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProfileRequest $request)
    {
        return $request->updateProfile();
    }

    /**
     * Ajax route
     *
     * @param Request $request
     * @return void
     */
    protected function ajaxRoute(Request $request)
    {
        if (method_exists(__CLASS__, $request->ajax)) {
            return $this->{$request->ajax}($request);
        }
        abort(404);
    }

    /**
     * Get total admin online
     *
     * @return Integer
     */
    protected function total_online()
    {
        return Admin::where('online_at', '>', now())->count();
    }


    /**
     * Get total admin
     *
     * @return Integer
     */
    protected function total_admin()
    {
        return Admin::count();
    }

    /**
    * Get total admin
    *
    * @return Integer
    */
    protected function queue_status()
    {
        return $this->total_pending_jobs() > 0 ? 'Queue is active, job is running' : 'Queue is not active, no job running';
    }

    /**
     * Get total admin
     *
     * @return Integer
     */
    protected function total_pending_jobs()
    {
        $connection = config('queue.default');
        $queue = Queue::connection($connection)->size('default');
        return $queue;
    }


    protected function avg_processing()
    {
        $data = Uploadfile::selectRaw('start_processed_at, done_processed_at')->get();
        if (empty($data)) {
            return '0h: 0m: 0s';
        } else {
            $sum_h = 0;
            $sum_m = 0;
            $sum_s = 0;
            $count_total_sum_s = 0;
            $count_total_sum_m = 0;
            $count_total_sum_h = 0;

            foreach ($data as $key => $row) {
                $datetime_1 = $row->start_processed_at ?? '';
                $datetime_2 = $row->done_processed_at ?? '';

                $start_datetime = new DateTime($datetime_1);
                $diff = $start_datetime->diff(new DateTime($datetime_2));

                $sum_h += $diff->h;
                $sum_m += $diff->i;
                $sum_s += $diff->s;
            }

            $sum_total_seconds = $sum_h * 3600 + $sum_m * 60 + $sum_s;

            $count_total_sum_h = floor($sum_total_seconds / 3600);
            $sum_total_seconds %= 3600;

            $count_total_sum_m = floor($sum_total_seconds / 60);
            $count_total_sum_s = $sum_total_seconds % 60;

            $string = $count_total_sum_h . 'h:' . $count_total_sum_m . 'm:' . $count_total_sum_s . 's';
            return $string;
        }
    }

    protected function latest_upload_file()
    {
        $data = Uploadfile::orderBy('created_at', 'desc')->first();
        return $data ? $data->file_name : '-';
    }

    protected function total_period()
    {
        return Periode::get()->count();
    }


    protected function total_collection_point()
    {
        return Collectionpoint::get()->count();
    }

    protected function total_file_uploaded()
    {
        return Uploadfile::get()->count();
    }

    protected function get_sum_total_period()
    {
        $data = [];
        $data_periode = Periode::get();
        foreach($data_periode as $periode) {
            $sum = DB::table($periode->code . '.cp_dp_all_count_sum')->select('sum')->sum('sum');
            $data[$periode->month . '-' . $periode->year] = $sum;
        }
        return $data;
    }

    /**
     * Response total admin online
     *
     * @param Request $request
     * @return Response
     */
    protected function load_total_online(Request $request)
    {
        return number_format($this->total_online(), 0);
    }

    /**
     * Response percentage admin online
     *
     * @param Request $request
     * @return Response
     */
    protected function load_percenteage_online(Request $request)
    {
        $result = ($this->total_online() / $this->total_admin()) * 100;
        $formater = number_format($result, 1) . '%';
        return '<h2>' . $formater . '</h2>
                <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="25"
                    style="height:10px;" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: ' . $result . '%"></div>
                </div>';
    }

    /**
     * Response total admin
     *
     * @param Request $request
     * @return Response
     */
    protected function load_total_admin(Request $request)
    {
        return number_format($this->total_admin(), 0);
    }

    /**
     * Response total admin
     *
     * @param Request $request
     * @return Response
     */
    protected function load_avg_processing(Request $request)
    {
        return $this->avg_processing();
    }

    /**
     * Response total admin
     *
     * @param Request $request
     * @return Response
     */
    protected function load_total_period(Request $request)
    {
        return number_format($this->total_period(), 0);
    }

    /**
    * Response total admin
    *
    * @param Request $request
    * @return Response
    */
    protected function load_total_collection_point(Request $request)
    {
        return number_format($this->total_collection_point(), 0);
    }

    /**
     * Response total admin
     *
     * @param Request $request
     * @return Response
     */
    protected function load_total_file_upload(Request $request)
    {
        return number_format($this->total_file_uploaded(), 0);
    }

    /**
     * Response table coworkers
     *
     * @param Request $request
     * @return Response
     */
    protected function load_table_coworkers(Request $request)
    {
        $data['roles'] = $request->user()->roles;
        return view('ladmin::profile._parts._table_coworkers', $data);
    }

    /**
     * Response table file on process
     *
     * @param Request $request
     * @return Response
     */
    protected function load_table_on_process(Request $request)
    {
        $data['files'] = FileJobs::where('is_imported', 0)->get();
        return view('ladmin::profile._parts._table_on_process', $data);
    }
}
