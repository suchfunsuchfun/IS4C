<?php
/*******************************************************************************

    Copyright 2010 Whole Foods Co-op, Duluth, MN

    This file is part of Fannie.

    IT CORE is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    IT CORE is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IT CORE; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/

class ContactInfo extends MemberModule {

	function ShowEditForm($memNum, $country="US"){
		$dbc = $this->db();
		
		$infoQ = sprintf("SELECT CardNo,FirstName,LastName,
				street,city,state,zip,phone,email_1,
				email_2,ads_OK FROM custdata AS c
				LEFT JOIN meminfo AS m ON c.CardNo = m.card_no
				WHERE c.personNum=1 AND CardNo=%d",$memNum);
		$infoR = $dbc->query($infoQ);
		$infoW = $dbc->fetch_row($infoR);

		$labels = array();
		switch ($country) {
			case "US":
				$labels['state'] = "State";
				$labels['zip'] = "Zip";
				break;
			case "CA":
				$labels['state'] = "Province";
				$labels['zip'] = "Postal Code";
				break;
		}

		$ret = "<fieldset><legend>Contact Info</legend>";
		$ret .= "<table class=\"MemFormTable\" 
			border=\"0\">";

		$ret .= "<tr><th>First Name</th>";
		$ret .= sprintf('<td colspan="2"><input name="ContactInfo_fn" maxlength="30"
				value="%s" /></td>',$infoW['FirstName']);
		$ret .= "<th>Last Name</th>";
		$ret .= sprintf('<td colspan="2"><input name="ContactInfo_ln" maxlength="30"
				value="%s" /></td></tr>',$infoW['LastName']);

		$addrs = strstr($infoW['street'],"\n")?explode("\n",$infoW['street']):array($infoW['street'],'');
		$ret .= "<tr><th>Address</th>";
		$ret .= sprintf('<td colspan="2"><input name="ContactInfo_addr1" maxlength="125"
				value="%s" /></td>',$addrs[0]);
		$ret .= "<th>Gets Mail</th>";
		$ret .= sprintf('<td colspan="2"><input type="checkbox" name="ContactInfo_mail"
				%s /></td></tr>',($infoW['ads_OK']==1?'checked':''));
		
		$ret .= "<tr><th>Address (2)</th>";
		$ret .= sprintf('<td colspan="2"><input name="ContactInfo_addr2" maxlength="125"
				value="%s" /></td>',$addrs[1]);

		$ret .= "<th>City</th>";
		$ret .= sprintf('<td><input name="ContactInfo_city" maxlength="20"
				value="%s" size="15" /></td>',$infoW['city']);
		$ret .= "<th>{$labels['state']}</th>";
		$ret .= sprintf('<td><input name="ContactInfo_state" maxlength="2"
				value="%s" size="2" /></td>',$infoW['state']);
		$ret .= "<th>{$labels['zip']}</th>";
		$ret .= sprintf('<td><input name="ContactInfo_zip" maxlength="10"
				value="%s" size="5" /></td></tr>',$infoW['zip']);

		$ret .= "<tr><th>Phone</th>";
		$ret .= sprintf('<td><input name="ContactInfo_ph1" maxlength="30"
				value="%s" size="12" /></td>',$infoW['phone']);
		$ret .= "<th>Alt. Phone</th>";
		$ret .= sprintf('<td><input name="ContactInfo_ph2" maxlength="30"
				value="%s" size="12" /></td>',$infoW['email_2']);
		$ret .= "<th>E-mail</th>";
		$ret .= sprintf('<td colspan="4"><input name="ContactInfo_email" maxlength="75"
				value="%s" /></td>',$infoW['email_1']);

		$ret .= "</table></fieldset>";
		return $ret;
	}

	function SaveFormData($memNum){
		global $FANNIE_ROOT;
		$dbc = $this->db();
		if (!class_exists("MeminfoController"))
			include($FANNIE_ROOT.'classlib2.0/data/controllers/MeminfoController.php');
		if (!class_exists("CustdataController"))
			include($FANNIE_ROOT.'classlib2.0/data/controllers/CustdataController.php');

		$MI_FIELDS = array(
			'street' => $_REQUEST['ContactInfo_addr1'],
			'city' => $_REQUEST['ContactInfo_city'],
			'state' => $_REQUEST['ContactInfo_state'],
			'zip' => $_REQUEST['ContactInfo_zip'],
			'phone' => $_REQUEST['ContactInfo_ph1'],
			'email_2' => $_REQUEST['ContactInfo_ph2'],
			'email_1' => $_REQUEST['ContactInfo_email'],
			'ads_OK' => ($_REQUEST['ContactInfo_mail'] ? 1 : 0)
		);
		if (!empty($_REQUEST['ContactInfo_addr2']))
			$MI_FIELDS['street'] .= "\n".$_REQUEST['ContactInfo_addr2'];
		$test1 = MeminfoController::update($memNum, $MI_FIELDS);

		$CUST_FIELDS = array(
			'personNum' => array(1),
			'FirstName' => array($_REQUEST['ContactInfo_fn']),
			'LastName' => array($_REQUEST['ContactInfo_ln'])
		);
		$test2 = CustdataController::update($memNum, $CUST_FIELDS);

		if ($test1 === False || $test2 === False)
			return "Error: problem saving Contact Information<br />";
		else
			return "";
	}

	function HasSearch(){ return True; }

	function ShowSearchForm($country="US"){
		$labels = array();
		switch ($country) {
			case "US":
				$labels['state'] = "State";
				$labels['zip'] = "Zip";
				break;
			case "CA":
				$labels['state'] = "Province";
				$labels['zip'] = "Postal Code";
				break;
		}
		return "<p><b>First Name</b>: <input type='text' name='ContactInfo_fn'
				size='10' /> &nbsp;&nbsp;&nbsp; <b>Last Name</b>: 
				<input type='text' name='ContactInfo_ln' size='10' />
				<br /><br />
				<b>Address</b>: 
				<input type='text' name='ContactInfo_addr' size='15' />
				<br /><br />
				<b>City</b>: 
				<input type='text' name='ContactInfo_city' size='8' />
				<b>{$labels['state']}</b>:
				<input type='text' name='ContactInfo_state' size='2' />
				<b>{$labels['zip']}</b>:
				<input type='text' name='ContactInfo_zip' size='5' />
				<br /><br />
				<b>Email</b>: 
				<input type='text' name='ContactInfo_email' size='15' />
				</p>";
	}

	function GetSearchResults(){
		$dbc = $this->db();

		$fn = isset($_REQUEST['ContactInfo_fn'])?$_REQUEST['ContactInfo_fn']:"";
		$ln = isset($_REQUEST['ContactInfo_ln'])?$_REQUEST['ContactInfo_ln']:"";
		$addr = isset($_REQUEST['ContactInfo_addr'])?$_REQUEST['ContactInfo_addr']:"";
		$city = isset($_REQUEST['ContactInfo_city'])?$_REQUEST['ContactInfo_city']:"";
		$state = isset($_REQUEST['ContactInfo_state'])?$_REQUEST['ContactInfo_state']:"";
		$zip = isset($_REQUEST['ContactInfo_zip'])?$_REQUEST['ContactInfo_zip']:"";
		$email = isset($_REQUEST['ContactInfo_email'])?$_REQUEST['ContactInfo_email']:"";

		$where = "";
		if (!empty($fn)){
			$where .= sprintf(" AND FirstName LIKE %s",
					$dbc->escape("%".$fn."%"));
		}
		if (!empty($ln)){
			$where .= sprintf(" AND LastName LIKE %s",
					$dbc->escape("%".$ln."%"));
		}
		if (!empty($addr)){
			$where .= sprintf(" AND street LIKE %s",
					$dbc->escape("%".$addr."%"));
		}
		if (!empty($city)){
			$where .= sprintf(" AND city LIKE %s",
					$dbc->escape("%".$city."%"));
		}
		if (!empty($state)){
			$where .= sprintf(" AND state LIKE %s",
					$dbc->escape("%".$state."%"));
		}
		if (!empty($zip)){
			$where .= sprintf(" AND zip LIKE %s",
					$dbc->escape("%".$zip."%"));
		}
		if (!empty($email)){
			$where .= sprintf(" AND email_1 LIKE %s",
					$dbc->escape("%".$email."%"));
		}

		$ret = array();
		if (!empty($where)){
			$q = "SELECT CardNo,FirstName,LastName FROM
				custdata as c LEFT JOIN meminfo AS m
				ON c.CardNo = m.card_no
				WHERE 1=1 $where ORDER BY m.card_no";
			$r = $dbc->query($q);
			if ($dbc->num_rows($r) > 0){
				while($w = $dbc->fetch_row($r)){
					$ret[$w[0]] = $w[1]." ".$w[2];
				}
			}
		}
		return $ret;
	}
}

?>
