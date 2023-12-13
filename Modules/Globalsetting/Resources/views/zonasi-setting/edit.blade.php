<x-ladmin-auth-layout>
    <x-slot name="title">Setting Zonasi Detail</x-slot>

    <form action="{{ route('ladmin.ratesetting.grade.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('globalsetting::_parts._form', ['data' => $data] )


        <div class="text-end">
            <x-ladmin-button>Update</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
