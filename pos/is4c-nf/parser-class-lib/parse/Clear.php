<?php
/*******************************************************************************

    Copyright 2007 Whole Foods Co-op

    This file is part of IT CORE.

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

class Clear extends Parser {
	function check($str){
		if ($str == "CL")
			return True;
		return False;
	}

	function parse($str){
		global $CORE_LOCAL;

		$CORE_LOCAL->set("msgrepeat",0);
		$CORE_LOCAL->set("strendered","");
		$CORE_LOCAL->set("strRemembered","");
		$CORE_LOCAL->set("SNR",0);
		$CORE_LOCAL->set("wgtRequested",1);
		// added by apbw 6/04/05 to correct voiding of refunded items
		$CORE_LOCAL->set("refund",0);	
		//$CORE_LOCAL->set("autoReprint",0);
		if ($CORE_LOCAL->get("tare") > 0) 
			TransRecord::addTare(0);

		$ret = $this->default_json();
		$ret['main_frame'] = MiscLib::base_url()."gui-modules/pos2.php";
		return $ret;
	}

	function doc(){
		return "<table cellspacing=0 cellpadding=3 border=1>
			<tr>
				<th>Input</th><th>Result</th>
			</tr>
			<tr>
				<td>CL</td>
				<td>Try to clear the screen of any
				messages</td>
			</tr>
			</table>";
	}

}

?>
