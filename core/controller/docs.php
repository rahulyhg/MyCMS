<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'docs');

$content_title = 'Dokumenty serwisu';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Docs_Model($db);

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Docs_View($db);

$status = new Status($db);
$user_id = $status->get_value('user_id');

$list_columns = array(
	array('db_name' => 'id', 					'column_name' => 'Id', 				'sorting' => 1),
	array('db_name' => 'section_id',			'column_name' => 'Sekcja',			'sorting' => 1),
	array('db_name' => 'owner_id',				'column_name' => 'Autor',			'sorting' => 1),
	array('db_name' => 'file_format', 			'column_name' => 'Format',			'sorting' => 1),
	array('db_name' => 'file_name', 			'column_name' => 'Nazwa', 			'sorting' => 1),
	array('db_name' => 'file_size', 			'column_name' => 'Rozmiar', 		'sorting' => 1),
	array('db_name' => 'doc_description',		'column_name' => 'Opis', 			'sorting' => 1),
	array('db_name' => 'active', 				'column_name' => 'Aktywny', 		'sorting' => 1),
	array('db_name' => 'modified', 				'column_name' => 'Modyfikacja', 	'sorting' => 1),
);

if (isset($_GET['mode'])) $_SESSION['mode'] = intval($_GET['mode']);

include 'main/navi.php';

$navi_object = new Navi($db);

$navi_params = $navi_object->init($list_columns);

$record_object = $navi_params['record_object'];
$db_params = $navi_params['db_params'];
$list_params = $navi_params['list_params'];

$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;

// dane z bazy potrzebne do kontrolek formularza:

$data_import = array(
	'owner' => $user_id,
);

// komplet danych przekazywanych do głównego operatora:

$objects = array(
	'model_object' => $model_object,
	'view_object' => $view_object,
	'record_object' => $record_object,
	'navi_object' => $navi_object,
	'db_params' => $db_params,
	'list_params' => $list_params,
	'list_columns' => $list_columns,
	'data_import' => $data_import,
);

include APP_DIR . 'controller/main/operator.php';

$controller_object = new Operator($objects);

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, $id);

if (isset($_GET['action'])) // add, view, edit, delete, download, preview
{
	switch ($_GET['action'])
	{
		// dodawanie:
		
		case 'add':
		{
			$content_options = $page_options->get_options('add');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'user_file', 
					'doc_description', 
				),
				'check' => array(
					'doc_description', 
				),
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->Add($params, $access, $acl->available());
		}
		break;

		// dodawanie wielu na raz:
		
		case 'add-multi':
		{
			$content_options = $page_options->get_options('add');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'upload_files',
				),
				'check' => array(
				),
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);
			
			$controller_object->AddMulti($params, $access, $acl->available());
		}
		break;

		// edycja:
		
		case 'edit':
		{
			$content_options = $page_options->get_options('edit');

			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'doc_description', 
				),
				'check' => array(
					'doc_description', 
				),
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->Edit($id, $params, $access, $acl->available());
		}
		break;

		// podgląd:
		
		case 'view':
		{
			$content_options = $page_options->get_options('view');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->View($id, $params, $access, $acl->available());
		}
		break;

		// pobieranie:
		
		case 'download':
		{
			$content_options = $page_options->get_options('download');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->Download($id, $params, $access, $acl->available());
		}
		break;

		// podgląd:
		
		case 'preview':
		{
			$content_options = $page_options->get_options('preview');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->Preview($id, $params, $access, $acl->available());
		}
		break;

		// usuwanie:
		
		case 'delete':
		{
			$content_options = $page_options->get_options('delete');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME, $db);

			$controller_object->Delete($id, $params, $access, $acl->available());
		}
		break;
	}
}
else // list of all
{
	$content_options = $page_options->get_options('multi');
	
	$mode_options = array (
		array (
			'address' => 'index.php?route=' . MODULE_NAME . '&mode=1',
			'caption' => 'Dokumenty PDF',
			'icon' => 'img/pdf.png'
		),
		array (
			'address' => 'index.php?route=' . MODULE_NAME . '&mode=2',
			'caption' => 'Nagrania dźwiękowe MP3',
			'icon' => 'img/mp3.png'
		),
	);

	$content_options = array_merge($mode_options, $content_options);
	
	$params = array(
		'content_title' => $content_title,
		'content_options' => $content_options
	);
	
	$access = array(ADMIN, OPERATOR);
	
	$acl = new AccessControlList(MODULE_NAME, $db);

	$controller_object->DrawList($params, $access, $acl->available());
}

$content_title = $controller_object->Get('content_title');
$content_options = $controller_object->Get('content_options');
$site_content = $controller_object->Get('site_content');
$site_message = $controller_object->Get('site_message');
$site_dialog = $controller_object->Get('site_dialog');

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
