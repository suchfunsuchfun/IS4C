<?php
/*******************************************************************************

    Copyright 2009 Whole Foods Co-op

    This file is part of IS4C.

    IS4C is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    IS4C is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/

require('../login.php');
$path = guesspath();
$page_title = 'IS4C : Auth : View Users';
$header = 'IS4C : Auth : View Users';

include($path."src/header.html");

if (!validateUser('admin')){
  return;
}

showUsers();

?>
<p />
<a href=menu.php>Main menu</a>
<?php
include($path."src/footer.html");
?>
