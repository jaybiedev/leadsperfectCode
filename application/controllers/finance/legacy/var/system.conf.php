<?php
/*	if (!session_is_registered('SYSCONF'))
	{
		session_register('SYSCONF');
		$SYSCONF = null;
		$SYSCONF = array();
	}
*/

	if (!isset($SYSCONF)) {
		global $SYSCONF;
        $SYSCONF = array();
	}

	$CI =& get_instance();
	$qr = $CI->db->get('sysconfig');

	foreach ($qr->result() as $row) {
		$SYSCONF[$row->sysconfig]=$row->value;
	}
	
	if (empty($SYSCONF['DB']))
	{
		$SYSCONF['DB'] = $SYSCONF['DBNAME'];
	}

	$CI->db->flush_cache();

	if ($ADMIN['branch_id'] > '0') {
        $CI->db->where('branch_id', $ADMIN['branch_id']);
    }
    else {
        $CI->db->where('local', true);
	}

	$qr = $CI->db->get('branch');
	$Branch = $qr->row();

	if ($Branch) {

		$SYSCONF['BRANCH'] = $Branch->branch;
		$SYSCONF['BRANCH_ID'] = $Branch->branch_id;
		$SYSCONF['BRANCH_CODE'] = $Branch->branch_code;
		$SYSCONF['printer_type'] = $Branch->printer_type;
		$SYSCONF['BUSINESS_NAME'] .= '-' . $Branch->branch;
		$SYSCONF['BUSINESS_ADDR'] = $Branch->branch_address;

        if ($SYSCONF['printer_type'] == '') {
            $SYSCONF['printer_type'] = 'UDP DRAFT';
        }

		$SYSCONF['PRINTER_TYPE'] = $SYSCONF['printer_type'];
    }
