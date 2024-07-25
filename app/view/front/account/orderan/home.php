<style>
[class^="icon-"] {
  background: transparent;
  text-shadow: none;
  box-shadow: none;
}
[class^="icon-"]:hover {
  background: transparent;
  text-shadow: none;
  box-shadow: none;
  transition: all 0.5s ease;
  -webkit-transition: all 0.5s ease;
}
.icon-nav {
  margin-top: 30px;
}
.icon-nav a {
  text-align: center;
  display: block;
  background-color: #a9dee4;
  box-shadow: 0 1px 2px 0 rgba(0,0,0,0.15);
  padding: 20px 0;
  font-size: 14px;
  font-weight: 300;
  color: #777777;
  transition: all 0.2s ease-in;
  margin-bottom: 30px;
  font-family: 'Verdana';
}
.icon-nav a:hover {
  color: #FFFFFF;
  background-color: #E87169;
}
.icon-nav a i {
  display: block;
  margin-bottom: 8px;
  font-size: 28px;
}
.icon-nopadding {
  padding:0;
}
.h-nomargin {
  margin: 0;
}
.btn-transparent {
  color: #3c3c3c;
  background-color: transparent;
  border-color: #3c3c3c;
}

.btn-transparent-2 {
  color: #3c3c3c;
  background-color: transparent;
  border: none;
  text-align: left;
  padding: 0.1em;
}
.btn-transparent-2 img {
  border-radius: 4px;
}
.dr-icon-group {
  margin-bottom: 1em;
}
.dr-icon-group small {
  color: #3c3c3c;
  font-weight: 100;
  text-transform: none;
  font-size: 10px;
}
.section-title {
  border-left: 3px #b39258 solid;
  font-size: 26px;
  padding-left: 0.5em;
  min-height: 22px;
  padding-top: 3px;
  margin-top: 0.5em;
}
.terlaris-list {
  padding-top: 1em;
  margin-right: 0.5em;
  border-radius: 6px;
}
ol.ol-terlaris {
}
ol.ol-terlaris li {
  margin-bottom: 0.25em;
}
ol.ol-terlaris li a {
  font-size: 15px;
  margin-left: 0.5em;
  text-decoration: underline;
}
a.aselengkapnya {
  color: #67c77e;
  text-decoration: underline;
}
a.aselengkapnya:hover, a.aselengkapnya:focus{
  color: #3c3c3c;
  text-decoration: underline;
}
.row.terlaris-list {
  padding-top: 0.25em;
  margin-right: 0.5em;
}
.margintopall10 {
  margin-top: 1em;
}
.label {
	color: #fff;
	font-weight: normal;
	padding: 0.5em;
}

.btn.btn-secondary {
	color: #000;
  background-color: #bbbcbd;
  border-color: #9e9e9e;
}
.btn.btn-secondary:hover {
	color: #000;
  background-color: #ececec;
  border-color: #adadad;
}
.open>.dropdown-menu {
  left: -6em;
}
@media only screen and (max-width: 768px){
  .margintopmobile10 {
    margin-top: 1em;
  }
  ol.ol-terlaris li a {
    font-size: 0.9em;
  }
}
@media only screen and (max-width: 425px){
  .open>.dropdown-menu {
    left: 0;
  }
}
@media only screen and (max-width: 360px){
  .icon-nav a {
    font-size: 11px;
  }
  .margintopmobile10 {
    margin-top: 1em;
  }
  .row.terlaris-list {
    padding-top: 0;
    margin-right: 0.5em;
  }
}
@media only screen and (max-width: 320px){
  .h-nomargin.icon-nopadding {
    font-size: 1em;
  }
  .dr-icon-group small {
    display:none;
  }
}
</style>
<div class="row">

	<div class="col-md-12">
    <h2 class="section-title">History Order</h2>
		<table id="drtable" class="table table-bordered">
			<thead>
				<tr>
					<th>TranID</th>
					<th>Tgl Pesan</th>
					<th>Nama Penerima</th>
					<th>Tujuan</th>
					<th>Kurir</th>
					<th>Total</th>
					<th>Status</th>
          <th>Action</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
	</div>

</div>
