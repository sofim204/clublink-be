function gritter(isi,judul='Info',emoji='smile'){
  jQuery.gritter.add({
    title: judul,
    text: isi,
    image: '<?=base_url('favicon.png')?>',
    sticky: false,
    time: ''
  });
}

$("#history_detail").on("click",function(){
  $("#history_detail_list").toggle('slow');
});
