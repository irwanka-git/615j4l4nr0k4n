<?php

namespace App\Http\Controllers;

use App\Library\Access;
use App\Library\Format;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use Image;
use DB;
use Session;
use Datatables;
use Crypt;
use Auth;

use App\Models\Jalan;
use App\Models\TingkatKerusakan;
use App\Models\TitikKerusakan;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Gambar;

class RekapKecamatanController extends Controller
{
    //

    function index(){

    	$pagetitle = "Rekapitulasi Titik Kerusakan";
        $smalltitle = "Berdasarkan Kecamatan";
        $data =  DB::select("SELECT
								a.id,
								a.kode_kecamatan,
								a.nama_kecamatan ,
								b.rusak_berat, 
								b.rusak_ringan, 
								b.rusak_sedang
							FROM
								kecamatan AS a
								LEFT JOIN (
								SELECT
									id_kecamatan,
									sum( CASE WHEN id_tingkat_kerusakan = 1 THEN 1 ELSE 0 END ) AS rusak_ringan,
									sum( CASE WHEN id_tingkat_kerusakan = 2 THEN 1 ELSE 0 END ) AS rusak_sedang,
									sum( CASE WHEN id_tingkat_kerusakan = 3 THEN 1 ELSE 0 END ) AS rusak_berat 
								FROM
									`titik_kerusakan` 
								GROUP BY
								id_kecamatan 
								) AS b ON a.id = b.id_kecamatan");

        return view('data.rekap-kecamatan', compact('pagetitle','smalltitle', 'data'));
    }

    function datatable(Request $r){
         
    }
    
}
