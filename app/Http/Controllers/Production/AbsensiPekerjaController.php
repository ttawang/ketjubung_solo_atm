<?php

namespace App\Http\Controllers\Production;

use App\Helpers\Date;
use App\Helpers\Define;
use App\Http\Controllers\Controller;
use App\Models\AbsensiPekerja;
use App\Models\AbsensiShift;
use App\Models\MappingPekerjaMesin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsensiPekerjaController extends Controller
{
    private static $model = 'AbsensiPekerja';
    // private static $modelDetail = '';

    function __construct()
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $breadcumbs = [['nama' => 'Weaving', 'link' => 'javascript:void(0)', 'active' => ''], ['nama' => 'Absensi Pekerja', 'link' => 'javascript:void(0)', 'active' => 'active']];
        $menuAssets = menuAssets('weaving', 'absensi pekerja', $breadcumbs, true, true, false, true);
        $checkRangeDateButton = AbsensiPekerja::where('tanggal', date('Y-m-d'))->count() > 0 ? 'disabled' : '';
        if (!$request->ajax()) return view('contents.production.weaving.absensi_pekerja.index', compact('menuAssets', 'checkRangeDateButton'));
        $input['name'] = self::$model;
        $input['usedAction'] = 'NOUSED';
        $search = strtolower($request['search']['value']);
        $filterMesin = $request['id_mesin'] ? implode(',', $request['id_mesin']) : '';
        $constructor = AbsensiPekerja::select(
            'tanggal',
            'id_pekerja',
            'id_group',
            DB::raw("string_agg(id::varchar, ',') as arr_id"),
            DB::raw("string_agg(id_mesin::varchar, ',') as arr_mesin"),
            'shift',
            'lembur'
        )
            ->when($search, function ($query, $value) {
                $query->whereHas('relPekerja', function ($query) use ($value) {
                    return $query
                        ->whereRaw("LOWER(name) LIKE '%$value%'")
                        ->orwhereRaw("LOWER(no_register) LIKE '%$value%'")
                        ->orwhereRaw("LOWER(no_hp) LIKE '%$value%'");
                });
            })->when($request['tanggal'], function ($query, $value) {
                return $query->where('tanggal', $value);
            }, function ($query) {
                return $query->whereBetween('tanggal', [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')]);
            })->when($request['id_group'], function ($query, $value) {
                return $query->where('id_group', $value);
            })->when($request['shift'], function ($query, $value) {
                return $query->where('shift', $value);
            })
            ->groupBy('tanggal', 'id_pekerja', 'id_group', 'shift', 'lembur')
            ->when($filterMesin, function ($query, $value) {
                return $query->havingRaw("array_agg( id_mesin )::INTEGER[] && ARRAY[$value]");
            })
            ->orderBy('tanggal', 'ASC');
        $attributes = ['customTanggal', 'relPekerja', 'relAbsensiMesin', 'relGroup', 'relNoRegisterPekerja'];
        return Define::fetch($input, $constructor, $attributes);
    }

    public function create(Request $request)
    {
        $data = collect([]);
        $absensiShift = AbsensiShift::pluck('shift');
        $response['checkRangeDateButton'] = AbsensiPekerja::where('tanggal', date('Y-m-d'))->count() > 0;
        $response['render'] = view('contents.production.weaving.absensi_pekerja.form', compact('data', 'absensiShift'))->render();
        return $response;
    }

    public function edit($id, Request $request)
    {
        $filter = $request['filter'];
        $data = AbsensiPekerja::select(
            DB::raw("string_agg(id::varchar, ',') as arr_id"),
            'tanggal',
            'id_pekerja',
            'id_group',
            DB::raw("string_agg(id_mesin::varchar, ',') as arr_mesin"),
            'shift',
            'lembur'
        )
            ->where($filter)
            ->groupBy('tanggal', 'id_pekerja', 'id_group', 'shift', 'lembur')
            ->orderBy('tanggal', 'ASC')->first();

        $response['selected'] = [
            'select_pekerja' => [
                'id'          => $data->id_pekerja,
                'text'        => $data->relPekerja()->value('name'),
                'no_register' => $data->relPekerja()->value('no_register'),
                'id_group'    => $data->id_group,
                'nama_group'  => $data->relGroup()->value('name')
            ],
            'select_group' => [
                'id' => $data->id_group,
                'text' => $data->relGroup()->value('name')
            ]
        ];

        $selectedMesin = [];
        collect($data->relAbsensiMesin())->each(function ($item, $key) use (&$selectedMesin) {
            $selectedMesin['select_mesin'][] = [
                'id' => $key,
                'text' => $item
            ];
        });

        $id = $data->id_pekerja;
        $response['selected'] = array_merge($response['selected'], $selectedMesin);
        $response['render']   = view('contents.production.weaving.absensi_pekerja.form-edit', compact('data', 'id'))->render();
        return $response;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $range = $request['range'];
        try {
            $idUser = Auth::id();
            $expRange = explode(' - ', $range);
            $inputShift = $request['inputShift'];
            $shiftManual = $request['shift'];
            $shiftAuto = AbsensiShift::pluck('shift', 'id_group')->toArray();

            if ($inputShift == 'manual') {
                foreach ($shiftManual as $idGroup => $currShift) {
                    AbsensiShift::where('id_group', $idGroup)->update(['shift' => changeShift($currShift)]);
                }
            } else {
                AbsensiShift::orderBy('id')->each(function ($item, $key) {
                    AbsensiShift::where('id_group', $item->id_group)->update(['shift' => changeShift($item->shift)]);
                });
            }

            $firstDate = $expRange[0];
            $endDate = $expRange[1];
            $rangeDates = Date::rangeBetweenDates($firstDate, $endDate, 'D', true);
            $dataPekerjaMesin = MappingPekerjaMesin::whereHas('relMesin', function ($query) {
                return $query->where('jenis', 'LOOM');
            })->orderBy('id')->get()->mapWithKeys(function ($item, $key) use ($inputShift, $shiftManual, $shiftAuto, $idUser) {
                $idGroup = $item->relPekerja()->value('id_group');
                $data[$key] = [
                    'id_pekerja' => $item->id_pekerja,
                    'id_group'   => $idGroup,
                    'id_mesin'   => $item->id_mesin,
                    'shift'      => $inputShift == 'auto' ? $shiftAuto[$idGroup] : $shiftManual[$idGroup],
                    'created_by' => $idUser,
                    'created_at' => now()
                ];
                return $data;
            })->toArray();

            $data = [];
            foreach ($rangeDates as $key => $date) {
                $data = array_merge($data, data_fill($dataPekerjaMesin, '*.tanggal', $date->format('Y-m-d')));
                $dataPekerjaMesin = unsetMultiKeys(['tanggal'], $dataPekerjaMesin, true);
            }

            AbsensiPekerja::insert($data);
            activity()->log("Generate Absensi Pekerja ({$range})");
            DB::commit();
            return response('Data Successfully Saved!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage() . ' ' . $th->getLine(), 401);
        }
    }

    public function update($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request['input'];
            AbsensiPekerja::whereIn('id', explode(',', $request['arr_id']))->forceDelete();

            $data = [];
            $idUser = Auth::id();
            foreach ($input['id_mesin'] as $key => $idMesin) {
                $data[] = [
                    'tanggal'    => $input['tanggal'],
                    'id_pekerja' => $input['id_pekerja'],
                    'id_group'   => $input['id_group'],
                    'id_mesin'   => $idMesin,
                    'shift'      => $input['shift'],
                    'lembur'     => $input['lembur'],
                    'created_by' => $idUser,
                    'updated_by' => $idUser,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            AbsensiPekerja::insert($data);
            activity()->log("Mengubah Absensi Pekerja");
            DB::commit();
            return response('Data Successfully Updated!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }

    public function destroy($id, Request $request)
    {
        DB::beginTransaction();
        try {
            AbsensiPekerja::whereIn('id', explode(',', $request['arrId']))->forceDelete();
            activity()->log("Menghapus Absensi Pekerja Lembur");
            DB::commit();
            return response('Data Successfully Deleted!', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th->getMessage(), 401);
        }
    }
}
