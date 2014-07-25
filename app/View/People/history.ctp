<div class='container'>

	<h2>Your tests history</h2>
	<table class = 'table table-striped table-condensed table-hover table-bordered'>
	<?php
		if( !empty($scores) ){
			echo $this->Html->tableHeaders(array('Test ID', 'Time taken', 'Time limit','Score'));
			foreach($scores as $score){
				echo $this->Html->tableCells(array(
					$score['Score']['test_id'],
					$score['Score']['time_taken'],
					$score['Test']['time_limit'].' mins',
					round($score['Score']['score']*100,0).'%'
					));
			}
		}
		else{
			echo 'Oops... It looks like you have no history....', '<br>';
		}
	?>
	</table>

</div>