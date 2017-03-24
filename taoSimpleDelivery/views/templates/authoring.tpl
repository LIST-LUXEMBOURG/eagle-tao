<section>
	<header>
		<h1><?=__('Select delivery test')?></h1>
	</header>
	<div>
		<?=get_data('formContent')?>
	</div>
	<footer>
		<button id="saver-action-<?=get_data('formId')?>" class="btn-info small"><?=__('Save')?></button>
	</footer	
</section>

<script type="text/javascript">
require(['jquery', 'i18n', 'ui/feedback'], function($, __, feedback) {
    
    $('#saver-action-<?=get_data('formId')?>').click(function(){
        var toSend = $('#<?=get_data('formId')?>').serialize();
        $.ajax({
            url: "<?=get_data('saveUrl')?>",
            type: "POST",
            data: toSend,
            dataType: 'json',
            success: function(response) {
                if (response.saved) {
                    feedback().success(__('Selection saved successfully'));
                }
            }
        });
    });
});
</script>
