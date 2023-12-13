<x-ladmin-auth-layout>
    <x-slot name="title">Setting Zonasi</x-slot>
    @can(['ladmin.globalsetting.setting.create'])
    <x-slot name="button">
        <a href="{{ route('ladmin.globalsetting.setting.create', ladmin()->back()) }}" class="btn btn-primary">&plus; Buat General Setting</a>
    </x-slot>
    @endcan
    <x-ladmin-card>
        <x-slot name="body">
            {{ Modules\Globalsetting\Datatables\SettingDatatables::table() }}
        </x-slot>
    </x-ladmin-card>
    <x-slot name="scripts">
    </x-slot>
    <x-slot name="scripts">
    </x-slot>
</x-ladmin-auth-layout>
