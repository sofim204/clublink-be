<style>
	.description-content {
		margin: 3px;
	}
	/* styling img tag send from ckeditor */
	img {
		max-width: 100%;
		height: auto !important;
	}
</style>
<!-- by Muhammad Sofi 17 January 2022 13:44 | change background color webview to handle dark mode -->
<!-- <div id="page-content" style="background-color: #F2EEEB;"> -->
<div id="">
	<div class="row">
		<div class="container">
			<div class="description-content">
				<p><?=htmlspecialchars_decode(stripslashes($list_detail->teks))?></p>
			</div>
		</div>
	</div>
</div>
<script>
	if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
		console.log('User prefers dark mode on his device');
	} else {
		console.log("User prefers light mode on his device");
	}
</script>


