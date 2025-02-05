<?php

if ( ! defined( 'ABSPATH' ) )
{
	exit; // Exit if accessed directly
}

if( ! class_exists( 'WP_List_Table' ) )
{
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if( ! class_exists( 'inpostparcelsHelper' ) )
{
	require_once(dirname(__FILE__) . '/inpostparcelsHelper.php');
}

add_action('init', 'tadmin_header');

class My_List_Parcel extends WP_List_Table
{
	///
	// Cloning is forbidden.
	//
	public function __clone()
	{
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'inpost' ), '1.0' );
	}

	///
	// Unserializing instances of this class is forbidden.
	//
	public function __wakeup()
	{
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'inpost' ), '1.0' );
	}

	///
	// __construct function
	//
	function __construct()
	{
		global $status, $page;

		parent::__construct( array(
		'singular'  => __( 'parcel', 'mylisttable' ),     //singular name of the listed records
		'plural'    => __( 'parcels', 'mylisttable' ),   //plural name of the listed records
		'ajax'      => false        //does this table support ajax?

		) );

		add_action( 'admin_head', array( &$this, 'admin_header' ) );
	}

	///
	// admin_header function
	//
	// @brief set up the plugin and process various actions
	//
	function admin_header()
	{
		echo '<style type="text/css">';
		echo '.wp-list-table .column-id { width: 5%; }';
		echo '.wp-list-table .column-order_id { width: 13%; }';
		echo '.wp-list-table .column-parcel_id { width: 13%; }';
		echo '.wp-list-table .column-parcel_status { width: 19%;}';
		echo '.wp-list-table .column-parcel_target_machine_id { width: 19%;}';
		echo '.wp-list-table .column-sticker_creation_date { width: 19%;}';
		echo '.wp-list-table .column-file_name { width: 19%;}';
		echo '.wp-list-table .column-creation_date { width: 19%;}';
		echo '</style>';
	}

	function no_items()
	{
		_e( 'No parcels found.' );
	}

	///
	// column_default function
	//
	// @brief Build the column data for display
	//
	function column_default( $item, $column_name )
	{
		switch( $column_name )
		{
			case 'order_id':
			case 'parcel_id':
			case 'parcel_status':
			case 'parcel_target_machine_id':
			case 'sticker_creation_date':
			case 'file_name':
			case 'creation_date':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ) ;
				// Show the whole array for troubleshooting
				// purposes.
		}
	}

	///
	// get_sortable_columns function
	//
	// @return mixed array of column names
	//
	function get_sortable_columns()
	{
		$sortable_columns = array(
			'order_id'      => array('order_id', false),
			'parcel_id'     => array('parcel_id', false),
			'parcel_status' => array('parcel_status', false),
			'parcel_target_machine_id' => array('parcel_target_machine_id', false),
			'sticker_creation_date'    => array('sticker_creation_date', false),
			'creation_date' => array('creation_date', false),
		);
		return $sortable_columns;
	}

	///
	// get_columns function
	//
	// @brief build the list of columns to be displayed.
	//
	function get_columns()
	{
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'order_id'      => __('Order ID', 'inpost'),
			'parcel_id'     => __('Parcel ID', 'inpost'),
			'parcel_status' => __('Parcel Status', 'inpost'),
			'parcel_target_machine_id' => __('Machine ID', 'inpost'),
			'sticker_creation_date'    => __('Sticker creation date', 'inpost'),
			'file_name'     => __('Download Link', 'inpost'),
			'creation_date' => __('Creation date', 'inpost'),
		);
		return $columns;
	}

	function usort_reorder( $a, $b )
	{
		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'order_id';
		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
		// Determine sort order
		$result = strcmp( $a[$orderby], $b[$orderby] );
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}

	function get_bulk_actions()
	{
		$actions = array(
			'inpost_create' => __('Create Multiple Parcels', 'inpost'),
			'stickers'      => __('Parcel Labels', 'inpost')
		);
		return $actions;
	}

	///
	// column_cb function
	//
	// @brief Set up a Check Box for group selection
	//
	function column_cb($item)
	{
        	return sprintf(
            '<input type="checkbox" name="parcel[]" value="%s" />', $item['id']
		);    
	}

	///
	// prepare_items function
	//
	// @brief Load the data from the database and paginate it
	//
	function prepare_items()
	{
		global $wpdb;

		$sort  = ( ! empty($_GET['orderby'] ) ) ? $_GET['orderby'] : 'order_id';
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

		$parcel_data  = $wpdb->get_results('select * from ' .
			$wpdb->prefix . INPOST_TABLE_NAME .
			' order by ' . $sort . ' ' . $order,
			ARRAY_A);
		$total_items  = count($parcel_data);
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$parcel_data  = $wpdb->get_results('select * from ' .
			$wpdb->prefix . INPOST_TABLE_NAME .
			' order by ' . $sort . ' ' . $order .
			' limit ' . $per_page .
			' offset ' . (($current_page - 1) * $per_page),
			ARRAY_A);

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
  

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page                     //WE have to determine how many items to show on a page
		) );
		$this->items = $parcel_data;
	}

} //class

