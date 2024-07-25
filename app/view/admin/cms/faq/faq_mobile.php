<style type="text/css">
	body{
		font-family: Poppins;
	}	
	
	.faq-title{
		margin-top: 20px;
	}

	.faq-title-detail{
		font-weight: bold;
		text-align: center;
	}

	.faq-subtitle-detail{
		text-align: center;
		font-size: 14px;
	}

	.accordion-body{
		margin-top: 20px;
	}

	.accordion-body .accordion .accordion-item {
		border-radius: 20px;
		border-width: 1.5px;
	}

	.accordion-body .accordion .accordion-item .accordion-button {
		border-radius: 20px;
		margin-top: -20px;
	}

	.accordion-button:not(.collapsed) {
		background: linear-gradient(90.22deg, #F97C26 0.18%, #F89A2D 99.81%);
		color: #FFFFFF;
	}	

	.accordion-button:not(.collapsed)::after {
		color-interpolation: white;
		-webkit-filter: brightness(0) invert(1);
		filter: brightness(0) invert(1);
	}
</style>
<!-- by Muhammad Sofi 17 January 2022 13:44 | change background color webview to handle dark mode -->
<div class="container-fluid" id="page-content" style="background-color: #F2EEEB;">
	<!-- TITLE SEGMENT -->	
	<div class="container faq-title">
		<?php if($language_id == "1") { ?>
			<div class="row">
				<h2 class="faq-title-detail" style="text-transform: none!important; font-family: Poppins;">Frequently Asked Questions</h2>
			</div>	
			<div class="row">
				<p class="faq-subtitle-detail">Below you will find answers to the most frequently asked questions about SellOn</p>
			</div>
		<?php } else if($language_id == "2") { ?>
			<div class="row">
				<h2 class="faq-title-detail" style="text-transform: none!important; font-family: Poppins;">Pertanyaan yang Sering Diajukan</h2>
			</div>	
			<div class="row">
				<p class="faq-subtitle-detail">Di bawah ini Anda akan menemukan jawaban atas pertanyaan yang paling sering diajukan tentang SellOn</p>
			</div>
		<?php } ?>	
	</div>	
	<!-- END TITLE SEGMENT -->

	<!-- ACCORDION -->
	<div class="container accordion-body">
		<div class="row">
			<div class="col">
				<?php foreach ($list_faq as $faq) { ?>
					<div class="accordion accordion-flush" id="accordionFlush">
						<div class="accordion-item">
							<h2 class="accordion-header" id="flush-heading<?=$faq->id?>">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?=$faq->id?>" aria-expanded="false" aria-controls="flush-collapse<?=$faq->id?>">
									<div class="faq-title">
										<div style="text-transform: none!important; font-family: Poppins;"><?=$faq->title?></div>
									</div>
								</button>
							</h2>
							<div id="flush-collapse<?=$faq->id?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?=$faq->id?>" data-bs-parent="#accordionFlush">
								<div class="accordion-body">
									<?=$faq->content?>
								</div>
							</div>
						</div>
						<br>
					</div>	
				<?php } ?> 	
			</div>
		</div>
	</div>
	<!-- END ACCORDION -->
</div>
	


