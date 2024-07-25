<?php
header("Content-Type: text/xml");
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach($ds as $d){
	echo '<url>';
	foreach($d as $k=>$v){
		echo "<$k>".trim($v)."</$k>";
	}
	echo '</url>';
}
echo '</urlset>';