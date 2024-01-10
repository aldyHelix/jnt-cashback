
<table class="table">
    <thead>
        <tr>
            <th>Periode</th>
            <th>File Name</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @if($files->count() > 0)
            @foreach ($files as $file)
            <tr>
                <td>
                    <strong class="text-primary">
                        {{ $file->schema_name }}
                    </strong>
                </td>
                <td>
                    {{ $file->file_name }}
                </td>
                <td>
                    {{ $file->is_imported ? 'Data imported' : 'On Process' }}
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">No file uploaded are currently processed</td>
            </tr>
        @endif
    </tbody>

</table>
