<?php

Route::get('/', function () {
	$pagetitle = "";
    return view('welcome', compact('pagetitle'));
});

Route::get('/debug', function () {
    return date('ym');
});

Route::get('/passwd', function () {
    return Hash::make('123456');
});

Route::get('/login','LoginController@page_login');
Route::post('/submit-login','LoginController@submit_login');
Route::get('/logout','LoginController@logout');
Route::get('/ganti-password','LoginController@ganti_password');
Route::post('/update-password','LoginController@submit_update_password');
Route::get('/get-data-map-default', 'SebaranTitikKerusakanController@get_data_map_default');
Route::get('/get-data-map-default-laporan', 'LaporanTitikKerusakanController@get_data_map');

//titik-kerusakan
Route::group(['prefix'=>'titik-kerusakan-public'], function(){
	Route::get('/', 'SebaranTitikKerusakanController@public_index');
	Route::get('/get-data-map-default', 'SebaranTitikKerusakanController@get_data_map_default');
	Route::get('/get-data-map-search/{id_kecamatan}/{tahun}/{id_tingkat_kerusakan}', 
			'SebaranTitikKerusakanController@get_data_map_search');
	Route::get('/get-info-window/{uuid}', 'SebaranTitikKerusakanController@get_info_windows');
});

Route::group(['prefix'=>'laporan-kerusakan-public'], function(){
	Route::get('/', 'LaporanTitikKerusakanController@public_index');
	Route::get('/get-data-map-default', 'LaporanTitikKerusakanController@get_data_map');
	Route::post('/upload-gambar', 'DataTitikKerusakanController@upload_gambar');
	Route::post('/insert', 'LaporanTitikKerusakanController@insert_laporan_warga');
	Route::get('/get-info-window/{uuid}', 'LaporanTitikKerusakanController@get_info_windows_public');
});


Route::get('/uuid', function () {
	list($usec, $sec) = explode(" ", microtime());
    $time = ((float)$usec + (float)$sec);
    $time = str_replace(".", "-", $time);
    $panjang = strlen($time);
    $sisa = substr($time, -1*($panjang-5));
    return Uuid::generate(3,rand(10,99).rand(0,9).substr($time, 0,5).'-'.rand(0,9).rand(0,9)."-".$sisa,Uuid::NS_DNS);
});

Route::post('/update-password','LoginController@submit_update_password');

