<x-ladmin-auth-layout>
    <x-slot name="title">Setting Sumber Waybill</x-slot>
    {{-- @can(['ladmin.globalsetting.sumberwaybill.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.globalsetting.setting.create', ladmin()->back()) }}" class="btn btn-primary">&plus; Buat General Setting</a>
    </x-slot>
    @endcan --}}
    <x-ladmin-card>
        <x-slot name="body">
            <div class="row">
                <div class="col-md-5 p-3 bg-dark offset-md-1">
                    <ul class="list-group shadow-lg connectedSortable" id="padding-item-drop">
                      @if(!empty($sumber_waybill) && count($sumber_waybill))
                        @foreach($sumber_waybill as $key => $value)
                          <li class="list-group-item" item-id="{{ $value }}">{{ $value}}</li>
                        @endforeach
                      @endif
                    </ul>
                </div>
                <div class="col-md-5 p-3 bg-dark offset-md-1 shadow-lg complete-item">
                    <ul class="list-group  connectedSortable" id="complete-item-drop">
                      {{-- @if(!empty($completeItem) && $completeItem->count())
                        @foreach($completeItem as $key => $value)
                          <li class="list-group-item " item-id="{{ $value->id }}">{{ $value->title }}</li>
                        @endforeach
                      @endif --}}
                    </ul>
                </div>
            </div>
        </x-slot>
    </x-ladmin-card>
    <x-slot name="styles">
        {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> --}}
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            #draggable {
                width: 150px;
                height: 150px;
                padding: 0.5em;
            }
        </style>
    </x-slot>
    <x-slot name="scripts">
        <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
        <script>
            $( "#padding-item-drop, #complete-item-drop" ).sortable({
            connectWith: ".connectedSortable",
            opacity: 0.5,
            });
            $( ".connectedSortable" ).on( "sortupdate", function( event, ui ) {
                var pending = [];
                var accept = [];
                $("#padding-item-drop li").each(function( index ) {
                if($(this).attr('item-id')){
                    pending[index] = $(this).attr('item-id');
                }
                });
                $("#complete-item-drop li").each(function( index ) {
                accept[index] = $(this).attr('item-id');
                });
                $.ajax({
                    url: "",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {pending:pending,accept:accept},
                    success: function(data) {
                    console.log('success');
                    }
                });

            });
        </script>
    </x-slot>
</x-ladmin-auth-layout>
