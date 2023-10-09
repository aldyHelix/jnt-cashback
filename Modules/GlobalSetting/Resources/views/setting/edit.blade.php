<x-ladmin-auth-layout>
    <x-slot name="title">General Setting Detail</x-slot>

    <form action="{{ route('ladmin.globalsetting.setting.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('globalsetting::setting._partials._form', ['data' => $data] )

        <div class="text-end">
            <x-ladmin-button>Update</x-ladmin-button>
        </div>

    </form>

</x-ladmin-auth-layout>
