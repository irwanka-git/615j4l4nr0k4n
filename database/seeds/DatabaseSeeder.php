<?php

use Illuminate\Database\Seeder;

use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;

use App\Models\StatusPerbaikan;
use App\Models\KlasifikasiJalan;
use App\Models\TingkatKerusakan;
use App\Models\TitikKerusakan;
use App\Models\Jalan;
use App\User;
use App\Gambar;
use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\UserRole;
use App\Library\Format;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init_user_role_menu();
        $this->init_referensi_kabupaten();
        $this->init_referensi_kecamatan();
        $this->init_referensi_desa();
        $this->init_tingkat_kerusakan();
        $this->init_klasifikasi_jalan();
        $this->init_master_jalan();
        $this->init_dummy_titik_sebaran();
        $this->init_ref_gambar();
    }

    public function init_user_role_menu(){
        //USER ADMIN
        $record = new User();
        $record->name = 'Administrator';
        $record->email = 'admin@gisjalan.com';
        $record->phone = '0853-1234-6783';
        $record->password = Hash::make('123456');
        $record->uuid = Format::generate_uuid();
        $record->save();
        $id_user_admin = $record->id;

        $record = new User();
        $record->name = 'Operator 1';
        $record->email = 'operator1@gisjalan.com';
        $record->phone = '0812-1234-9990';
        $record->password = Hash::make('123456');
        $record->uuid = Format::generate_uuid();
        $record->save();
        $id_user_operator = $record->id;

        //ROLE ADMIN
        $record = new Role();
        $record->nama_role = 'Administrator';
        $record->uuid = Format::generate_uuid();
        $record->save();
        $id_role_admin = $record->id;

        //ROLE OPERATOR
        $record = new Role();
        $record->nama_role = 'Operator';
        $record->uuid = Format::generate_uuid();
        $record->save();
        $id_role_operator = $record->id;

        //USER -> ADMIN
        $record = new UserRole();
        $record->id_user = $id_user_admin;
        $record->id_role = $id_role_admin;
        $record->uuid = Format::generate_uuid();
        $record->save();
        
        //USER -> Operator
        $record = new UserRole();
        $record->id_user = $id_user_operator;
        $record->id_role = $id_role_operator;
        $record->uuid = Format::generate_uuid();
        $record->save();


        //MENU
        $record = new Menu();
        $record->nama_menu = "Pengaturan Menu";
        $record->urutan = 1;
        $record->url = "setting-menu";
        $record->uuid = Format::generate_uuid();
        $record->save();
        $idm_setting_menu = $record->id;

        $record = new Menu();
        $record->nama_menu = "Pengaturan Pengguna";
        $record->urutan = 2;
        $record->url = "setting-role";
        $record->uuid = Format::generate_uuid();
        $record->save();
        $idm_setting_role = $record->id;
 

        $record = new RoleMenu();
        $record->id_role = $id_role_admin;
        $record->id_menu = $idm_setting_menu;
        $record->ucc = 1;
        $record->ucu = 1;
        $record->ucd = 1;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new RoleMenu();
        $record->id_role = $id_role_admin;
        $record->id_menu = $idm_setting_role;
        $record->ucc = 1;
        $record->ucu = 1;
        $record->ucd = 1;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new Menu();
        $record->nama_menu = "Referensi Kecamatan";
        $record->urutan = 3;
        $record->url = "ref-kecamatan";
        $record->uuid = Format::generate_uuid();
        $record->save();
        $idm_ref_kecamatan = $record->id;

        $record = new RoleMenu();
        $record->id_role = $id_role_admin;
        $record->id_menu = $idm_ref_kecamatan;
        $record->ucc = 1;
        $record->ucu = 1;
        $record->ucd = 1;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new RoleMenu();
        $record->id_role = $id_role_operator;
        $record->id_menu = $idm_ref_kecamatan;
        $record->ucc = 0;
        $record->ucu = 0;
        $record->ucd = 0;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new Menu();
        $record->nama_menu = "Referensi Desa";
        $record->urutan = 4;
        $record->url = "ref-desa";
        $record->uuid = Format::generate_uuid();
        $record->save();
        $idm_ref_desa = $record->id;

        $record = new RoleMenu();
        $record->id_role = $id_role_admin;
        $record->id_menu = $idm_ref_desa;
        $record->ucc = 1;
        $record->ucu = 1;
        $record->ucd = 1;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new RoleMenu();
        $record->id_role = $id_role_operator;
        $record->id_menu = $idm_ref_desa;
        $record->ucc = 0;
        $record->ucu = 0;
        $record->ucd = 0;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new Menu();
        $record->nama_menu = "Master Jalan";
        $record->urutan = 5;
        $record->url = "master-jalan";
        $record->uuid = Format::generate_uuid();
        $record->save();
        $idm_master_jalan = $record->id;

        $record = new RoleMenu();
        $record->id_role = $id_role_admin;
        $record->id_menu = $idm_master_jalan;
        $record->ucc = 1;
        $record->ucu = 1;
        $record->ucd = 1;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new RoleMenu();
        $record->id_role = $id_role_operator;
        $record->id_menu = $idm_master_jalan;
        $record->ucc = 0;
        $record->ucu = 0;
        $record->ucd = 0;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new Menu();
        $record->nama_menu = "Peta Sebaran";
        $record->urutan = 6;
        $record->url = "sebaran-kerusakan";
        $record->uuid = Format::generate_uuid();
        $record->save();
        $idm_sebaran_rusak = $record->id;

        $record = new RoleMenu();
        $record->id_role = $id_role_admin;
        $record->id_menu = $idm_sebaran_rusak;
        $record->ucc = 1;
        $record->ucu = 1;
        $record->ucd = 1;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new RoleMenu();
        $record->id_role = $id_role_operator;
        $record->id_menu = $idm_sebaran_rusak;
        $record->ucc = 0;
        $record->ucu = 0;
        $record->ucd = 0;
        $record->uuid = Format::generate_uuid();
        $record->save();


        $record = new Menu();
        $record->nama_menu = "Entri Titik Kerusakan";
        $record->urutan = 7;
        $record->url = "titik-kerusakan";
        $record->uuid = Format::generate_uuid();
        $record->save();
        $idm_titik_kerusakan = $record->id;
        
        $record = new RoleMenu();
        $record->id_role = $id_role_admin;
        $record->id_menu = $idm_titik_kerusakan;
        $record->ucc = 1;
        $record->ucu = 1;
        $record->ucd = 1;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new RoleMenu();
        $record->id_role = $id_role_operator;
        $record->id_menu = $idm_titik_kerusakan;
        $record->ucc = 1;
        $record->ucu = 1;
        $record->ucd = 1;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new Menu();
        $record->nama_menu = "Rekapitulasi";
        $record->urutan = 8;
        $record->url = "rekapitulasi";
        $record->uuid = Format::generate_uuid();
        $record->save();
        $idm_rekapitulasi = $record->id;

        $record = new RoleMenu();
        $record->id_role = $id_role_admin;
        $record->id_menu = $idm_rekapitulasi;
        $record->ucc = 1;
        $record->ucu = 1;
        $record->ucd = 1;
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new RoleMenu();
        $record->id_role = $id_role_operator;
        $record->id_menu = $idm_rekapitulasi;
        $record->ucc = 0;
        $record->ucu = 0;
        $record->ucd = 0;
        $record->uuid = Format::generate_uuid();
        $record->save();

    }
    
    public function init_referensi_kabupaten(){
          $file_csv = storage_path('data/kabupaten.csv');
          $ref_kabupaten = array();
          $rows = array_map(function($v){return str_getcsv($v, ";");}, file($file_csv));
          foreach($rows as $row){
                $record = new Kabupaten();
                $record->kode_kabupaten = $row[0];
                $record->nama_kabupaten = ucwords(strtolower($row[1]));
                $record->uuid = Format::generate_uuid();
                $record->save();
          }
    }

    public function init_referensi_kecamatan(){
          $file_csv = storage_path('data/kecamatan.csv');
          $ref_kabupaten = array();
          $rows = array_map(function($v){return str_getcsv($v, ";");}, file($file_csv));
          foreach($rows as $row){
                $record = new Kecamatan();
                $record->kode_kecamatan = $row[0];
                $record->kode_kabupaten = $row[1];
                $record->nama_kecamatan = ucwords(strtolower($row[2]));
                $record->uuid = Format::generate_uuid();
                $record->save();
          }
    }

    public function init_referensi_desa(){
          $file_csv = storage_path('data/desa.csv');
          $ref_kabupaten = array();
          $rows = array_map(function($v){return str_getcsv($v, ";");}, file($file_csv));
          foreach($rows as $row){
                $record = new Desa();
                $record->kode_desa = $row[0];
                $record->kode_kecamatan = $row[1];
                $record->nama_desa = ucwords(strtolower($row[2]));
                $record->uuid = Format::generate_uuid();
                $record->save();
          }
    }


    public function init_tingkat_kerusakan()
    {
            $file_csv = storage_path('data/tingkat_kerusakan.csv');
            $rows = array_map(function($v){return str_getcsv($v, ";");}, file($file_csv));
            foreach($rows as $row){
                  $record = new TingkatKerusakan();
                  $record->kode_rusak = $row[0];
                  $record->nama_kerusakan = $row[1];
                  $record->warna = $row[2];
                  $record->warna_stroke = $row[3];
                  $record->uuid = Format::generate_uuid();
                  $record->save();
            }
    }

    public function init_klasifikasi_jalan()
    {
        $record = new KlasifikasiJalan();
        $record->kode_klasifikasi = 'N';
        $record->nama_klasifikasi = 'Jalan Nasional';
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new KlasifikasiJalan();
        $record->kode_klasifikasi = 'P';
        $record->nama_klasifikasi = 'Jalan Provinsi';
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new KlasifikasiJalan();
        $record->kode_klasifikasi = 'K';
        $record->nama_klasifikasi = 'Jalan Kabupaten';
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new KlasifikasiJalan();
        $record->kode_klasifikasi = 'T';
        $record->nama_klasifikasi = 'Jalan Kota';
        $record->uuid = Format::generate_uuid();
        $record->save();

        $record = new KlasifikasiJalan();
        $record->kode_klasifikasi = 'D';
        $record->nama_klasifikasi = 'Jalan Desa';
        $record->uuid = Format::generate_uuid();
        $record->save();

    }

    public function init_master_jalan()
    {
          $file_csv = storage_path('data/jalan.csv');
          $rows = array_map(function($v){return str_getcsv($v, ";");}, file($file_csv));
          foreach($rows as $row){
                $record = new Jalan();
                $record->kode_jalan = $row[0];
                $record->id_klasifikasi = $row[1];
                $record->nama_ruas_jalan = $row[2];
                $record->uuid = Format::generate_uuid();
                $record->save();
          }
    }

    public function init_dummy_titik_sebaran(){
          $file_csv = storage_path('data/titik_kerusakan.csv');
          $rows = array_map(function($v){return str_getcsv($v, ";");}, file($file_csv));
          foreach($rows as $row){
                $record = new TitikKerusakan();
                $record->kode = $row[0];
                $record->id_jalan = $row[1];
                $record->id_tingkat_kerusakan = $row[2];
                $record->latitude = $row[3];
                $record->longitude = $row[4];
                $record->id_kabupaten = $row[5];
                $record->id_kecamatan = $row[6];
                $record->id_desa = $row[7];
                $record->tahun = $row[8];
                $record->id_gambar = $row[9];
                $record->geo_location = $row[10];
                $record->uuid = Format::generate_uuid();
                $record->save();
          }
    }

    public function init_ref_gambar()
    {
          $file_csv = storage_path('data/gambar.csv');
          $rows = array_map(function($v){return str_getcsv($v, ",");}, file($file_csv));
          foreach($rows as $row){
                $record = new Gambar();
                $record->image = $row[0];
                $record->width = $row[1];
                $record->height = $row[2];
                $record->extension = $row[3];
                $record->save();
          }
    }
 

}
