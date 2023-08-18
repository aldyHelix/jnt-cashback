<x-ladmin-auth-layout>
    <x-slot name="title">Period Summary</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
        <div class="container">
            <div class="row" style="margin-bottom: 10px;">
                <div class="col">
                    <h4>Setting</h4>
                </div>
            </div>
            <div class="row" style="margin-bottom: 10px;">
                <div class="col-6">
                    Setting drop point outgoing
                    <div class="table-responsive">
                        <form action="{{ route('ladmin.period.setting-dp', $periode->id) }}" method="POST">
                            @csrf
                            @method('POST')
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Nama DP</td>
                                    <td>Pengurangan</td>
                                    <td>Penambahan</td>
                                    <td>Diskon COD</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dp as $key => $item)
                                <input type="hidden" name="dp[{{ $key }}][drop_point_outgoing]" value="{{ $item->drop_point_outgoing }}">
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="hidden" name="dp[{{ $key }}][is_import]" value=0>
                                            <input class="form-check-input" type="checkbox" name="dp[{{$key}}][is_import]" value=1 id="dpCheck" {{ intval($item->id) ? 'checked' : ''}}>
                                        </div>
                                    </td>
                                    <td style="text-align: left;width: 300px;">{{ $item->drop_point_outgoing }} </td>
                                    <td>
                                        <x-ladmin-input id="pengurangan_total" type="text" class="mb-3 col" name="dp[{{ $key }}][pengurangan_total]" value="{{ old('pengurangan_total', intval($item->pengurangan_total)) }}" placeholder="Pengurangan Total" />
                                    </td>
                                    <td>
                                        <x-ladmin-input id="penambahan_total" type="text" class="mb-3 col" name="dp[{{ $key }}][penambahan_total]" value="{{ old('penambahan_total', intval($item->penambahan_total)) }}" placeholder="Pemambahan  Total" />
                                    </td>
                                    <td>
                                        <x-ladmin-input id="diskon_cod" type="text" class="mb-3 col" name="dp[{{ $key }}][diskon_cod]" value="{{ old('diskon_cod', intval($item->diskon_cod)) }}" placeholder="Diskon COD" />
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button class="btn btn-primary" type="submit">Save</button>
                        </form>
                    </div>
                </div>
                <div class="col-6">
                    Setting Klien pengiriman
                    <div class="table-responsive">
                        <form action="{{ route('ladmin.period.update-klien', $periode->id) }}" method="POST">
                            @csrf
                            @method('POST')
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>Klien</td>
                                    <td>Reguler</td>
                                    <td>DFOD</td>
                                    <td>SUPER</td>
                                </tr>
                            </thead>
                                <tbody>
                                    @foreach ($klien_pengiriman as $key => $item)
                                    <input type="hidden" name="klien[{{$key}}][item]" value="{{ $item->klien_pengiriman }}">
                                    <tr>
                                        <td style="text-align: left;width: 300px;">{{ $item->klien_pengiriman ?? '(blank)' }} </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="klien[{{$key}}][reguler]" value=0>
                                                <input class="form-check-input" type="checkbox" name="klien[{{$key}}][reguler]" value=1 id="flexCheckDefaultReguler" {{ $item->is_reguler ? 'checked' : ''}}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="klien[{{$key}}][dfod]" value=0>
                                                <input class="form-check-input" type="checkbox" name="klien[{{$key}}][dfod]" value=1 id="flexCheckDefaultDfod" {{ $item->is_dfod ? 'checked' : ''}}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="klien[{{$key}}][super]" value=0>
                                                <input class="form-check-input" type="checkbox" name="klien[{{$key}}][super]" value=1 id="flexCheckDefaultSuper" {{ $item->is_super ? 'checked' : ''}}>
                                            </div>
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
        </div>
        </x-slot>
    </x-ladmin-card>
</x-ladmin-auth-layout>
