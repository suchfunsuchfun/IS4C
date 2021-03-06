<?php
require_once('../../../config.php');
include($FANNIE_ROOT.'classlib2.0/FanniePage.php');
include($FANNIE_ROOT.'classlib2.0/data/FannieDB.php');
include($FANNIE_ROOT.'classlib2.0/lib/FormLib.php');

$ts_db = FannieDB::get($FANNIE_PLUGIN_SETTINGS['TimesheetDatabase']);

class TsAreasReport extends FanniePage {

	function preprocess(){
		$this->header = "Timeclock - Labor Category Totals";
		$this->title = "Timeclock - Labor Category Totals";
		return True;
	}

	function body_content(){
		global $ts_db, $FANNIE_OP_DB, $FANNIE_PLUGIN_SETTINGS;
		include('./includes/header.html');

		echo "<form action='".$_SERVER['PHP_SELF']."' method=GET>";

		$currentQ = "SELECT periodID FROM {$FANNIE_PLUGIN_SETTINGS['TimesheetDatabase']}.payperiods 
			WHERE ".$ts_db->now()." BETWEEN periodStart AND periodEnd";
		$currentR = $ts_db->query($currentQ);
		list($ID) = $ts_db->fetch_row($currentR);

		$query = "SELECT date_format(periodStart, '%M %D, %Y') as periodStart, 
			date_format(periodEnd, '%M %D, %Y') as periodEnd, periodID 
			FROM {$FANNIE_PLUGIN_SETTINGS['TimesheetDatabase']}.payperiods 
			WHERE periodStart < ".$ts_db->now()." ORDER BY periodID DESC";
		$result = $ts_db->query($query);

		echo '<p>Starting Pay Period: <select name="period">
		    <option>Please select a starting pay period.</option>';

		while ($row = $ts_db->fetch_array($result)) {
			echo "<option value=\"" . $row['periodID'] . "\"";
			if ($row['periodID'] == $ID) { echo ' SELECTED';}
			echo ">(" . $row['periodStart'] . " - " . $row['periodEnd'] . ")</option>";
		}

		echo '</select>&nbsp;&nbsp;<button value="export" name="Export">Run</button></p></form>';

		if (FormLib::get_form_value('Export') == 'export') {
			$periodID = FormLib::get_form_value('period',0);
	
			$query = "SELECT s.ShiftID as id, 
				CASE WHEN s.NiceName='' OR s.NiceName IS NULL THEN s.ShiftName
				ELSE s.NiceName END as area
				FROM (SELECT ShiftID, NiceName, ShiftName, ShiftOrder 
				FROM ".$FANNIE_PLUGIN_SETTINGS['TimesheetDatabase'].".shifts WHERE visible = 1) s 
				GROUP BY s.ShiftID ORDER BY s.ShiftOrder";
			// echo $query;
			$result = $ts_db->query($query);
			echo "<table cellpadding='5'><thead>\n<tr>
				<th>ID</th><th>Area</th><th>Total</th><th>wages</th></tr></thead>\n<tbody>\n";
			while ($row = $ts_db->fetch_row($result)) {

				echo "<tr><td>".$row['id']."</td><td>".$row['area']."</td><td align='right'>";
		
				$query1 = "SELECT SUM(IF(".$row['id']." = 31, t.vacation,t.hours)) as total 
					FROM ". $FANNIE_PLUGIN_SETTINGS['TimesheetDatabase'].".timesheet t 
					WHERE t.periodID = $periodID AND t.area = " . $row['id'];
				// echo $query1;
				$result1 = $ts_db->query($query1);
				$totHrs = $ts_db->fetch_row($result1);
				$tot = ($totHrs[0]) ? $totHrs[0] : 0;
		
				echo $tot . "</td>";
				
				$query2 = "SELECT SUM(e.pay_rate) as agg FROM ".$FANNIE_OP_DB.".employees e, ".
					$FANNIE_PLUGIN_SETTINGS['TimesheetDatabase'].".timesheet t 
					WHERE t.emp_no = e.emp_no AND t.periodID = $periodID AND t.area = " . $row['id'];
				// echo $query2;
				$result2 = $ts_db->query($query2);
				$totAgg = $ts_db->fetch_row($result2);
				$agg = ($totAgg[0]) ? $totAgg[0] : 0;
		
				// echo "<td align='right'>$agg</td>";
		
				$wages = $tot * $agg;
				
				echo "<td align='right'>" . money_format('%#8n', $wages) . "</td></tr>\n";
			}
		}
		echo "</tbody></table>\n";
	}
}

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)){
	$obj = new TsAreasReport();
	$obj->draw_page();
}


?>
