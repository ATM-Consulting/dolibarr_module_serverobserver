<?php 

	require 'config.php';
	dol_include_once('/serverobserver/class/observer.class.php');

	llxHeader();
	dol_fiche_head();
	
	$TFkSoc = ServerObserver::getAllThirdparty();
	
	echo '<table id="table-check-all" class="border" width="100%">
			<tr class="liste_titre">
				<td>'.$langs->trans('Company').'</td>
				<td>'.$langs->trans('Status').'</td>
				<td>'.$langs->trans('Version').'</td>
				<td>'.$langs->trans('DocumentSize').'</td>
				<td>'.$langs->trans('Users').'</td>
				<td>'.$langs->trans('Modules').'</td>
			</tr>';
	
	foreach($TFkSoc as $fk_soc) {
		
		$societe = new Societe($db);
		$societe->fetch($fk_soc);
		
		echo '<tr fk_soc="'.$societe->id.'">
				<td>'.$societe->getNomUrl(1).'</td>
				<td rel="status">...</td>
				<td rel="version">...</td>
				<td rel="document">...</td>
				<td rel="user">...</td>
				<td rel="module">'.img_info().'</td>
			</tr>';
		
		
	}
	
	echo '</table>';
	
	?><script type="text/javascript">

	$(document).ready(function() {
		checkAll();
		
		setInterval('checkAll()',30000);
		
	});

	function checkAll() {

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
				$item.attr("ok",data.ok);
				
				if(data.ok) {
					console.log(data,$item);
					$item.find('td[rel=status]').html('<?php echo img_picto('','on')?>');
					$item.find('td[rel=version]').html('<a href="'+data.dolibarr.path.http+'" target="_blank">'+data.dolibarr.version+'</a>');
					$item.find('td[rel=user]').html('<?php echo img_picto('','object_user')?> '+ data.user.active);
					$item.find('td[rel=document]').html(data.dolibarr.data.size+'M');

					$item.find('td[rel=module]>img').attr('title', data.module.join(', '));

					$item.find('td[rel=module]>img').tipTip({maxWidth: "700px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50});
				}
				else {
					console.log($item);
					$item.find('td[rel=status]').html('<?php echo img_picto('','error')?>');
				}


				sortTable($("table#table-check-all"));
				
			});
			
		});
	}
	
	function sortTable($table){
		var rows = $table.find('>tbody>tr[fk_soc]').get();

		rows.sort(function(a, b) {

			var A = parseInt($(a).attr('ok'));
			var B = parseInt($(b).attr('ok'));

			if(A == 0) {
				return -1;
			}
			else if(A == 1 && B==0) {
				return 1;
			}
			

			return 0;
		});

		$.each(rows, function(index, row) {
			$table.children('tbody').append(row);
		});
	}
	
	</script><?php
	
	dol_fiche_end();
	llxFooter();