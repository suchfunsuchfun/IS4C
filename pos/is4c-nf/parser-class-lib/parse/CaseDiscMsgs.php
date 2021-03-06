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

class CaseDiscMsgs extends Parser {
	function check($str){
		if ($str == "cdinvalid" ||
		    $str == "cdStaffNA" ||
	    	    $str == "cdSSINA"){
			return True;
		}
		return False;
	}

	function parse($str){
		global $CORE_LOCAL;
		$ret = $this->default_json();
		if ($str == "cdInvalid") 
			$ret['output'] = DisplayLib::boxMsg($CORE_LOCAL->get("casediscount")._("% case discount invalid"));
		elseif ($str == "cdStaffNA") 
			$ret['output'] = DisplayLib::boxMsg(_("case discount not applicable to staff"));
		elseif ($str == "cdSSINA") 
			$ret['output'] = DisplayLib::boxMsg(_("hit 10% key to apply case discount for member")." ".$CORE_LOCAL->get("memberID"));
	
		return $ret;
	}

	function doc(){
		return "<table cellspacing=0 cellpadding=3 border=1>
			<tr>
				<th>Input</th><th>Result</th>
			</tr>
			<tr>
				<td>cdInvalid</td>
				<td>Display error message</td>
			</tr>
			<tr>
				<td>cdStaffNA</td>
				<td>Display error message</td>
			</tr>
			<tr>
				<td>cdSSINA</td>
				<td>Display instructional message</td>
			</tr>
			<tr>
				<td colspan=2><i>I'm not entirely sure
				what this one's for. It's just here
				to reproduce original pos2.php 
				functionality</td>
			</tr>
			</table>";
	}
}

?>
