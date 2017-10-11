<?php 

	require 'config.php';
	

	llxHeader();
	dol_fiche_head();
	
	$TFkSoc = ServerObserver::getAllThirdparty();

	foreach($TFkSoc as $fk_soc) {
		
		$societe = new Societe($db);
		$societe->fetch($fk_soc);
		
		echo '<tr>
				<td>'.$societe->getNomUrl(1).'</td>
		</tr>';
		
		
	}
	
	dol_fiche_end();
	llxFooter();