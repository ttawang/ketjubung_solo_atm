<?php

function mutasiProduksi($request)
{
    $mode = $request->mode ?? null;
    $tgl_awal = $request->tgl_awal ?? date('Y-m-d');
    $tgl_akhir = $request->tgl_akhir ?? date('Y-m-d');
    $proses = $request->proses ?? null;
    $sql = "";
    if ($mode == 'produksi') {
        $sql = "SELECT
                data.*,
                gudang.name nama_gudang,
                barang.name nama_barang,
                warna.alias nama_warna,
                motif.alias nama_motif,
                grade.grade nama_grade,
                kualitas.kode nama_kualitas,
                mesin.name nama_mesin,
                satuan1.name nama_satuan_1,
                satuan2.name nama_satuan_2
                FROM
                (
                SELECT
                1 sort,
                'pengiriman_barang' proses,
                log.code,
                data.id_log_stok id_log,
                data.id_pengiriman_barang id_spk,
                parent.nomor,
                data.tanggal,
                data.id,
                data.id_parent_detail id_parent,
                CASE
                    WHEN status = 'ASAL' THEN 'keluar'
                    ELSE 'masuk'
                END AS status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                data.id_beam,
                data.id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                data.tipe_pra_tenun
                FROM tbl_pengiriman_barang_detail AS data
                LEFT JOIN tbl_pengiriman_barang AS parent ON parent.id = data.id_pengiriman_barang
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                2 sort,
                'dyeing_softcone' proses,
                log.code,
                log.id id_log,
                data.id_dyeing id_spk,
                parent.no_kikd nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                'keluar' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_keluar_1 volume_1,
                log.volume_keluar_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_dyeing_detail AS data
                LEFT JOIN tbl_dyeing AS parent ON parent.id = data.id_dyeing
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_keluar
                WHERE data.status = 'SOFTCONE'
                AND data.deleted_at IS NULL

                UNION

                SELECT
                2 sort,
                'dyeing_softcone' proses,
                log.code,
                log.id id_log,
                data.id_dyeing id_spk,
                parent.no_kikd nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                'masuk' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_masuk_1 volume_1,
                log.volume_masuk_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_dyeing_detail AS data
                LEFT JOIN tbl_dyeing AS parent ON parent.id = data.id_dyeing
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_masuk
                WHERE data.status = 'SOFTCONE'
                AND data.deleted_at IS NULL

                UNION

                SELECT
                3 sort,
                'dyeing_dyeoven' proses,
                log.code,
                log.id id_log,
                data.id_dyeing id_spk,
                parent.no_kikd nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                'keluar' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_keluar_1 volume_1,
                log.volume_keluar_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_dyeing_detail AS data
                LEFT JOIN tbl_dyeing AS parent ON parent.id = data.id_dyeing
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_keluar
                WHERE data.status = 'DYEOVEN'
                AND data.deleted_at IS NULL

                UNION

                SELECT
                3 sort,
                'dyeing_dyeoven' proses,
                log.code,
                log.id id_log,
                data.id_dyeing id_spk,
                parent.no_kikd nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                'masuk' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_masuk_1 volume_1,
                log.volume_masuk_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_dyeing_detail AS data
                LEFT JOIN tbl_dyeing AS parent ON parent.id = data.id_dyeing
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_masuk
                WHERE data.status = 'DYEOVEN'
                AND data.deleted_at IS NULL

                UNION

                SELECT
                4 sort,
                'dyeing_overcone' proses,
                log.code,
                log.id id_log,
                data.id_dyeing id_spk,
                parent.no_kikd nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                'keluar' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_keluar_1 volume_1,
                log.volume_keluar_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_dyeing_detail AS data
                LEFT JOIN tbl_dyeing AS parent ON parent.id = data.id_dyeing
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_keluar
                WHERE data.status = 'OVERCONE'
                AND data.deleted_at IS NULL

                UNION

                SELECT
                4 sort,
                'dyeing_overcone' proses,
                log.code,
                log.id id_log,
                data.id_dyeing id_spk,
                parent.no_kikd nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                'masuk' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_masuk_1 volume_1,
                log.volume_masuk_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_dyeing_detail AS data
                LEFT JOIN tbl_dyeing AS parent ON parent.id = data.id_dyeing
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_masuk
                WHERE data.status = 'OVERCONE'
                AND data.deleted_at IS NULL

                UNION

                SELECT
                5 sort,
                'warping' proses,
                data.code,
                data.id_log_stok_penerimaan id_log,
                data.id_warping id_spk,
                CONCAT('Mesin ',mesin.name) nomor,
                data.tanggal,
                data.id,
                NULL::BIGINT id_parent,
                CASE
                    WHEN data.code = 'BBW' THEN 'keluar'
                    ELSE 'masuk'
                END status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                NULL::BIGINT id_grade,
                NULL::BIGINT id_kualitas,
                data.id_beam,
                data.id_mesin id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_warping_detail AS data
                LEFT JOIN tbl_warping AS parent ON parent.id = data.id_warping
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = parent.id_mesin
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                6 sort,
                'pakan' proses,
                data.code,
                data.id_log_stok_penerimaan id_log,
                data.id_pakan id_spk,
                parent.nomor nomor,
                data.tanggal,
                data.id,
                NULL::BIGINT id_parent,
                CASE
                    WHEN data.code = 'BBW' THEN 'keluar'
                    ELSE 'masuk'
                END status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                NULL::BIGINT id_motif,
                NULL::BIGINT id_grade,
                NULL::BIGINT id_kualitas,
                NULL::BIGINT id_beam,
                NULL::BIGINT id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_pakan_detail AS data
                LEFT JOIN tbl_pakan AS parent ON parent.id = data.id_pakan
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                7 sort,
                'leno' proses,
                data.code,
                data.id_log_stok_penerimaan id_log,
                data.id_leno id_spk,
                parent.nomor nomor,
                data.tanggal,
                data.id,
                NULL::BIGINT id_parent,
                CASE
                WHEN data.code = 'BHDR' THEN 'keluar'
                    ELSE 'masuk'
                END status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                NULL::BIGINT id_motif,
                NULL::BIGINT id_grade,
                NULL::BIGINT id_kualitas,
                NULL::BIGINT id_beam,
                NULL::BIGINT id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_leno_detail AS data
                LEFT JOIN tbl_leno AS parent ON parent.id = data.id_leno
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                8 sort,
                'sizing' proses,
                data.code,
                data.id_log_stok_penerimaan id_log,
                data.id_sizing id_spk,
                parent.no_sizing nomor,
                data.tanggal,
                data.id,
                data.id_parent id_parent,
                CASE
                WHEN data.code = 'BL' THEN 'keluar'
                    ELSE 'masuk'
                END status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                CASE WHEN data.code = 'BL' THEN log.volume_keluar_1 ELSE log.volume_masuk_1 END volume_1,
                CASE WHEN data.code = 'BL' THEN log.volume_keluar_2 ELSE log.volume_masuk_2 END volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_sizing_detail AS data
                LEFT JOIN tbl_sizing AS parent ON parent.id = data.id_sizing
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                9 sort,
                'cucuk' proses,
                log.code,
                log.id id_log,
                NULL id_spk,
                NULL nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'keluar' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_keluar_1 volume_1,
                log.volume_keluar_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_cucuk AS data
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_keluar
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                9 sort,
                'cucuk' proses,
                log.code,
                log.id id_log,
                NULL id_spk,
                NULL nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'masuk' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_masuk_1 volume_1,
                log.volume_masuk_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_cucuk AS data
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_masuk
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                10 sort,
                'tyeing' proses,
                log.code,
                log.id id_log,
                NULL id_spk,
                NULL nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'keluar' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_keluar_1 volume_1,
                log.volume_keluar_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_tyeing AS data
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_keluar
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                10 sort,
                'tyeing' proses,
                log.code,
                log.id id_log,
                NULL id_spk,
                NULL nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'masuk' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_masuk_1 volume_1,
                log.volume_masuk_2 volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_tyeing AS data
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_masuk
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                11 sort,
                'tenun' proses,
                log.code,
                log.id id_log,
                data.id_tenun id_spk,
                CONCAT(kikw.name,' | ',mesin.name) nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                CASE WHEN log.code = 'BG' THEN 'masuk' ELSE 'keluar' END status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                data.volume_1,
                data.volume_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_tenun_detail AS data
                LEFT JOIN tbl_tenun AS parent ON parent.id = data.id_tenun
                LEFT JOIN tbl_beam AS beam ON beam.id = parent.id_beam
                LEFT JOIN tbl_nomor_kikw AS kikw ON kikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_tenun_detail AS detail ON detail.code = 'BBTL' AND detail.id_beam = parent.id_beam
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = detail.id_mesin
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                12 sort,
                'inspecting_grey' proses,
                data.code,
                data.id_log_stok_penerimaan id_log,
                data.id_tenun id_spk,
                CONCAT(nokikw.name,' | ',mesin.name) nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'keluar' status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                NULL id_grade,
                NULL id_kualitas,
                data.id_beam,
                data.id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_tenun_detail AS data
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN tbl_nomor_kikw AS nokikw ON nokikw.id = beam.id_nomor_kikw
                WHERE data.deleted_at IS NULL AND data.code = 'BG'

                UNION

                SELECT
                12 sort,
                'inspecting_grey' proses,
                log.code,
                NULL id_log,
                tenun.id id_spk,
                CONCAT(nokikw.name,' | ',mesin.name) nomor,
                data.tanggal,
                NULL::BIGINT id,
                NULL id_parent,
                'masuk' status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                NULL id_grade,
                NULL id_kualitas,
                data.id_beam,
                data.id_mesin,
                SUM(data.volume_1) volume_1,
                SUM(data.volume_2) volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_inspecting_grey_detail AS data
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan
                LEFT JOIN tbl_tenun_detail AS tenundetail ON tenundetail.id = data.id_tenun_detail
                LEFT JOIN tbl_tenun AS tenun ON tenun.id = tenundetail.id_tenun
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_nomor_kikw AS nokikw ON nokikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                WHERE data.deleted_at IS NULL
                GROUP BY
                log.code,
                tenun.id,
                data.tanggal,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_beam,
                data.id_mesin,
                data.id_satuan_1,
                data.id_satuan_2,
                nokikw.name,
                mesin.name

                UNION

                SELECT
                13 sort,
                'dudulan' proses,
                data.code,
                data.id_log_stok_penerimaan id_log,
                data.id_dudulan id_spk,
                parent.nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                CASE WHEN data.code = 'BGIG' THEN 'keluar' ELSE 'masuk' END status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                data.id_beam,
                data.id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_dudulan_detail AS data
                LEFT JOIN tbl_dudulan AS parent ON parent.id = data.id_dudulan
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                14 sort,
                'inspecting_dudulan' proses,
                log.code,
                log.id id_log,
                parent.id id_spk,
                parent.nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'keluar' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_keluar_1,
                log.volume_keluar_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_inspect_dudulan_detail AS data
                LEFT JOIN tbl_dudulan AS parent ON parent.id = data.id_dudulan
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan_keluar
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                14 sort,
                'inspecting_dudulan' proses,
                log.code,
                log.id id_log,
                parent.id id_spk,
                parent.nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'masuk' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_masuk_1,
                log.volume_masuk_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_inspect_dudulan_detail AS data
                LEFT JOIN tbl_dudulan AS parent ON parent.id = data.id_dudulan
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan_masuk
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                15 sort,
                'jahit_sambung' proses,
                log.code,
                log.id id_log,
                CONCAT(data.id_beam,data.id_mesin)::BIGINT id_spk,
                CASE 
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NOT NULL THEN CONCAT(nokikw.name,' | ',mesin.name)
                    WHEN  data.id_beam IS NULL AND data.id_mesin IS NOT NULL THEN CONCAT(mesin.name)
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NULL THEN CONCAT(nokikw.name)
                    ELSE NULL
                END nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'keluar' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_keluar_1,
                log.volume_keluar_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_jahit_sambung_detail AS data
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_nomor_kikw AS nokikw ON nokikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan_keluar
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                15 sort,
                'jahit_sambung' proses,
                log.code,
                log.id id_log,
                CONCAT(data.id_beam,data.id_mesin)::BIGINT id_spk,
                CASE 
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NOT NULL THEN CONCAT(nokikw.name,' | ',mesin.name)
                    WHEN  data.id_beam IS NULL AND data.id_mesin IS NOT NULL THEN CONCAT(mesin.name)
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NULL THEN CONCAT(nokikw.name)
                    ELSE NULL
                END nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'masuk' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_masuk_1,
                log.volume_masuk_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_jahit_sambung_detail AS data
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_nomor_kikw AS nokikw ON nokikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan_masuk
                WHERE data.deleted_at IS NULL
                
                UNION

                SELECT
                16 sort,
                'p1' proses,
                log.code,
                log.id id_log,
                parent.id id_spk,
                parent.nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                CASE WHEN data.code = 'P1' THEN 'masuk' ELSE 'keluar' END status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                data.id_beam,
                data.id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_p1_detail AS data
                LEFT JOIN tbl_p1 AS parent ON parent.id = data.id_p1
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                17 sort,
                'finishing_cabut' proses,
                log.code,
                log.id id_log,
                parent.id id_spk,
                parent.nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                CASE WHEN data.code = 'FC' THEN 'masuk' ELSE 'keluar' END status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                data.id_beam,
                data.id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_finishing_cabut_detail AS data
                LEFT JOIN tbl_finishing_cabut AS parent ON parent.id = data.id_finishing_cabut
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                18 sort,
                'drying' proses,
                log.code,
                log.id id_log,
                CONCAT(data.id_beam,data.id_mesin)::BIGINT id_spk,
                CASE 
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NOT NULL THEN CONCAT(nokikw.name,' | ',mesin.name)
                    WHEN  data.id_beam IS NULL AND data.id_mesin IS NOT NULL THEN CONCAT(mesin.name)
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NULL THEN CONCAT(nokikw.name)
                    ELSE NULL
                END nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'keluar' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_keluar_1,
                log.volume_keluar_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_drying_detail AS data
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_nomor_kikw AS nokikw ON nokikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan_keluar
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                18 sort,
                'drying' proses,
                log.code,
                log.id id_log,
                CONCAT(data.id_beam,data.id_mesin)::BIGINT id_spk,
                CASE 
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NOT NULL THEN CONCAT(nokikw.name,' | ',mesin.name)
                    WHEN  data.id_beam IS NULL AND data.id_mesin IS NOT NULL THEN CONCAT(mesin.name)
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NULL THEN CONCAT(nokikw.name)
                    ELSE NULL
                END nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'masuk' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_masuk_1,
                log.volume_masuk_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_drying_detail AS data
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_nomor_kikw AS nokikw ON nokikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan_masuk
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                19 sort,
                'jigger' proses,
                log.code,
                log.id id_log,
                CONCAT(data.id_beam,data.id_mesin)::BIGINT id_spk,
                CASE 
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NOT NULL THEN CONCAT(nokikw.name,' | ',mesin.name)
                    WHEN  data.id_beam IS NULL AND data.id_mesin IS NOT NULL THEN CONCAT(mesin.name)
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NULL THEN CONCAT(nokikw.name)
                    ELSE NULL
                END nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'keluar' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_keluar_1,
                log.volume_keluar_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_jigger_detail AS data
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_nomor_kikw AS nokikw ON nokikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan_keluar
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                19 sort,
                'jigger' proses,
                log.code,
                log.id id_log,
                CONCAT(data.id_beam,data.id_mesin)::BIGINT id_spk,
                CASE 
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NOT NULL THEN CONCAT(nokikw.name,' | ',mesin.name)
                    WHEN  data.id_beam IS NULL AND data.id_mesin IS NOT NULL THEN CONCAT(mesin.name)
                    WHEN data.id_beam IS NOT NULL AND data.id_mesin IS NULL THEN CONCAT(nokikw.name)
                    ELSE NULL
                END nomor,
                data.tanggal,
                data.id,
                NULL id_parent,
                'masuk' status,
                log.id_gudang,
                log.id_barang,
                log.id_warna,
                log.id_motif,
                log.id_grade,
                log.id_kualitas,
                log.id_beam,
                log.id_mesin,
                log.volume_masuk_1,
                log.volume_masuk_2,
                log.id_satuan_1,
                log.id_satuan_2,
                log.tipe_pra_tenun
                FROM tbl_jigger_detail AS data
                LEFT JOIN tbl_beam AS beam ON beam.id = data.id_beam
                LEFT JOIN tbl_nomor_kikw AS nokikw ON nokikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = data.id_mesin
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan_masuk
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                20 sort,
                'p2' proses,
                log.code,
                log.id id_log,
                parent.id id_spk,
                parent.nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                CASE WHEN data.code = 'P2' THEN 'masuk' ELSE 'keluar' END status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                data.id_beam,
                data.id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_p2_detail AS data
                LEFT JOIN tbl_p2 AS parent ON parent.id = data.id_p2
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan
                WHERE data.deleted_at IS NULL

                UNION

                SELECT
                21 sort,
                'jahit_p2' proses,
                log.code,
                log.id id_log,
                parent.id id_spk,
                parent.nomor,
                data.tanggal,
                data.id,
                data.id_parent,
                CASE WHEN data.code = 'JP2' THEN 'masuk' ELSE 'keluar' END status,
                data.id_gudang,
                data.id_barang,
                data.id_warna,
                data.id_motif,
                data.id_grade,
                data.id_kualitas,
                data.id_beam,
                data.id_mesin,
                data.volume_1,
                data.volume_2,
                data.id_satuan_1,
                data.id_satuan_2,
                NULL tipe_pra_tenun
                FROM tbl_jahit_p2_detail AS data
                LEFT JOIN tbl_jahit_p2 AS parent ON parent.id = data.id_jahit_p2
                LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok_penerimaan
                WHERE data.deleted_at IS NULL


                ) AS data
                LEFT JOIN tbl_gudang AS gudang ON gudang.deleted_at IS NULL AND gudang.id = data.id_gudang
                LEFT JOIN tbl_barang AS barang ON barang.deleted_at IS NULL AND barang.id = data.id_barang
                LEFT JOIN tbl_warna AS warna ON warna.deleted_at IS NULL AND warna.id = data.id_warna
                LEFT JOIN tbl_motif AS motif ON motif.deleted_at IS NULL AND motif.id = data.id_motif
                LEFT JOIN tbl_kualitas AS grade ON grade.deleted_at IS NULL AND grade.id = data.id_grade
                LEFT JOIN tbl_mapping_kualitas AS kualitas ON kualitas.deleted_at IS NULL AND kualitas.id = data.id_kualitas
                LEFT JOIN tbl_beam AS beam ON beam.deleted_at IS NULL AND beam.id = data.id_beam
                LEFT JOIN tbl_mesin AS mesin ON mesin.deleted_at IS NULL AND mesin.id = data.id_mesin
                LEFT JOIN tbl_satuan AS satuan1 ON satuan1.deleted_at IS NULL AND satuan1.id = data.id_satuan_1
                LEFT JOIN tbl_satuan AS satuan2 ON satuan2.deleted_at IS NULL AND satuan2.id = data.id_satuan_2
                ORDER BY
                data.sort,
                data.id_spk,
                data.tanggal,
                CASE
                    WHEN 
                        data.proses IN ('pengiriman_barang','sizing','dudulan','p1') AND
                        data.id_parent IS NULL
                        THEN data.id
                    WHEN
                        data.proses IN ('pengiriman_barang','sizing','dudulan','p1') AND
                        data.id_parent IS NOT NULL
                        THEN data.id_parent
                    ELSE data.id
                END,
                data.status
                --data.tanggal
        ";
    } elseif ($mode == 'rekap') {
        if ($proses == 'persediaan_benang_grey') {
            $sql = "SELECT
                barang.id id_barang,
                barang.name nama_barang,
                saldo_awal.id_gudang saldo_awal_id_gudang,
                saldo_awal_gudang.name saldo_awal_nama_gudang,
                saldo_awal.volume_1 saldo_awal_volume_1,
                saldo_awal.id_satuan_1 saldo_awal_id_satuan_1,
                saldo_awal_satuan_1.name saldo_awal_nama_satuan_1,
                saldo_awal.volume_2 saldo_awal_volume_2,
                saldo_awal.id_satuan_2 saldo_awal_id_satuan_2,
                saldo_awal_satuan_2.name saldo_awal_nama_satuan_2,
                masuk.id_gudang masuk_id_gudang,
                masuk_gudang.name masuk_nama_gudang,
                masuk.volume_1 masuk_volume_1,
                masuk.id_satuan_1 masuk_id_satuan_1,
                masuk_satuan_1.name masuk_nama_satuan_1,
                masuk.volume_2 masuk_volume_2,
                masuk.id_satuan_2 masuk_id_satuan_2,
                masuk_satuan_2.name masuk_nama_satuan_2,
                keluar.id_gudang keluar_id_gudang,
                keluar_gudang.name keluar_nama_gudang,
                keluar.volume_1 keluar_volume_1,
                keluar.id_satuan_1 keluar_id_satuan_1,
                keluar_satuan_1.name keluar_nama_satuan_1,
                keluar.volume_2 keluar_volume_2,
                keluar.id_satuan_2 keluar_id_satuan_2,
                keluar_satuan_2.name keluar_nama_satuan_2
                FROM tbl_barang AS barang
                
                LEFT JOIN
                (
                    SELECT
                    log.id_gudang,
                    log.id_barang,
                    SUM(log.volume_masuk_1::DECIMAL) volume_1,
                    log.id_satuan_1,
                    SUM(log.volume_masuk_2::DECIMAL) volume_2,
                    log.id_satuan_2
                    FROM
                    log_stok_penerimaan AS log
                    WHERE log.deleted_at IS NULL AND log.code = 'PB'  AND log.tanggal < '$tgl_awal
                ' GROUP BY log.id_barang, log.id_gudang, log.id_satuan_1, log.id_satuan_2
                ) AS saldo_awal ON saldo_awal.id_barang = barang.id
                
                LEFT JOIN
                (
                    SELECT
                    log.id_gudang,
                    log.id_barang,
                    SUM(log.volume_masuk_1::DECIMAL) volume_1,
                    log.id_satuan_1,
                    SUM(log.volume_masuk_2::DECIMAL) volume_2,
                    log.id_satuan_2
                    FROM
                    log_stok_penerimaan AS log
                    WHERE log.deleted_at IS NULL AND log.code = 'PB'  AND log.tanggal >= '$tgl_awal' AND log.tanggal <= '$tgl_akhir'
                    GROUP BY log.id_barang, log.id_gudang, log.id_satuan_1, log.id_satuan_2
                ) AS masuk ON masuk.id_barang = barang.id
                
                LEFT JOIN
                (
                    SELECT
                    data.id_barang,
                    parent.id_gudang_tujuan id_gudang,
                    SUM(volume_1::DECIMAL) volume_1,
                    data.id_satuan_1,
                    SUM(volume_2::DECIMAL) volume_2,
                    data.id_satuan_2
                    FROM tbl_pengiriman_barang_detail AS data
                    LEFT JOIN tbl_pengiriman_barang AS parent ON parent.id = data.id_pengiriman_barang
                    LEFT JOIN log_stok_penerimaan AS log ON log.id = data.id_log_stok
                    WHERE data.deleted_at IS NULL AND data.status = 'ASAL' AND log.code IN ('PB','BHDG') AND data.tanggal >= '$tgl_awal' AND data.tanggal <= '$tgl_akhir'
                    GROUP BY data.id_barang, parent.id_gudang_tujuan, data.id_satuan_1, data.id_satuan_2
                ) AS keluar ON keluar.id_barang = barang.id
                
                LEFT JOIN tbl_gudang AS saldo_awal_gudang ON saldo_awal_gudang.id = saldo_awal.id_gudang
                LEFT JOIN tbl_gudang AS masuk_gudang ON masuk_gudang.id = masuk.id_gudang
                LEFT JOIN tbl_gudang AS keluar_gudang ON keluar_gudang.id = keluar.id_gudang
                LEFT JOIN tbl_satuan AS saldo_awal_satuan_1 ON saldo_awal_satuan_1.id = saldo_awal.id_satuan_1
                LEFT JOIN tbl_satuan AS masuk_satuan_1 ON masuk_satuan_1.id = masuk.id_satuan_1
                LEFT JOIN tbl_satuan AS keluar_satuan_1 ON keluar_satuan_1.id = keluar.id_satuan_1
                LEFT JOIN tbl_satuan AS saldo_awal_satuan_2 ON saldo_awal_satuan_2.id = saldo_awal.id_satuan_2
                LEFT JOIN tbl_satuan AS masuk_satuan_2 ON masuk_satuan_2.id = masuk.id_satuan_2
                LEFT JOIN tbl_satuan AS keluar_satuan_2 ON keluar_satuan_2.id = keluar.id_satuan_2
                WHERE barang.deleted_at IS NULL AND barang.id_tipe = 1
                ORDER BY barang.id
            ";
        } elseif ($proses == 'pemotongan_sarung') {
            $sql = "SELECT
                EXTRACT(MONTH FROM tanggal) AS bulan,
                id_barang,
                barang.name nama_barang,
                id_motif,
                motif.alias nama_motif,
                SUM(volume_1) volume
                FROM tbl_tenun_detail AS data
                LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
                LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
                WHERE data.deleted_at IS NULL AND data.code = 'BG'
                GROUP BY data.id_barang, data.id_motif, EXTRACT(MONTH FROM tanggal), barang.name, motif.alias
                ORDER BY EXTRACT(MONTH FROM tanggal), data.id_barang, data.id_motif
            ";
        } else if ($proses == 'inspecting_grey') {
            $sql = "SELECT
                inspect.id_beam,
                nomor_kikw.name nomor_kikw,
                nomor_beam.name nomor_beam,
                inspect.id_mesin,
                mesin.name nama_mesin,
                mesin.tipe nama_jenis_mesin,
                inspect.id_motif,
                motif.alias nama_motif,
                inspect.id_warna,
                warna.alias nama_warna,
                inspect.id_barang,
                barang.name nama_barang,
                inspect.id_group,
                grup.name nama_group,
                SUM(COALESCE(inspect.volume_1, 0)) jumlah,
                SUM(COALESCE(inspect.jml_grade_a, 0)) grade_a,
                SUM(COALESCE(inspect.jml_grade_b, 0)) grade_b,
                SUM(COALESCE(inspect.jml_grade_c, 0)) grade_c,
                SUM(COALESCE(inspect.jml_kualitas_1, 0)) kualitas_1,
                SUM(COALESCE(inspect.jml_kualitas_2, 0)) kualitas_2,
                SUM(COALESCE(inspect.jml_kualitas_3, 0)) kualitas_3,
                SUM(COALESCE(inspect.jml_kualitas_4, 0)) kualitas_4,
                SUM(COALESCE(inspect.jml_kualitas_5, 0)) kualitas_5,
                SUM(COALESCE(inspect.jml_kualitas_6, 0)) kualitas_6,
                SUM(COALESCE(inspect.jml_kualitas_7, 0)) kualitas_7,
                SUM(COALESCE(inspect.jml_kualitas_8, 0)) kualitas_8,
                SUM(COALESCE(inspect.jml_kualitas_9, 0)) kualitas_9,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_10,
                SUM(COALESCE(inspect.jml_kualitas_11, 0)) kualitas_11,
                SUM(COALESCE(inspect.jml_kualitas_13, 0)) kualitas_12,
                SUM(COALESCE(inspect.jml_kualitas_14, 0)) kualitas_13,
                SUM(COALESCE(inspect.jml_kualitas_15, 0)) kualitas_14,
                SUM(COALESCE(inspect.jml_kualitas_16, 0)) kualitas_15,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_16,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_17,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_18,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_19,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_20,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_21,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_22,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_23,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_24,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_25,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_26,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_27,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_28,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_29,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_30,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_31,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_32,
                SUM(COALESCE(inspect.jml_kualitas_10, 0)) kualitas_33
                FROM
                tbl_inspecting_grey_detail AS inspect
                LEFT JOIN tbl_beam AS beam ON beam.id = inspect.id_beam
                LEFT JOIN tbl_nomor_beam AS nomor_beam ON nomor_beam.id = beam.id_nomor_beam
                LEFT JOIN tbl_nomor_kikw AS nomor_kikw ON nomor_kikw.id = beam.id_nomor_kikw
                LEFT JOIN tbl_mesin AS mesin ON mesin.id = inspect.id_mesin
                LEFT JOIN tbl_barang AS barang ON barang.id = inspect.id_barang
                LEFT JOIN tbl_motif AS motif ON motif.id = inspect.id_motif
                LEFT JOIN tbl_warna AS warna ON warna.id = inspect.id_warna
                LEFT JOIN tbl_group AS grup ON grup.id = inspect.id_group
                WHERE inspect.tanggal >= '$tgl_awal' AND inspect.tanggal <= '$tgl_akhir'
                AND inspect.deleted_at IS NULL
                GROUP BY
                inspect.id_beam,
                nomor_beam.name,
                nomor_kikw.name,
                inspect.id_mesin,
                mesin.name,
                mesin.tipe,
                inspect.id_motif,
                motif.alias,
                inspect.id_warna,
                warna.alias,
                inspect.id_barang,
                barang.name,
                inspect.id_group,
                grup.name    
                ORDER BY
                mesin.tipe,
                mesin.name   
            ";
        } else if ($proses == 'persediaan_benang_warna') {
            $sql = "SELECT
                barang.id,
                barang.name nama_barang,
                sa.volume_1::numeric sa_cones,
                ROUND(sa.volume_2::numeric, 2) sa_kg,
                dyeing.volume_1 dyeing_cones,
                ROUND(dyeing.volume_2::numeric, 2) dyeing_kg,
                jsl.volume_1 jsl_cones,
                ROUND(jsl.volume_2::numeric, 2) jsl_kg,
                wh.volume_1 wh_cones,
                ROUND(wh.volume_2::numeric, 2) wh_kg,
                rt.volume_1 rt_cones,
                ROUND(rt.volume_2::numeric, 2) rt_kg,
                warping.volume_1 warping_cones,
                ROUND(warping.volume_2::numeric, 2) warping_kg,
                pakan.volume_1 pakan_cones,
                ROUND(pakan.volume_2::numeric, 2) pakan_kg
                FROM tbl_barang AS barang
                LEFT JOIN
                (
                    SELECT
                    log.id_barang,
                    SUM(COALESCE(volume_masuk_1,0)) - SUM(COALESCE(volume_keluar_1,0)) volume_1,
                    SUM(COALESCE(volume_masuk_2,0)) - SUM(COALESCE(volume_keluar_2,0)) as volume_2
                    FROM log_stok_penerimaan AS log
                    WHERE log.deleted_at IS NULL AND
                    log.code = 'BHD' AND
                    log.tanggal < '$tgl_awal'
                    GROUP BY log.id_barang
                ) AS sa ON sa.id_barang = barang.id
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_pengiriman_barang_detail AS detail
                    LEFT JOIN tbl_pengiriman_barang AS parent ON parent.id = detail.id_pengiriman_barang
                    WHERE
                    detail.deleted_at IS NULL AND
                    parent.id_tipe_pengiriman = 2 AND
                    detail.status = 'TUJUAN' AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang
                ) AS dyeing ON dyeing.id_barang = barang.id
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_dyeing_jasa_luar_detail AS detail
                    WHERE detail.deleted_at IS NULL AND
                    detail.status = 'TERIMA' AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang
                ) AS jsl ON jsl.id_barang = barang.id
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_dyeing_grey_detail AS detail
                    WHERE detail.deleted_at IS NULL AND
                    detail.status = 'TERIMA' AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang
                ) AS wh ON wh.id_barang = barang.id
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    barang.name nama_barang,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_pengiriman_barang_detail AS detail
                    LEFT JOIN tbl_pengiriman_barang AS parent ON parent.id = detail.id_pengiriman_barang
                    LEFT JOIN tbl_barang AS barang ON barang.id = detail.id_barang
                    WHERE detail.deleted_at IS NULL AND
                    detail.status = 'TUJUAN' AND
                    parent.id_tipe_pengiriman IN (4,13,14,17) AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang, barang.name
                ) AS rt ON rt.nama_barang = barang.name
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_warping_detail AS detail
                    WHERE detail.deleted_at IS NULL AND
                    detail.code = 'BBW' AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang
                ) AS warping ON warping.id_barang = barang.id
                LEFT JOIN
                (
                    SELECT
                    barang.name nama_barang,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_distribusi_pakan_detail AS detail
                    LEFT JOIN tbl_barang AS barang ON barang.id = detail.id_barang
                    WHERE detail.deleted_at IS NULL AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY barang.name
                ) AS pakan ON pakan.nama_barang = barang.name
                WHERE barang.deleted_at IS NULL AND
                barang.id_tipe = 1 AND
                barang.owner = 'SOLO'
                ORDER BY
                barang.id
            ";
        } else if ($proses == 'persediaan_benang_warna_per_jenis') {
            $barang = $request->barang_benang ?? "";
            $sql = "SELECT
                barang.id id_barang,
                barang.name nama_barang,
                warna.id id_warna,
                warna.name nama_warna,
                COALESCE(sa.volume_1::numeric,0) sa_cones,
                COALESCE(ROUND(sa.volume_2::numeric, 2),0) sa_kg,
                COALESCE(dyeing.volume_1::numeric,0) dyeing_cones,
                COALESCE(ROUND(dyeing.volume_2::numeric, 2),0) dyeing_kg,
                COALESCE(jsl.volume_1::numeric,0) jsl_cones,
                COALESCE(ROUND(jsl.volume_2::numeric, 2),0) jsl_kg,
                COALESCE(wh.volume_1::numeric,0) wh_cones,
                COALESCE(ROUND(wh.volume_2::numeric, 2),0) wh_kg,
                COALESCE(rt.volume_1::numeric,0) rt_cones,
                COALESCE(ROUND(rt.volume_2::numeric, 2),0) rt_kg,
                COALESCE(warping.volume_1::numeric,0) warping_cones,
                COALESCE(ROUND(warping.volume_2::numeric, 2),0) warping_kg,
                COALESCE(pakan.volume_1::numeric,0) pakan_cones,
                COALESCE(ROUND(pakan.volume_2::numeric, 2),0) pakan_kg
                FROM tbl_barang AS barang
                LEFT JOIN tbl_warna AS warna ON 1=1
                LEFT JOIN
                (
                    SELECT
                    log.id_barang,
                    log.id_warna,
                    SUM(COALESCE(volume_masuk_1,0)) - SUM(COALESCE(volume_keluar_1,0)) volume_1,
                    SUM(COALESCE(volume_masuk_2,0)) - SUM(COALESCE(volume_keluar_2,0)) volume_2
                    FROM log_stok_penerimaan AS log
                    WHERE log.deleted_at IS NULL AND
                    log.code = 'BHD' AND
                    log.tanggal < '$tgl_akhir'
                    GROUP BY log.id_barang, log.id_warna
                    ORDER BY log.id_barang, log.id_warna
                ) AS sa ON sa.id_barang = barang.id AND sa.id_warna = warna.id
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    detail.id_warna,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_pengiriman_barang_detail AS detail
                    LEFT JOIN tbl_pengiriman_barang AS parent ON parent.id = detail.id_pengiriman_barang
                    WHERE
                    detail.deleted_at IS NULL AND
                    parent.id_tipe_pengiriman = 2 AND
                    detail.status = 'TUJUAN' AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang, detail.id_warna
                ) AS dyeing ON dyeing.id_barang = barang.id AND dyeing.id_warna = warna.id
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    detail.id_warna,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_dyeing_jasa_luar_detail AS detail
                    WHERE detail.deleted_at IS NULL AND
                    detail.status = 'TERIMA' AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang, detail.id_warna
                ) AS jsl ON jsl.id_barang = barang.id AND jsl.id_warna = warna.id
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    detail.id_warna,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_dyeing_grey_detail AS detail
                    WHERE detail.deleted_at IS NULL AND
                    detail.status = 'TERIMA' AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang, detail.id_warna
                ) AS wh ON wh.id_barang = barang.id  AND wh.id_warna = warna.id
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    detail.id_warna,
                    barang.name nama_barang,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_pengiriman_barang_detail AS detail
                    LEFT JOIN tbl_pengiriman_barang AS parent ON parent.id = detail.id_pengiriman_barang
                    LEFT JOIN tbl_barang AS barang ON barang.id = detail.id_barang
                    WHERE detail.deleted_at IS NULL AND
                    detail.status = 'TUJUAN' AND
                    parent.id_tipe_pengiriman IN (4,13,14,17) AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang, barang.name, detail.id_warna
                ) AS rt ON rt.nama_barang = barang.name AND rt.id_warna = warna.id
                LEFT JOIN
                (
                    SELECT
                    detail.id_barang,
                    detail.id_warna,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_warping_detail AS detail
                    WHERE detail.deleted_at IS NULL AND
                    detail.code = 'BBW' AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY detail.id_barang, detail.id_warna
                ) AS warping ON warping.id_barang = barang.id AND warping.id_warna = warna.id
                LEFT JOIN
                (
                    SELECT
                    barang.name nama_barang,
                    detail.id_warna,
                    SUM(detail.volume_1) volume_1,
                    SUM(detail.volume_2) volume_2
                    FROM tbl_distribusi_pakan_detail AS detail
                    LEFT JOIN tbl_barang AS barang ON barang.id = detail.id_barang
                    WHERE detail.deleted_at IS NULL AND
                    detail.tanggal >= '$tgl_awal' AND
                    detail.tanggal <= '$tgl_akhir'
                    GROUP BY barang.name, detail.id_warna
                ) AS pakan ON pakan.nama_barang = barang.name AND pakan.id_warna = warna.id
                WHERE warna.jenis = 'SINGLE' AND
                warna.deleted_at IS NULL AND
                barang.deleted_at IS NULL AND
                barang.owner = 'SOLO' AND barang.id = $barang
                ORDER BY warna.alias
            ";
        } else if ($proses == 'cacat_jasa_luar_finishing') {
            $sql = "
                SELECT
                barang.name nama_barang,
                motif.alias nama_motif,
                warna.alias nama_warna,
                data.*
                FROM
                (
                    SELECT
                    COALESCE(p1.id_barang, fc.id_barang, p2.id_barang) AS id_barang,
                    COALESCE(p1.id_motif, fc.id_motif, p2.id_motif) AS id_motif,
                    COALESCE(p1.id_warna, fc.id_warna, p2.id_warna) AS id_warna,
                    p1.volume_1 p1_volume_1,
                    p1.id_supplier p1_id_supplier,
                    p1.supplier p1_supplier,
                    p1.id_grade p1_id_grade,
                    p1.kualitas p1_kualitas,
                    fc.volume_1 fc_volume_1,
                    fc.id_supplier fc_id_supplier,
                    fc.supplier fc_supplier,
                    fc.id_grade fc_id_grade,
                    fc.kualitas fc_kualitas,
                    p2.volume_1 p2_volume_1,
                    p2.id_supplier p2_id_supplier,
                    p2.supplier p2_supplier,
                    p2.id_grade p2_id_grade,
                    p2.kualitas p2_kualitas
                    FROM
                    (
                        SELECT
                        inspect.id_barang,
                        inspect.id_warna,
                        inspect.id_motif,
                        MAX(inspect.volume_1) volume_1,
                        parent.id_supplier,
                        supplier.name supplier,
                        inspect.id_grade,
                        STRING_AGG(DISTINCT kualitas.kode, ', ' ORDER BY kualitas.kode) AS kualitas
                        FROM tbl_inspect_p1_kualitas AS inspect_kualitas
                        LEFT JOIN tbl_inspect_p1_detail AS inspect ON inspect.id = inspect_kualitas.id_inspect_p1_detail
                        LEFT JOIN tbl_p1 AS parent ON parent.id = inspect.id_p1
                        LEFT JOIN tbl_mapping_kualitas AS kualitas ON kualitas.id = inspect_kualitas.id_kualitas
                        LEFT JOIN tbl_supplier AS supplier ON supplier.id = parent.id_supplier
                        WHERE kualitas.deleted_at IS NULL
                        GROUP BY inspect.id_barang, inspect.id_warna, inspect.id_motif, inspect.id_grade, parent.id_supplier, supplier.name
                    ) p1
                    FULL OUTER JOIN
                    (
                        SELECT
                        inspect.id_barang,
                        inspect.id_warna,
                        inspect.id_motif,
                        MAX(inspect.volume_1) volume_1,
                        parent.id_supplier,
                        supplier.name supplier,
                        inspect.id_grade,
                        STRING_AGG(DISTINCT kualitas.kode, ', ' ORDER BY kualitas.kode) AS kualitas
                        FROM tbl_inspect_finishing_cabut_kualitas AS inspect_kualitas
                        LEFT JOIN tbl_inspect_finishing_cabut_detail AS inspect ON inspect.id = inspect_kualitas.id_inspect_finishing_cabut_detail
                        LEFT JOIN tbl_finishing_cabut AS parent ON parent.id = inspect.id_finishing_cabut
                        LEFT JOIN tbl_mapping_kualitas AS kualitas ON kualitas.id = inspect_kualitas.id_kualitas
                        LEFT JOIN tbl_supplier AS supplier ON supplier.id = parent.id_supplier
                        WHERE kualitas.deleted_at IS NULL
                        GROUP BY inspect.id_barang, inspect.id_warna, inspect.id_motif, inspect.id_grade, parent.id_supplier, supplier.name
                    ) fc ON fc.id_barang = p1.id_barang AND fc.id_warna = p1.id_warna AND fc.id_motif = p1.id_motif
                    FULL OUTER JOIN
                    (
                        SELECT
                        inspect.id_barang,
                        inspect.id_warna,
                        inspect.id_motif,
                        MAX(inspect.volume_1) volume_1,
                        parent.id_supplier,
                        supplier.name supplier,
                        inspect.id_grade,
                        STRING_AGG(DISTINCT kualitas.kode, ', ' ORDER BY kualitas.kode) AS kualitas
                        FROM tbl_inspect_p2_kualitas AS inspect_kualitas
                        LEFT JOIN tbl_inspect_p2_detail AS inspect ON inspect.id = inspect_kualitas.id_inspect_p2_detail
                        LEFT JOIN tbl_p2 AS parent ON parent.id = inspect.id_p2
                        LEFT JOIN tbl_mapping_kualitas AS kualitas ON kualitas.id = inspect_kualitas.id_kualitas
                        LEFT JOIN tbl_supplier AS supplier ON supplier.id = parent.id_supplier
                        WHERE kualitas.deleted_at IS NULL
                        GROUP BY inspect.id_barang, inspect.id_warna, inspect.id_motif, inspect.id_grade, parent.id_supplier, supplier.name
                    ) p2 ON p2.id_barang = p1.id_barang AND p2.id_warna = p1.id_warna AND p2.id_motif = p1.id_motif
                ) data
                LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
                LEFT JOIN tbl_motif AS motif ON motif.id = data.id_motif
                LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
                ORDER BY id_barang, id_motif, id_warna
            ";
        } else if ($proses == 'pengiriman_barang') {
            $tipe_pengiriman = $request->tipe_pengiriman ?? null;
            $where = ($tipe_pengiriman) ? "WHERE asal.id_tipe_pengiriman = $tipe_pengiriman" : '';
            $sql = "SELECT
                asal.id_tipe_pengiriman,
                asal.initial,
                asal.nama_pengiriman,
                asal.id_barang id_barang,
                barang.name nama_barang,
                asal.id_motif id_motif,
                motif.alias nama_motif,
                asal.id_gudang asal_id_gudang,
                asal_gudang.name asal_nama_gudang,
                asal.volume_1 asal_volume_1,
                asal.id_satuan_1 asal_id_satuan_1,
                asal_satuan_1.name asal_nama_satuan_1,
                asal.volume_2 asal_volume_2,
                asal.id_satuan_2 asal_id_satuan_2,
                asal_satuan_2.name asal_nama_satuan_2,
                tujuan.id_gudang tujuan_id_gudang,
                tujuan_gudang.name tujuan_nama_gudang,
                tujuan.volume_1 tujuan_volume_1,
                tujuan.id_satuan_1 tujuan_id_satuan_1,
                tujuan_satuan_1.name tujuan_nama_satuan_1,
                tujuan.volume_2 tujuan_volume_2,
                tujuan.id_satuan_2 tujuan_id_satuan_2,
                tujuan_satuan_2.name tujuan_nama_satuan_2
                FROM
                (
                    SELECT
                    tipe.id id_tipe_pengiriman,
                    tipe.initial,
                    tipe.name nama_pengiriman,
                    detail.id_gudang,
                    detail.id_barang,
                    detail.id_motif,
                    SUM(detail.volume_1::numeric) volume_1,
                    detail.id_satuan_1,
                    SUM(detail.volume_2::numeric) volume_2,
                    detail.id_satuan_2,
                    tipe.id_gudang_asal,
                    tipe.id_gudang_tujuan
                    FROM tbl_pengiriman_barang_detail AS detail
                    LEFT JOIN tbl_pengiriman_barang AS parent ON parent.id = detail.id_pengiriman_barang
                    LEFT JOIN tbl_tipe_pengiriman AS tipe ON tipe.id = parent.id_tipe_pengiriman
                    WHERE detail.deleted_at IS NULL AND detail.status = 'ASAL' AND tipe.is_aktif = 'YA' AND detail.tanggal >= '$tgl_awal' AND detail.tanggal <= '$tgl_akhir'
                    GROUP BY tipe.id, tipe.initial, tipe.name, detail.id_gudang, detail.id_barang, detail.id_satuan_1, detail.id_satuan_2, tipe.id_gudang_asal, tipe.id_gudang_tujuan, detail.id_motif
                ) AS asal
                LEFT JOIN
                (
                    SELECT
                    tipe.id id_tipe_pengiriman,
                    tipe.initial,
                    tipe.name nama_pengiriman,
                    detail.id_gudang,
                    detail.id_barang,
                    detail.id_motif,
                    SUM(detail.volume_1::numeric) volume_1,
                    detail.id_satuan_1,
                    SUM(detail.volume_2::numeric) volume_2,
                    detail.id_satuan_2,
                    tipe.id_gudang_asal,
                    tipe.id_gudang_tujuan
                    FROM tbl_pengiriman_barang_detail AS detail
                    LEFT JOIN tbl_pengiriman_barang AS parent ON parent.id = detail.id_pengiriman_barang
                    LEFT JOIN tbl_tipe_pengiriman AS tipe ON tipe.id = parent.id_tipe_pengiriman
                    WHERE detail.deleted_at IS NULL AND detail.status = 'TUJUAN' AND tipe.is_aktif = 'YA' AND detail.tanggal >= '$tgl_awal' AND detail.tanggal <= '$tgl_akhir'
                    GROUP BY tipe.id, tipe.initial, tipe.name, detail.id_gudang, detail.id_barang, detail.id_satuan_1, detail.id_satuan_2, tipe.id_gudang_asal, tipe.id_gudang_tujuan, detail.id_motif
                ) AS tujuan ON tujuan.initial = asal.initial AND tujuan.nama_pengiriman = asal.nama_pengiriman AND tujuan.id_gudang = asal.id_gudang_tujuan AND tujuan.id_barang = asal.id_barang AND tujuan.id_motif = asal.id_motif
                LEFT JOIN tbl_satuan AS asal_satuan_1 ON asal_satuan_1.id = asal.id_satuan_1
                LEFT JOIN tbl_satuan AS asal_satuan_2 ON asal_satuan_2.id = asal.id_satuan_2
                LEFT JOIN tbl_satuan AS tujuan_satuan_1 ON tujuan_satuan_1.id = tujuan.id_satuan_1
                LEFT JOIN tbl_satuan AS tujuan_satuan_2 ON tujuan_satuan_2.id = tujuan.id_satuan_2
                LEFT JOIN tbl_barang AS barang ON barang.id = asal.id_barang
                LEFT JOIN tbl_motif AS motif ON motif.id = asal.id_motif
                LEFT JOIN tbl_gudang AS asal_gudang ON asal_gudang.id = asal.id_gudang
                LEFT JOIN tbl_gudang AS tujuan_gudang ON tujuan_gudang.id = tujuan.id_gudang
                $where
                ORDER BY asal.id_tipe_pengiriman
            ";
        } else if ($proses == 'produksi_dyeing') {
            $mesin = $request->mesin_dyeing ? "AND id_mesin = {$request->mesin_dyeing}" : "";
            $sql = "SELECT
                barang.name nama_barang,
                warna.alias nama_warna,
                data.*
                FROM
                (
                    SELECT
                    data.id_mesin,
                    data.tanggal,
                    data.id_barang,
                    data.id_warna,
                    SUM(COALESCE(data.volume_1,0)) cones,
                    SUM(COALESCE(data.volume_2,0)) kg
                    FROM tbl_dyeing_detail AS data
                    WHERE data.deleted_at IS NULL
                    AND data.status = 'OVERCONE'
                    $mesin
                    GROUP BY data.id_mesin, data.tanggal, data.id_barang, data.id_warna
                    ORDER BY data.tanggal ASC
                ) AS data
                LEFT JOIN tbl_barang AS barang ON barang.id = data.id_barang
                LEFT JOIN tbl_warna AS warna ON warna.id = data.id_warna
                ORDER BY data.tanggal,warna.alias, barang.name";
        }
    }

    return $sql;
}
