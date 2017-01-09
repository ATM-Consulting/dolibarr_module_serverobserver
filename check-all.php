<?php 

	require 'config.php';
	dol_include_once('/serverobserver/class/observer.class.php');

	llxHeader();
	dol_fiche_head();
	
	$TFkSoc = ServerObserver::getAllThirdparty();
	
	echo '<table class="border" width="100%">
			<tr class="liste_titre">
				<td>'.$langs->trans('Company').'</td>
				<td>'.$langs->trans('Status').'</td>
				<td>'.$langs->trans('Version').'</td>
				
						</tr>';
	
	foreach($TFkSoc as $fk_soc) {
		
		$societe = new Societe($db);
		$societe->fetch($fk_soc);
		
		echo '<tr fk_soc="'.$societe->id.'">
				<td>'.$societe->getNomUrl(1).'</td>
				<td rel="status">...</td>
				<td rel="version">...</td>
		</tr>';
		
		
	}
	
	echo '</table>';
	
	?><script type="text/javascript">

	$(document).ready(function() {
		$('tr[fk_soc]').each(function(i,item) {
			
			$item = $(item);
			var fk_soc = $item.attr('fk_soc');
			
			$.ajax({
				url:"<?php echo dol_buildpath('/serverobserver/script/interface.php',1)?>"
				,data:{
					fk_soc:fk_soc
					,get:"status"
				}
				,dataType:"json"
			}).done(function(data) {

				$item = $('tr[fk_soc='+data.fk_soc+']');
				
				if(data) {
					console.log(data,item);
					$item.find('td[rel=status]').html('<?php echo img_picto('','on')?>');
					$item.find('td[rel=version]').html(data.dolibarr.version);
				}
				else {
					console.log(item);
					$item.find('td[rel=status]').html('<?php echo img_picto('','error')?>');
				}
			});
			
		});
		
		
		
	});
	</script><?php
	
	dol_fiche_end();
	llxFooter();