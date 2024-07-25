var drtable = {};

function gritter(isi,judul='Info',emoji='smile'){
  jQuery.gritter.add({
    title: judul,
    text: isi,
    image: '<?=base_url('favicon.png')?>',
    sticky: false,
    time: ''
  });
}

drtable = jQuery('#drtable').DataTable({
  'order': [[ 0, 'desc' ]],
  'responsive': true,
	'processing': true,
  'serverSide': true,
	'ajax': {
		type: 'POST',
		'url': '<?php echo base_url('api_web/order/'); ?>',

	},
});
