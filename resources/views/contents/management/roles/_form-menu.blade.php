<div class="panel-body">
    <div class="form-group row" id="showAlert">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon md-alert-circle-o mr-2"></i> Peringatan!</h5>
                Jika data diubah, untuk mengetahui perubahannya silahkan untuk <a href="javascript:void(0);"
                    onclick='location.reload(true); return false;'>refresh</a> halaman ini.
            </div>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" id="id_group" value="{{ $id }}">
        @foreach ($templateMenu as $prefix => $parents)
            <div class="col-md-4">
                <div class="form-group">
                    <div class="form-group">
                        <h6><i class="fas fa-folder mr-2"></i>{{ strtoupper($prefix) }}</h6>
                        <hr>
                    </div>
                    @foreach ($parents as $parent)
                        @php
                            $i = $loop->iteration;
                        @endphp
                        <div class="form-group row clearfix px-3">
                            <span class="col-sm-6 col-form-label"><span
                                    class="col-form-label mr-2">{{ $i }}.</span>
                                {{ $parent['display_name'] }}</span>
                            <div class="col-sm-6">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" id="checkbox{{ $parent['name'] }}{{ $i }}"
                                        {{ $parent['selected'] ? 'checked' : '' }} onchange="checkedBox($(this))"
                                        value="{{ $parent['id'] }}">
                                    <label for="checkbox{{ $parent['name'] }}{{ $i }}"></label>
                                </div>
                            </div>
                        </div>
                        @foreach ($parent['childs'] as $j => $child)
                            @php
                                $j = $loop->iteration;
                            @endphp
                            <div class="form-group row clearfix px-5">
                                <span class="col-sm-6 col-form-label"><span
                                        class="col-form-label mr-2">{{ $i . '.' . $j }}.</span>
                                    {{ $child['display_name'] }}</span>
                                <div class="col-sm-6">
                                    <div class="icheck-success d-inline">
                                        <input type="checkbox" id="checkbox{{ $child['name'] }}{{ $j }}"
                                            {{ $child['selected'] ? 'checked' : '' }} onchange="checkedBox($(this))"
                                            value="{{ $child['id'] }}">
                                        <label for="checkbox{{ $child['name'] }}{{ $j }}"></label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            <button type="button" class="btn btn-default btn-sm waves-effect waves-classic"
                onclick="closeForm($(this));">
                <i class="icon md-arrow-left mr-2"></i> Kembali
            </button>
        </div>
    </div>
</div>
