<form action="{{ route('production.absensi_pekerja.store') }}" onsubmit="submitForm(event, $(this));" method="POST"
    class="formInput" style="height: 450px;">
    <div class="form-group">
        <label>Tanggal</label>
        <div class="input-group">
            <div id="reportrange"
                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <i class="icon fa-calendar"></i>&nbsp;
                <span></span> <i class="icon fa-caret-down"></i>
            </div>
            <input type="hidden" name="range">
        </div>
    </div>
    <div class="form-group">
        <label>Shift</label>
        <div class="radio-custom radio-primary">
            <input type="radio" id="inputShiftAuto" onchange="changeShift($(this));" value="auto" name="inputShift"
                checked>
            <label for="inputShiftAuto">Auto</label>
        </div>
        @foreach ($absensiShift as $item)
            Group {{ $loop->iteration }} <span class="badge badge-outline badge-primary">{{ $item }}</span> |
        @endforeach
        <div class="radio-custom radio-primary">
            <input type="radio" id="inputShiftManual" onchange="changeShift($(this));" value="manual"
                name="inputShift">
            <label for="inputShiftManual">Manual</label>
        </div>
    </div>
    <div id="wrapperShiftManual"></div>
</form>
