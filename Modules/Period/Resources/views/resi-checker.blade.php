<x-ladmin-auth-layout>
    <x-slot name="title">Resi Checker Validation</x-slot>

    <x-ladmin-card>
        <x-slot name="body">
                <input type="file" name="file" id="file">
                <input type="hidden" name="_token" id="uploadToken" value="{{ csrf_token() }}">
                <select name="periode" id="periode">
                    @foreach ($periode as $item)
                        <option value="{{ $item->code }}">{{ $item->month }} - {{ $item->year}}</option>
                    @endforeach
                </select>

                <button class="btn btn-primary" id="process">Process</button>

            <div id="resi-list"></div>
        </x-slot>


    </x-ladmin-card>

    <x-slot name="scripts">
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('process').addEventListener('click', function() {
                    let fileInput = document.getElementById('file');
                    let file = fileInput.files[0];
                    let periode = document.getElementById('periode').value;
                    let token = document.getElementById('uploadToken').value;

                    let formData = new FormData();
                    formData.append('file', file);
                    formData.append('periode', periode);
                    formData.append('_token', token);

                    let xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route('ladmin.period.resi-checker-process') }}', true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                let response = JSON.parse(xhr.responseText);

                                console.log(response);
                            } else {
                                console.error('Error:', xhr.status);
                            }
                        }
                    };
                    xhr.send(formData);
                });
            });
        </script>
    </x-slot>
</x-ladmin-auth-layout>
