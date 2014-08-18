<?php echo $this->Html->css('score'); ?>
<div id = 'score'>
	<h2><?php echo __('Your result'); ?></h2>

	<div class='score-result'>
	<?php echo __('Your score for this test:'); ?>
	<span id='result'>
		<?php echo $correct.'/'.$numberOfQuestions; ?>
	</span>
	<?php echo __('questions correct.'); ?>
	
	<?php
		$percentage = $numberOfQuestions==0?0:$correct/$numberOfQuestions;

		if($percentage == 1.0){
			echo __('Exellent, perfect!');
		}
		else if($percentage > 0.7){
			echo __('Good job!');
		}
		else if($percentage > 0.5){
			echo __('Keep going.');
		}
		else if($percentage > 0.3){
			echo __('Try harder.');
		}
		else{
			echo __('Do you need a tutor?');
		}
	?>
	</div>
	</br>

	<?php echo $this->Html->link(__('Return to Dashboard'), array('controller' => 'people', 'action' => 'dashboard'), array('class' => 'btn btn-primary btn-sm') ); ?>
	<hr>
	<h2><?php echo __('Details'); ?></h2>
	<?php echo $this->element('score_view'); ?>
	<hr>

	<?php echo $this->Html->link(__('Return to Dashboard'), array('controller' => 'people', 'action' => 'dashboard'), array('class' => 'btn btn-primary btn-sm') ); ?>
</div>