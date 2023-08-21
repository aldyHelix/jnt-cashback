<x-ladmin-auth-layout>
    <x-slot name="title">Master Data Tarif Delivery Fee </x-slot>
    @can(['ladmin.ratesetting.deliferyfee.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.ratesetting.delivery.create',ladmin()->back()) }}" class="btn btn-primary">&plus; Add New</a>
    </x-slot>
    @endcan
    <x-ladmin-card>
        <x-slot name="body">
            <div class="row">
                <div class="col-4">
                    {{ \Modules\RateSetting\Datatables\DeliveryFeeDatatables::table() }}
                </div>
                <div class="col-8">
                    Setting Zona Delivery
                    <div class="table-responsive" style="height: 500px">
                        <form action="{{ route('ladmin.ratesetting.delivery.setting') }}" method="POST">
                            @csrf
                            @method('POST')
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Zona</td>
                                    <td>Nama CP</td>
                                    <td>TTD</td>
                                    <td>KPI Target count</td>
                                    <td>KPI Reduce not achievement</td>
                                </tr>
                            </thead>
                                <tbody>
                                    @foreach ($collection_point as $key => $item)
                                    <input type="hidden" name="cp[{{$key}}][id]" value="{{ $item->id }}">
                                    <input type="hidden" name="cp[{{$key}}][drop_point_outgoing]" value="{{ $item->drop_point_outgoing }}">
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="cp[{{ $key }}][is_show]" value=0>
                                                <input class="form-check-input" type="checkbox" name="cp[{{$key}}][is_show]" value=1 id="dpCheck"
                                                {{ $item->is_show ? 'checked' : 0}}>
                                            </div>
                                        </td>
                                        <td style="text-align: left;width: 100px;">
                                            <select class="form-select" name="cp[{{ $key }}][zona_delivery]">
                                                @foreach ($zona as $zone)
                                                    <option value="{{$zone}}" {{$item->zona_delivery == $zone ? 'selected' : ''}}>{{ $zone }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="text-align: left;width: 200px;">{{ $item->nama_cp ?? '(blank)' }} </td>
                                        <td style="text-align: left;width: 200px;">
                                            <x-ladmin-input id="drop_point_ttd" type="text" class="mb-3 col" name="cp[{{ $key }}][drop_point_ttd]" value="{{ old('drop_point_ttd', $item->drop_point_ttd) }}" placeholder="Drop point ttd" />
                                        </td>
                                        <td>
                                            <x-ladmin-input id="kpi_target_count" type="text" class="mb-3 col" name="cp[{{ $key }}][kpi_target_count]" value="{{ old('kpi_target_count', intval($item->kpi_target_count)) }}" placeholder="KPI target count" />
                                        </td>
                                        <td>
                                            <x-ladmin-input id="kpi_reduce_not_achievement" type="text" class="mb-3 col" name="cp[{{ $key }}][kpi_reduce_not_achievement]" value="{{ old('kpi_reduce_not_achievement', intval($item->kpi_reduce_not_achievement)) }}" placeholder="KPI Reduce" />
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>

                            <button class="btn btn-primary" type="submit">Save</button>
                        </form>

                    </div>
                </div>
            </div>
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
        {{-- <script src="{{ asset("css/uploadfile/uploadfile.css") }}"></script> --}}
    </x-slot>
    <x-slot name="scripts">
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.8/xlsx.full.min.js"></script>
        <script src="{{ asset("js/uploadfile/uploadfile.js") }}"></script> --}}
    </x-slot>
</x-ladmin-auth-layout>
