<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?=$this->getTitle()?></title>

  <meta name="description" content="<?=$this->getDescription()?>">
  <meta name="keyword" content="<?=$this->getKeyword()?>"/>
  <meta name="author" content="<?=$this->getAuthor()?>">
  <meta name="robots" content="<?=$this->getRobots()?>" />

  <!-- Icons -->
  <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
  <link rel="shortcut icon" href="<?=$this->cdn_url('skin/').$this->theme?>images/favicon.png">
  <link rel="apple-touch-icon" href="<?=$this->cdn_url('skin/').$this->theme?>images/icon57.png" sizes="57x57">
  <link rel="apple-touch-icon" href="<?=$this->cdn_url('skin/').$this->theme?>images/icon72.png" sizes="72x72">
  <link rel="apple-touch-icon" href="<?=$this->cdn_url('skin/').$this->theme?>images/icon76.png" sizes="76x76">
  <link rel="apple-touch-icon" href="<?=$this->cdn_url('skin/').$this->theme?>images/icon114.png" sizes="114x114">
  <link rel="apple-touch-icon" href="<?=$this->cdn_url('skin/').$this->theme?>images/icon120.png" sizes="120x120">
  <link rel="apple-touch-icon" href="<?=$this->cdn_url('skin/').$this->theme?>images/icon144.png" sizes="144x144">
  <link rel="apple-touch-icon" href="<?=$this->cdn_url('skin/').$this->theme?>images/icon152.png" sizes="152x152">
  <link rel="apple-touch-icon" href="<?=$this->cdn_url('skin/').$this->theme?>images/icon180.png" sizes="180x180">
  <!-- END Icons -->

  <!-- Stylesheets -->
  <!-- END Stylesheets -->

  <?php $this->getAdditionalBefore()?>
  <?php $this->getAdditional()?>
  <?php $this->getAdditionalAfter()?>

</head>
