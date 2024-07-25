<?php
if(!isset($analytic_page_type)) $analytic_page_type = 'CMS';
if(!isset($analytic_page_name)) $analytic_page_name = $this->getTitle();
?>
var analytic_is_loop = 1;
function getBrowser(){
  var browser = {};
  browser.name = '';
  browser.version = '';
  if(typeof UAParser === 'function'){
    var parser = new UAParser();
    if(typeof parser === 'object'){
      if(typeof parser.getBrowser === 'function'){
        var result = parser.getBrowser();
        if(typeof result === 'object'){
          if(typeof result.name !== 'undefined'){
            browser.name = result.name;
            browser.version = result.version;
          }
        }
      }
    }
  }
  return browser;
}
function setVisitor(){
  var browser = getBrowser();

  var url = '<?php echo base_url('analytic/set/'); ?>';
  var fdata = {};
  fdata.page_type = '<?=$analytic_page_type?>';
  fdata.page_url = window.location.pathname;
  fdata.browser_name = browser.name;
  fdata.browser_version = browser.version;
  if(window.location.search.length>0){
    fdata.page_url += ''+window.location.search;
  }
  fdata.page_name = '<?=$analytic_page_name?>';
  $.post(url,fdata).done(function(data){
    //console.log('tca-visitor-analytic');
  });
  if(analytic_is_loop==1){
    setTimeout(function(){
      setVisitor();
    },15000);
  }
}
$(window).on("unload",function(e){
  analytic_is_loop=0;
  setVisitor();
});
$(window).on("hashchange",function(e){
  analytic_is_loop=0;
  setVisitor();
});


//init
setVisitor();
