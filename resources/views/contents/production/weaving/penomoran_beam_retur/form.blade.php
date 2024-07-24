<div class="panel-body container-fluid">
    <form
        action="{{ isset($id) ? route('production.penomoran_beam_retur.update', $id) : route('production.penomoran_beam_retur.store') }}"
        onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
        @if (isset($id))
            @method('PATCH')
        @endif
        <input type="hidden" id="idDetail" value="{{ $id ?? '' }}">
        <input type="hidden" name="input[code]">
        <input type="hidden" name="id_penomoran_baru_retur" value="{{ $data->id_beam_retur ?? '' }}">
        <input type="hidden" name="id_nomor_kikw"
            value="{{ !empty($data) ? $data->relBeam()->value('id_nomor_kikw') : '' }}">
        <input type="hidden" name="id_beam_baru" value="{{ $data->id_beam ?? '' }}">
        <input type="hidden" name="id_mesin_current" value="{{ $data->id_mesin ?? '' }}">
        <input type="hidden" name="id_log_stok_keluar"
            value="{{ !empty($data) ? $data->relPenomoranBeamReturDetail()->value('id_log_stok_penerimaan') : '' }}">
        <input type="hidden" name="id_log_stok_masuk" value="{{ !empty($data) ? $data->id_log_stok_penerimaan : '' }}">
        <div class="form-group row row-lg">
            <div class="col-md-12 col-lg-6">
                <div class="form-group form-material row">
                    <label class="col-md-3 col-form-label">Tanggal: </label>
                    <div class="col-md-9">
                        <input type="date" value="{{ $data->tanggal ?? date('Y-m-d') }}" class="form-control"
                            name="tanggal" required />
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row row-lg">
            <div class="col-md-12 col-lg-6">
                @include('contents.production.weaving.penomoran_beam_retur.form-beam-retur')
            </div>

            <div class="col-md-12 col-lg-6">
                @include('contents.production.weaving.penomoran_beam_retur.form-beam-baru')
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-12">
                <button type='button' class='btn btn-default btn-sm waves-effect waves-classic'
                    onclick='closeForm($(this));'>
                    <i class='icon md-arrow-left mr-2'></i> Kembali
                </button>
                <button type='submit' id="btnSubmit"
                    class='btn btn-primary btn-sm waves-effect waves-classic float-right'
                    @if (!isset($id)) disabled @endif>
                    <i class='icon md-check mr-2'></i> Submit
                </button>
            </div>
        </div>
    </form>
</div>
