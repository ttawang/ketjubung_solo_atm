<form action="{{ isset($id) ? route('production.tenun.update', $id) : route('production.tenun.store') }}"
    onsubmit="submitFormDetail(event, $(this));" method="POST" class="formInput" style="height: 450px;">
    @if (isset($id))
        @method('PATCH')
    @endif
    <input type="hidden" id="idDetail" value="{{ $id ?? '' }}">
    <input type="hidden" name="isDetail" value="true">
    <input type="hidden" name="form" value="{{ $attr['form'] }}">
    <input type="hidden" name="tab" value="{{ $attr['tab'] }}">
    <input type="hidden" name="input[id_tenun]" value="{{ $idParent }}">
    <input type="hidden" name="input[id_beam]" value="{{ $data->id_beam }}">
    <input type="hidden" id="no_kikw" value="{{ $data->throughNomorKikw()->value('name') ?? '' }}">
    @include("contents.production.weaving.tenun.form-detail-{$attr['form']}");
</form>