// ------------------------- non class functions -----------------------------

function tadmin_header()
{
	$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
	if( 'my_list_test' != $page )
		return;

	if(isset($_POST['action']))
	{
		$action = $_POST['action'];

		// Do no further processing if the action is not one we care
		// about.
		if($action != 'stickers' &&
			$action != 'inpost_create')
		{
			return;
		}
	}
	else
	{
		// Nothing for us to do.
		return;
	}

	if($action !== false && isset($_POST['parcel']))
	{
		do_process($action, $_POST['parcel']);
	}
}

///
// do_process function
//
// @brief process the bulk action the user has picked.
// @param the action to be done
// @param the id of the order
//
function do_process($action, $id)
{
	global $wpdb;

	// Get the URL & API key from the options
	$options    = get_option('inpost_shipping_layers');

	$url        = $options['api_url'];
	$key        = $options['api_key'];
	$label_type = $options['label_type'];

	if($url == '' || $key == '')
	{
		// Somehow we don't have the URL or KEY.
		echo '<div id="message" class="error">';
		echo '<h1>InPost URL or API Key not set up</h1>';
		echo '<p>Please go to the InPost Shipping method and fill in this information.</p>';
		echo '</div>';

		return;
	}

	if ($label_type == '')
	{
		// Set to PDF and we will create the labels
		$label_type = 'pdf';
	}
	if(strcasecmp($label_type, 'pdf') == 0)
	{
		// PDF format selected
		$format = 'Pdf';
		$type   = 'normal';
	}
	else
	{
		// Epl2 format selected
		$format = 'Epl2';
		$type   = 'A6P';
	}

	$parcel_sticker = array();

	// Check that the last character on the URL is a '/'.
	// If not add a '/'.
	if($url[(strlen($url) - 1)] != '/')
	{
		$url .= '/';
	}

	foreach($id as $row)
	{
		// Get the data for the order
		$parcel_data  = $wpdb->get_results('select * from ' .
			$wpdb->prefix . INPOST_TABLE_NAME .
			' where id=' . $row );

		// Assign the parcel ID
		$parcel_id = $parcel_data[0]->parcel_id;

		$bits = explode(':', $parcel_data[0]->variables);

		$mobile = $bits[0];
		$size   = $bits[1];
		$email  = $bits[2];

		switch($action)
		{
			case 'inpost_create':
				if($parcel_data[0]->parcel_id != '')
				{
					// A parcel can only be generated once for a
					// line.
					continue;
				}

				$params['url']        = $url . 'parcels';
				$params['token']      = $key;
				$params['methodType'] = 'POST';
				$params['params']['description'] = 'Order # ' . $parcel_data[0]->order_id;
				$params['params']['receiver'] = array('phone' => $mobile, 'email' => $email);
				$params['params']['size'] = $size;
				$params['params']['tmp_id'] = inpostparcelsHelper::generate(4, 15);
				$params['params']['target_machine'] = $parcel_data[0]->parcel_target_machine_id;
				$reply = inpostparcelsHelper::connectInpostparcels($params);

				$parcel_id = '';

				if($reply['info']['http_code'] == '201')
				{
					$parcel_id = $reply['result']->id;
					update_parcel($row, $reply['result']->id);
				}
				else
				{
					// Failed to create a parcel.
					// Tell the user.
					echo '<div id="message" class="error">';
					echo '<h1>InPost Failed to Create Your Parcel</h1>';
					echo '<p>Please contact InPost.</p>';
					echo '<p>Error Code ' .
						$reply['info']['http_code'] .
					       '.</p>';
					echo '</div>';
					continue;
				}

				// Now pay for the parcel.
				$params['url']        = $url .
					'parcels/' .
					$parcel_id . '/pay';
				$params['token']      = $key;
				$params['methodType'] = 'POST';
				$params['params']     = array();

				$reply = inpostparcelsHelper::connectInpostparcels($params);

				if($reply['info']['http_code'] == '204')
				{
					//error_log('Parcel is paid for.');
				}
				else
				{
					// Failed to pay for a parcel.
					// Tell the user.
					echo '<div id="message" class="error">';
					echo '<h1>InPost Failed to Pay For Your Parcel</h1>';
					echo '<p>Please contact InPost.</p>';
					echo '<p>Error Code ' .
						$reply['info']['http_code'] .
					       '.</p>';
					echo '</div>';
					continue;
				}

				// Now get the details for the parcel
				$params['url']        = $url .
					'parcels/' .
					$parcel_id;
				$params['token']      = $key;
				$params['methodType'] = 'GET';
				$params['params']     = array();

				$reply = inpostparcelsHelper::connectInpostparcels($params);

				if($reply['info']['http_code'] == '200')
				{
					update_parcel_details($row, $reply['result']);
				}
				break;
			case 'stickers':
				// Create the Labels for the selected
				// parcels
				if($parcel_data[0]->file_name != '')
				{
					// A parcel's label only needs to be
					// generated once for a line.
					continue;
				}

				$parcel_sticker[] = $parcel_id;
				break;
			default:
				break;
		}
	}

	if($action == 'stickers' && count($parcel_sticker) > 0)
	{
		if(count($parcel_sticker) > 1)
		{
			$parcel_list = implode(';', $parcel_sticker);
		}
		else
		{
			$parcel_list = $parcel_sticker[0];
		}

		$params['url']        = $url .
			'stickers/' .
			$parcel_list;
		$params['token']      = $key;
		$params['methodType'] = 'GET';
		$params['params']     = array(
			'format' => $label_type,
			'id'     => $parcel_list,
			'type'   => $type,
		);

		$reply = inpostparcelsHelper::connectInpostparcels($params);

		if ($reply['info']['http_code'] == '200')
		{
			// Try and save the PDF as a local (server) file.
			$base_name = '-pdfs/' . 'stickers_' .
				date('Y-m-d_H-i-s');

			if (strcasecmp($label_type, 'pdf') == 0)
			{
				$base_name .= '.pdf';
			}
			else
			{
				$base_name .= '.epl2';
			}

			$temp_name    = explode("/", INPOST_PLUGIN_FILE);
			$temp_name    = $temp_name[count($temp_name) - 1];

			$dir_filename = INPOST_PLUGIN_FILE . $base_name;
			$filename     = plugins_url() . '/' . $temp_name . $base_name;

			$file = fopen($dir_filename, 'wb');

			if($file != false)
			{
				fwrite($file, base64_decode($reply['result']));

				fclose($file);

				// Save the change in status of the parcel.
				update_parcel_sticker($parcel_list, $filename);
			}
			else
			{
				// Failed to save the parcel data.
				// Tell the user.
				echo '<div id="message" class="error">';
					echo '<h1>InPost Failed to Save PDF Labels</h1>';
					echo '<p>Please check that the folder pdf_files has 777 permisions.</p>';
					echo '<p>Please contact InPost.</p>';
				echo '<p>Error Code ' .
					$file .
			       	'.</p>';
				echo '</div>';
			}

		}
		else
		{
			// Failed to generate labels for parcel(s)
			// Tell the user.
			echo '<div id="message" class="error">';
			echo '<h1>InPost Failed to Generate Parcel Labels</h1>';
			echo '<p>Please contact InPost.</p>';
			echo '<p>Error Code ' .
				$reply['info']['http_code'] .
			       '.</p>';
			echo '</div>';
		}
	}
}

