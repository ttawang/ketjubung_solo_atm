<?php

namespace App\Exports;

use App\Helpers\Date;
use App\Models\LogStokPenerimaanWithoutAppend;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StokopnameExport implements FromQuery, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{

    use Exportable, RegistersEventListeners;
    private static $id_gudang, $code, $tanggal, $proses, $countData;

    public function __construct($idGudang, $code, $tanggal)
    {
        self::$id_gudang = $idGudang == 'ALL' ? '' : $idGudang;
        self::$code = $code;
        self::$tanggal = $tanggal;
        self::$proses = StokopnameCodeText($code);
        self::$countData = 0;
    }

    public static function afterSheet(AfterSheet $event)
    {
        $workSheet = $event->sheet->getDelegate();
        $arrayColumnCollapsed = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
        foreach ($arrayColumnCollapsed as $key => $value) {
            $workSheet->getColumnDimension($value)->setCollapsed(true);
            $workSheet->getColumnDimension($value)->setVisible(false);
        }

        $keys = ['class1', 'class2', 'class3', 'class4'];
        $stokRow = [
            'class1' => ['R', 'T'],
            'class2' => ['S', 'U', 'V', 'X'],
            'class3' => ['Z', 'AB', 'AC', 'AE'],
            'class4' => ['Y', 'AA']
        ];

        $code = self::$code;
        $endRows = self::$countData + 3;
        foreach ($keys as $key) {
            if (!checkCodeStokopname($code, $key)) continue;
            foreach ($stokRow[$key] as $row) {
                $workSheet->getStyle("{$row}4:{$row}{$endRows}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ]
                ]);
            }
        }
        // $workSheet->protectCells('A3:E3', 'PASSWORD');
        // $workSheet->getStyle('F3:G3')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        // $workSheet->getProtection()->setSheet(true);
    }

    public function query()
    {
        $idGudang = self::$id_gudang;
        $orderBy = "id_barang, id_warna, id_motif, id_beam, id_mesin, id_grade, id_kualitas, id_gudang ASC";
        $select  = 'ROW_NUMBER() OVER (ORDER BY ' . $orderBy . ') as iteration, id_gudang, id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, code, id_beam, id_mesin,
        SUM(COALESCE(volume_masuk_1, 0)::decimal - COALESCE(volume_keluar_1, 0)::decimal) as stok_utama, 
        SUM(COALESCE(volume_masuk_2, 0)::decimal - COALESCE(volume_keluar_2, 0)::decimal) as stok_pilihan';
        $groupBy = 'id_gudang, id_barang, id_satuan_1, id_satuan_2, id_warna, id_motif, id_grade, id_kualitas, code, id_beam, id_mesin';
        $fetch = LogStokPenerimaanWithoutAppend::when($idGudang, function ($query) use ($idGudang) {
            return $query->where('id_gudang', $idGudang);
        })
            ->where('tanggal', '<=', self::$tanggal)
            ->where('code', self::$code)
            ->selectRaw($select)
            ->groupByRaw($groupBy)
            ->orderByRaw($orderBy);
        self::$countData = $fetch->get()->count();
        return $fetch;
    }

    public function map($data): array
    {
        $defaultItem = [self::$tanggal, self::$code, $data->id_gudang, $data->id_barang, $data->id_satuan_1, $data->id_satuan_2, $data->id_warna, $data->id_motif, $data->id_beam, $data->id_mesin, $data->id_grade, $data->id_kualitas, $data->tipe_pra_tenun, $data->is_sizing];
        $mappingItem = function ($code) use ($data, $defaultItem) {
            if (checkCodeStokopname($code, 'class1')) {
                $customItem = [$data->iteration, $data->relGudang()->value('name'), $data->relBarang()->value('name'), floatValue($data->stok_utama, true), $data->relSatuan1()->value('name')];
                return array_merge($defaultItem, $customItem);
            } else if (checkCodeStokopname($code, 'class2')) {
                $customItem = [$data->iteration, $data->relGudang()->value('name'), $data->relBarang()->value('name'), $data->relWarna()->value('name'), floatValue($data->stok_utama, true), $data->relSatuan1()->value('name'), '', floatValue($data->stok_pilihan, true), $data->relSatuan2()->value('name'), ''];
                return array_merge($defaultItem, $customItem);
            } else if (checkCodeStokopname($code, 'class3')) {
                $customItem = [$data->iteration, $data->relGudang()->value('name'), $data->relBarang()->value('name'), $data->relWarna()->value('name'), $data->relMotif()->value('alias'), $data->throughNomorBeam()->value('name'), $data->throughNomorKikw()->value('name'), $data->relMesin()->value('name'), $data->tipe_pra_tenun, $data->is_sizing, floatValue($data->stok_utama, true), $data->relSatuan1()->value('name'), 1, floatValue($data->stok_pilihan, true), $data->relSatuan2()->value('name'), ''];
                return array_merge($defaultItem, $customItem);
            } else if (checkCodeStokopname($code, 'class4')) {
                $customItem = [$data->iteration, $data->relGudang()->value('name'), $data->relBarang()->value('name'), $data->relWarna()->value('name'), $data->relMotif()->value('alias'), $data->throughNomorBeam()->value('name'), $data->throughNomorKikw()->value('name'), $data->relMesin()->value('name'), $data->relGrade()->value('grade'), $data->relKualitas()->value('kode'), floatValue($data->stok_utama, true), $data->relSatuan1()->value('name')];
                return array_merge($defaultItem, $customItem);
            }
        };

        return $mappingItem(self::$code);
    }

    public function headings(): array
    {
        $defaultHeader = ["tanggal", "code", "id_gudang", "id_barang", "id_satuan_1", "id_satuan_2", "id_warna", "id_motif", "id_beam", "id_mesin", "id_grade", "id_kualitas", "tipe_pra_tenun", "is_sizing"];
        $mappingHeader = function ($code) use ($defaultHeader) {
            if (checkCodeStokopname($code, 'class1')) {
                $customHeader = ["NO", "GUDANG", "NAMA BARANG", "STOK", "SATUAN", "STOKOPNAME", "CATATAN"];
                return array_merge($defaultHeader, $customHeader);
            } else if (checkCodeStokopname($code, 'class2')) {
                $customHeader = ["NO", "GUDANG", "NAMA BARANG", "WARNA", "STOK_1", "SATUAN_1", "STOKOPNAME_1", "STOK_2", "SATUAN_2", "STOKOPNAME_2", "CATATAN"];
                return array_merge($defaultHeader, $customHeader);
            } else if (checkCodeStokopname($code, 'class3')) {
                $customHeader = ["NO", "GUDANG", "NAMA BARANG", "WARNA", "MOTIF", "NO BEAM", "NO KIKW", "MESIN (LOOM)", "TIPE PRA TENUN", "SIZING", "STOK_1", "SATUAN_1", "STOKOPNAME_1", "STOK_2", "SATUAN_2", "STOKOPNAME_2", "CATATAN"];
                return array_merge($defaultHeader, $customHeader);
            } else if (checkCodeStokopname($code, 'class4')) {
                $customHeader = ["NO", "GUDANG", "NAMA BARANG", "WARNA", "MOTIF", "NO BEAM", "NO KIKW", "MESIN (LOOM)", "KUALITAS", "JENIS CACAT", "STOK", "SATUAN", "STOKOPNAME", "CATATAN"];
                return array_merge($defaultHeader, $customHeader);
            }
        };

        return [["HEADER", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "Tanggal : ", Date::format(self::$tanggal, 105), '', "Proses : ", self::$proses], [], $mappingHeader(self::$code)];
    }
}