Route::group(["middleware"=>['auth.login','auth.menu']], function(){
	Route::get('/dashboard', 'HomeController@index');
	
	//setting-menu
	Route::group(['prefix'=>'setting-menu'], function(){
		Route::get('/', 'SettingMenuController@index');
		Route::get('/dt', 'SettingMenuController@datatable');
		Route::get('/get-data/{uuid}', 'SettingMenuController@get_data');
		Route::post('/insert', 'SettingMenuController@submit_insert');
		Route::post('/update', 'SettingMenuController@submit_update');
		Route::post('/delete', 'SettingMenuController@submit_delete');
	});

	//setting-role
	Route::group(['prefix'=>'setting-role'], function(){
		Route::get('/', 'SettingRoleController@index');
		Route::get('/dt-role', 'SettingRoleController@datatable_role');
		Route::get('/get-role/{uuid}', 'SettingRoleController@get_data_role');
		Route::post('/insert-role', 'SettingRoleController@submit_insert_role');
		Route::post('/update-role', 'SettingRoleController@submit_update_role');
		Route::post('/delete-role', 'SettingRoleController@submit_delete_role');

		Route::get('/menu/{uuid}', 'SettingRoleMenuController@index');//tampilkan menu by role
		Route::get('/dt-menu/{uuid}', 'SettingRoleMenuController@datatable');//menu per role
		Route::get('/get-menu/{uuid}', 'SettingRoleMenuController@get_data');
		Route::post('/insert-menu', 'SettingRoleMenuController@submit_insert');
		Route::post('/update-menu', 'SettingRoleMenuController@submit_update');
		Route::post('/delete-menu', 'SettingRoleMenuController@submit_delete');

		Route::get('/user/{uuid}', 'SettingRoleUserController@index');//tampilkan menu by role
		Route::get('/get-user/{uuid}', 'SettingRoleUserController@get_data');//tampilkan menu by role
		Route::get('/dt-user/{uuid}', 'SettingRoleUserController@datatable');//menu per role
		Route::post('/insert-user', 'SettingRoleUserController@submit_insert');
		Route::post('/update-user', 'SettingRoleUserController@submit_update');
		Route::post('/delete-user', 'SettingRoleUserController@submit_delete');
	});

	//referensi-kecamatan
	Route::group(['prefix'=>'ref-kecamatan'], function(){
		Route::get('/', 'ReferensiKecamatanController@index');
		Route::get('/dt', 'ReferensiKecamatanController@datatable');
		Route::get('/get-data/{uuid}', 'ReferensiKecamatanController@get_data');
		Route::post('/insert', 'ReferensiKecamatanController@submit_insert');
		Route::post('/update', 'ReferensiKecamatanController@submit_update');
		Route::post('/delete', 'ReferensiKecamatanController@submit_delete');
	});


	//referensi-desa
	Route::group(['prefix'=>'ref-desa'], function(){
		Route::get('/', 'ReferensiDesaController@index');
		Route::get('/dt', 'ReferensiDesaController@datatable');
		Route::get('/get-data/{uuid}', 'ReferensiDesaController@get_data');
		Route::post('/insert', 'ReferensiDesaController@submit_insert');
		Route::post('/update', 'ReferensiDesaController@submit_update');
		Route::post('/delete', 'ReferensiDesaController@submit_delete');
	});


	//master-jalan
	Route::group(['prefix'=>'master-jalan'], function(){
		Route::get('/', 'MasterJalanController@index');
		Route::get('/dt', 'MasterJalanController@datatable');
		Route::get('/get-data/{uuid}', 'MasterJalanController@get_data');
		Route::get('/generate-kode-jalan/{id_klasifikasi}', 'MasterJalanController@generate_kode_jalan');
		Route::post('/insert', 'MasterJalanController@submit_insert');
		Route::post('/update', 'MasterJalanController@submit_update');
		Route::post('/delete', 'MasterJalanController@submit_delete');
	});

	//titik-kerusakan
	Route::group(['prefix'=>'titik-kerusakan'], function(){
		Route::get('/', 'DataTitikKerusakanController@index');
		Route::get('/dt', 'DataTitikKerusakanController@datatable');
		Route::get('/get-list-desa/{id}', 'DataTitikKerusakanController@generate_list_desa');
		Route::get('/get-data/{uuid}', 'DataTitikKerusakanController@get_data');
		Route::get('/get-data-detil/{uuid}', 'DataTitikKerusakanController@get_data_detil');
		Route::post('/insert', 'DataTitikKerusakanController@submit_insert');
		Route::post('/update', 'DataTitikKerusakanController@submit_update');
		Route::post('/delete', 'DataTitikKerusakanController@submit_delete');
		Route::post('/upload-gambar', 'DataTitikKerusakanController@upload_gambar');
	});

	//sebaran-kerusakan
	Route::group(['prefix'=>'sebaran-kerusakan'], function(){
		Route::get('/', 'SebaranTitikKerusakanController@index');
		Route::get('/get-data-map-default', 'SebaranTitikKerusakanController@get_data_map_default');
		Route::get('/get-data-map-search/{id_kecamatan}/{tahun}/{id_tingkat_kerusakan}', 
				'SebaranTitikKerusakanController@get_data_map_search');
		Route::get('/get-info-window/{uuid}', 'SebaranTitikKerusakanController@get_info_windows');
	});

	//sebaran-kerusakan
	Route::group(['prefix'=>'laporan-titik-kerusakan'], function(){
		Route::get('/', 'LaporanTitikKerusakanController@index_admin');
		Route::get('/dt', 'LaporanTitikKerusakanController@datatable');
		Route::get('/get-data-detil/{uuid}', 'LaporanTitikKerusakanController@get_data_detil');
		Route::get('/get-data-map-default', 'SebaranTitikKerusakanController@get_data_map_default');
		Route::get('/get-info-window/{uuid}', 'LaporanTitikKerusakanController@get_info_windows');
		Route::post('submit-verifikasi', 'LaporanTitikKerusakanController@submit_verifikasi');
		Route::post('submit-tolak', 'LaporanTitikKerusakanController@submit_tolak');
	});

	//rekap-kecamatan
	Route::group(['prefix'=>'rekap-kecamatan'], function(){
		Route::get('/', 'RekapKecamatanController@index');
		Route::get('/dt', 'RekapKecamatanController@datatable');
		Route::get('/map/{id_kecamatan}', 'RekapKecamatanController@peta_kecamatan');
	});
	
});

