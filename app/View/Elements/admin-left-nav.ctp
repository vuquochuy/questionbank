<div class='col-lg-2'>
	<ul class="nav nav-pills nav-stacked" id='admin-left-nav'>
		<li><?php echo $this->Html->link(__('Add Questions'), array('controller' => 'Admin', 'action' => 'insertQuestions')); ?></li>
		<li><?php echo $this->Html->link(__('Add Mutiple Questions'), array('controller' => 'Admin', 'action' => 'insertMultipleQuestions')); ?></li>
		<li><?php echo $this->Html->link(__('Add Categories'), '#'); ?></li>
		<li><?php echo $this->Html->link(__('Add Subjects'), '#'); ?></li>
	</ul>
</div>

<!-- set active for current tag -->
<script>
	$(document).ready(function(){
		var path = window.location.pathname;
	    path = path.replace(/\/$/, "");
	    path = decodeURIComponent(path);

	    $("#admin-left-nav a").each(function () {
	        var href = $(this).attr('href');
	        if (path.substring(0, href.length) === href) {
	            $(this).closest('li').addClass('active');
	        }
	    });
	});
</script>