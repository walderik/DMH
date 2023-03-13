<?php
require 'header.php';
include 'navigation_subpage.php';
?>
		<h2>Profil</h2>
		<div class="content">
			<div>
				<p><b>Ditt konto:</b></p>
				<table>
					<tr>
						<td>Namn:</td>
						<td><?= $current_user->Name ?></td>
					</tr>
					<tr>
						<td>Epost:</td>
						<td><?= $current_user->Email ?></td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>