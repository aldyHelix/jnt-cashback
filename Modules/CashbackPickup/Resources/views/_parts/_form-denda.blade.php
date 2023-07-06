<a href="" data-bs-toggle="modal" class="btn btn-sm btn-outline-primary" data-bs-target="#modal-denda-role-{{ $id }}">
    Setting Denda
</a>

<x-ladmin-modal id="modal-denda-role-{{ $id }}" class="text-start">
    <x-slot name="title">Setting Denda Grading {{ $grading }}</x-slot>
    <x-slot name="body">
    <form action="{{ route('ladmin.cashbackpickup.denda') }}" method="POST">
        @csrf
            <input type="hidden" name="periode_id" value="{{$id}}">
            <input type="hidden" name="grading_type" value="{{$grading}}">
            <div class="row d-flex align-items-center">
                <label for="sprinter_pickup" class="form-label col-lg-3">Sprinter Pickup <span class="text-danger">*</span></label>
                <x-ladmin-input id="sprinter_pickup" type="text" class="mb-3 col" required name="sprinter_pickup"
                    value="{{ old('sprinter_pickup', $denda->sprinter_pickup) }}" placeholder="Sprinter Pickup" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="transit_fee" class="form-label col-lg-3">Transit Fee <span class="text-danger">*</span></label>
                <x-ladmin-input id="transit_fee" type="text" class="mb-3 col" required name="transit_fee"
                    value="{{ old('transit_fee', $denda->transit_fee) }}" placeholder="Transit Fee" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="denda_void" class="form-label col-lg-3">Denda Void <span class="text-danger">*</span></label>
                <x-ladmin-input id="denda_void" type="text" class="mb-3 col" required name="denda_void"
                    value="{{ old('denda_void', $denda->denda_void) }}" placeholder="Denda Void" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="denda_dfod" class="form-label col-lg-3">Denda DFOD <span class="text-danger">*</span></label>
                <x-ladmin-input id="denda_dfod" type="text" class="mb-3 col" required name="denda_dfod"
                    value="{{ old('denda_dfod', $denda->denda_dfod) }}" placeholder="Denda DFOD" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="denda_pusat" class="form-label col-lg-3">Denda Pusat <span class="text-danger">*</span></label>
                <x-ladmin-input id="denda_pusat" type="text" class="mb-3 col" required name="denda_pusat"
                    value="{{ old('denda_pusat', $denda->denda_pusat) }}" placeholder="Denda Pusat" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="denda_selisih_berat" class="form-label col-lg-3">Denda Selisih Berat <span class="text-danger">*</span></label>
                <x-ladmin-input id="denda_selisih_berat" type="text" class="mb-3 col" required name="denda_selisih_berat"
                    value="{{ old('denda_selisih_berat', $denda->denda_selisih_berat) }}" placeholder="Denda Selisih Berat" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="denda_lost_scan_kirim" class="form-label col-lg-3">Denda Lost Scan Kirim <span class="text-danger">*</span></label>
                <x-ladmin-input id="denda_lost_scan_kirim" type="text" class="mb-3 col" required name="denda_lost_scan_kirim"
                    value="{{ old('denda_lost_scan_kirim', $denda->denda_lost_scan_kirim) }}" placeholder="Denda Lost Scan Kirim" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="denda_auto_claim" class="form-label col-lg-3">Denda Auto Claim <span class="text-danger">*</span></label>
                <x-ladmin-input id="denda_auto_claim" type="text" class="mb-3 col" required name="denda_auto_claim"
                    value="{{ old('denda_auto_claim', $denda->denda_auto_claim) }}" placeholder="Denda Auto Claim" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="denda_sponsorship" class="form-label col-lg-3">Denda Sponsorship <span class="text-danger">*</span></label>
                <x-ladmin-input id="denda_sponsorship" type="text" class="mb-3 col" required name="denda_sponsorship"
                    value="{{ old('denda_sponsorship', $denda->denda_sponsorship) }}" placeholder="Denda Sponsorship" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="denda_late_pickup_ecommerce" class="form-label col-lg-3">Denda Late Pickup Ecommerce <span class="text-danger">*</span></label>
                <x-ladmin-input id="denda_late_pickup_ecommerce" type="text" class="mb-3 col" required name="denda_late_pickup_ecommerce"
                    value="{{ old('denda_late_pickup_ecommerce', $denda->denda_late_pickup_ecommerce) }}" placeholder="Denda Late Pickup Ecommerce" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="potongan_pop" class="form-label col-lg-3">Denda Potongan POP <span class="text-danger">*</span></label>
                <x-ladmin-input id="potongan_pop" type="text" class="mb-3 col" required name="potongan_pop"
                    value="{{ old('potongan_pop', $denda->potongan_pop) }}" placeholder="Denda Potongan POP" />
            </div>

            <div class="row d-flex align-items-center">
                <label for="denda_lainnya" class="form-label col-lg-3">Denda Lainnya <span class="text-danger">*</span></label>
                <x-ladmin-input id="denda_lainnya" type="text" class="mb-3 col" required name="denda_lainnya"
                value="{{ old('denda_lainnya', $denda->denda_lainnya) }}" placeholder="Denda Lainnya" />
            </div>

            <x-ladmin-button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</x-ladmin-button>
            <x-ladmin-button type="submit" class="text-white" color="primary">Simpan</x-ladmin-button>
            </form>
        </x-slot>
</x-ladmin-modal>
