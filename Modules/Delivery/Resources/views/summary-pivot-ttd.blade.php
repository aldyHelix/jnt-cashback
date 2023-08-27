<x-ladmin-auth-layout>
    <x-slot name="title">Delivery Summary</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
        <div class="container">
            <div class="row" style="margin-bottom: 10px;">
                <div class="col">
                    <h4>Summary Pivot TTD</h4>
                </div>
            </div>
            <div class="row">
                @foreach($pivot as $key => $item)
                <div class="col">
                    <h5>{{ strtoupper(str_replace('_', ' ', $key)) }}</h5>
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Sprinter</th>
                            <th scope="col">Count</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($item as $cell)
                            <tr>
                                <td>{{ $cell->sprinter}}</td>
                                <td>{{ decimal_format($cell->count) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                              <td><b>Total</b></td>
                              <td>{{ decimal_format($item->sum('count')) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endforeach
            </div>
        </div>
        <div class="row">
            {{-- {!! $direct_fee !!} --}}
        </div>
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
        <script>
            function downloadExcel(route) {
                // Make a request to the server-side script to initiate the download
                window.location.href = route;
            }
        </script>
    </x-slot>
</x-ladmin-auth-layout>
