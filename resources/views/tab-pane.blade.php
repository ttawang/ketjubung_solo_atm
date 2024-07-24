<div class="nav-tabs-horizontal" data-plugin="tabs">
    <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
        <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#exampleTabsLineLeftOne"
                aria-controls="exampleTabsLineLeftOne" role="tab"><i class="icon md-truck mr-2"></i> Order</a>
        </li>
        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsLineLeftTwo"
                aria-controls="exampleTabsLineLeftTwo" role="tab"><i class="icon md-check mr-2"></i>Terima</a>
        </li>
    </ul>
    <div class="tab-content py-20">
        <div class="tab-pane active" id="exampleTabsLineLeftOne" role="tabpanel">
            <div class="form-group row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-classic"
                        onclick="addForm($(this));">
                        <i class="icon md-plus mr-2"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="exampleAddRow">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>Gudang Bahan Baku</th>
                                <th>Jenis Bahan</th>
                                <th>Volume</th>
                                <th>Penyedia</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="exampleTabsLineLeftTwo" role="tabpanel">
            Mnesarchum velit cumanum utuntur tantam deterritum, democritum vulgo contumeliae
            abest studuisse quanta telos. Inmensae. Arbitratu dixisset
            invidiae ferre constituto gaudeat contentam, omnium nescius,
            consistat interesse animi, amet fuisset numen graecos incidunt
            euripidis praesens, homines religionis dirigentur postulant.
            Magnum utrumvis gravitate appareat fabulae facio perveniri
            fruenda indicaverunt texit, frequenter probet diligenter
            sententia meam distinctio theseo legerint corporis quoquo,
            optari futurove expedita.
        </div>
    </div>
</div>


<table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableDetail">
    <thead>
        <tr>
            <th rowspan="2" width="30px">No</th>
            <th rowspan="2">Jenis Barang</th>
            <th rowspan="2">Kode Warna</th>
            <th rowspan="2">Lot</th>
            {{-- <th rowspan="2">Satuan</th> --}}
            <th colspan="3">Proses Produksi</th>
            <th rowspan="2">BPHD</th>
            <th rowspan="2">Aksi</th>
        </tr>
        <tr>
            <th>Softcone</th>
            <th>Dye & Dry</th>
            <th>Overcone</th>
        </tr>
    </thead>
    <tbody>
        <td>1</td>
        <td>Ry. 30s</td>
        <td>B06</td>
        <td>F</td>
        {{-- <td></td> --}}
        <td>08/04/2023</td>
        <td>11/04/2023</td>
        <td>12/04/2023</td>
        <td>13/04/2023</td>
        <td>
            <a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" data-original-title="Edit">
                <i class="icon md-edit" aria-hidden="true"></i>
            </a>
            <a href="javascript:void(0);"
                class="btn btn-sm btn-icon btn-pure btn-default on-default waves-effect waves-classic"
                data-toggle="tooltip" data-original-title="Delete">
                <i class="icon md-delete" aria-hidden="true"></i>
            </a>
        </td>
    </tbody>
</table>