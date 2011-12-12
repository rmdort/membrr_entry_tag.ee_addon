<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Membrr Subscribe hook. This extension works with membrr and channel protection. You can post an entry, process payment and this extension tags that entry to the user.
*
* @package			Membrr_entry_tag
* @version			1.0.1
* @author			Vinay m ~ <vinay@artminister.com>
* @link				http://artminister.com
* @license			http://creativecommons.org/licenses/by-sa/3.0/
*/

/**
 * Version 1.0 20110512
 * --------------------
 * Initial public release
 */

class Membrr_entry_tag_ext
{
	/**
	* Extension settings
	*
	* @var	array
	*/
	var $settings = array();

	/**
	* Extension name
	*
	* @var	string
	*/
	var $name = 'Membrr Entry Tag';

	/**
	* Extension version
	*
	* @var	string
	*/
	var $version = '1.0.1';

	/**
	* Extension description
	*
	* @var	string
	*/
	var $description = 'Membrr Entry Tag extension works with membrr and channel protection. You can post an entry, process payment and this extension tags that entry to the user.';

	/**
	* Do settings exist?
	*
	* @var	bool
	*/
	var $settings_exist = 'y';
	
	/**
	* Documentation link
	*
	* @var	string
	*/
	var $docs_url = 'http://artminister.com';

	// --------------------------------------------------------------------


	
  /**
	 * Constructor
	 *
	 * @author		Vinay m <rmdort@gmail.com>
 	 * @copyright	Copyright (c) Artminister
 	 * @access		Public
	 */
	function Membrr_entry_tag_ext($settings = '')
	{
		$this->EE =& get_instance();

		$this->settings = $settings;
	}
	

	/**
	 * membrr_subscribe hook
	 *
	 * @author		Vinay m <rmdort@gmail.com>
 	 * @copyright	Copyright (c) Artminister
 	 * @access		Public
	 */
	function membrr_entry_tag_id($member_id, $recurring_id, $plan_id, $end_date){
	
	  // Find the Channel ID tagged to this plan
	  $channel_query = $this->EE->db->query("SELECT channel_id FROM exp_membrr_channels WHERE plans =".$plan_id);
	  $channel_id = $channel_query->row('channel_id');	  
	  
		$query = $this->EE->db->query("SELECT entry_id FROM exp_channel_titles WHERE author_id=".$member_id." AND channel_id=1 ORDER BY entry_id DESC LIMIT 1");
		
		if($query->num_rows() > 0){
		
		  foreach($query->result_array() as $row){
		    
		    $data = array('channel_id' => $channel_id, 'channel_entry_id' => $row['entry_id'], 'recurring_id' => $recurring_id, 'active' => 1, 'channel_post_date' => '0000-00-00 00:00:00');
		    
		    $sql = $this->EE->db->insert_string('exp_membrr_channel_posts', $data);
  		  
  		  $this->EE->db->query($sql);
  		  
  		  // Update the Entry Status to paid
  		  
  		  $status_data = array("status"=>"Paid");
  		  $status_query = $this->EE->db->update_string('exp_channel_titles', $status_data, "entry_id=".$row['entry_id']);
  		  $this->EE->db->query($status_query);
  		  
		  }		  
		  		  
		}
	}
	/* End of Entry_submission_redirect */


	/**
	 * Activate this extension
	 *
	 * @author		Vinay m <rmdort@gmail.com>
	 * @copyright	Copyright (c) Artminister
	 * @access		Public
	 */
	function activate_extension()
	{
		// data to insert
		$data = 
			array(
				'class'		=> __CLASS__,
				'method'	=> 'membrr_entry_tag_id',
				'hook'		=> 'membrr_subscribe',
				'priority'	=> 10,
				'version'	=> $this->version,
				'enabled'	=> 'y',
				'settings'	=> ''
			);
		
		// insert in database
		$this->EE->db->insert('exp_extensions', $data);
	}
	/* End of Activate_extension */


	/**
	 * Update this extension
	 *
	 * @author		Vinay m <rmdort@gmail.com>
 	 * @copyright	Copyright (c) Artminister
 	 * @access		Public
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
		
		// Init data array
		$data = array();
		
		// Add version to data array
		$data['version'] = $this->version;

		// Update records using data array
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->update('exp_extensions', $data);
	}
	/* End of update_extension */


	/**
	 * Disable this extension
	 *
	 * @author		Vinay m <rmdort@gmail.com>
 	 * @copyright	Copyright (c) Artminister
 	 * @access		Public
	 */
	function disable_extension()
	{
		// Delete records
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('exp_extensions');
	}
	/* End of disable_extension */
	 
}
// END CLASS

/* End of file ext.membrr_entry_tag.php */