///
// my_add_menu_items
//
function my_add_menu_items()
{
	$hook = add_menu_page( 'My Plugin List Table', 'InPost Parcels',
		'activate_plugins', 'my_list_test', 'my_render_list_page' );
	add_action( "load-$hook", 'add_options' );
}

///
// update_parcel function
//
// @brief Update the parcel record with the created ID
// @param id
// @param parcel_id
//
function update_parcel($id, $p_id)
{
	global $wpdb;

	$parcel_status = inpostparcelsHelper::getParcelStatus();

	$sql_data = array(
		'parcel_id' => $p_id,
		'parcel_status' => $parcel_status['Created']
	);

	$where_data = array(
		'id' => $id
	);

	$ret = $wpdb->update( $wpdb->prefix . INPOST_TABLE_NAME,
		$sql_data, $where_data);

	return $ret;
}

///
// update_parcel_details
//
// @brief Save the parcel details in case we need to refer to them.
// @param the order ID
// @param the parcel details
//
function update_parcel_details($id, $parcel_detail)
{
	global $wpdb;

	$sql_data = array(
		'parcel_detail' => json_encode($parcel_detail)
	);

	$where_data = array(
		'id' => $id
	);

	$ret = $wpdb->update( $wpdb->prefix . INPOST_TABLE_NAME,
		$sql_data, $where_data);

	return $ret;
}

///
// update_parcel_sticker function
//
// @param The order ID to be updated.
//
function update_parcel_sticker($id, $filename)
{
	global $wpdb;

	$sql_data = array(
		'sticker_creation_date' => date('Y-m-d H:i:s'),
		'file_name' => '<a href="' . $filename . '" target="_blank">Click Here</a>'
	);

	$new_id = explode(';', $id);

	foreach($new_id as $row)
	{
		$where_data = array(
			'parcel_id' => $row
		);

		$ret = $wpdb->update($wpdb->prefix . INPOST_TABLE_NAME,
			$sql_data, $where_data);
	}

	return $ret;
}

function add_options()
{
	global $myListTable;
	$myListTable = new My_List_Parcel();
}

add_action( 'admin_menu', 'my_add_menu_items' );

///
// my_render_list_page function
//
// @brief Show the list of parcels waiting.
//
function my_render_list_page()
{
	global $myListTable;
	echo '</pre><div class="wrap"><h2>InPost Parcel List</h2>'; 
	$myListTable->prepare_items(); 
?>
	<form method="post">
    <input type="hidden" name="page" value="ttest_list_table">
<?php
	$myListTable->display(); 
	echo '</form></div>'; 
}
