<style>
body {font-family: Arial;}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
</style>
</head>
<body>

<div id="page-content">
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>CRM</li>
		<li>Chat Community</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong><i class="fa fa-wechat"></i>&nbsp;Chat</strong></h2>
		</div>

		<div class="tab">
			<button class="tablinks" onclick="openCity(event, 'general')">General</button>
			<button class="tablinks" onclick="openCity(event, 'ecommerce ')">E-Commerce </button>
			<button class="tablinks" onclick="openCity(event, 'community')">Community</button>
			<button class="tablinks" onclick="openCity(event, 'admin')">Admin</button>
		</div>

		<div id="general" class="tabcontent">
			<div class="table-responsive">
				<table id="drTable" class="table table-vcenter table-condensed table-bordered">
					<thead>
						<tr>
							<th class="text-center">Chat ID</th>
							<th>Product Name</th>
							<th>Last Update</th>
							<th>Last Chat By</th> 
							<th>Buyer</th>
							<th>Seller</th>
							<th>Chat Message</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>

		<div id="ecommerce " class="tabcontent">
			<h3>E-Commerce </h3>
		</div>

		<div id="community" class="tabcontent">
			<h3>Community</h3>
		</div>

		<div id="admin" class="tabcontent">
			<h3>Admin</h3>
		</div>
	</div>
	<!-- END Content -->
</div>

<script>
	function openCity(evt, cityName) {
		var i, tabcontent, tablinks;
		tabcontent = document.getElementsByClassName("tabcontent");
		for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
		}
		tablinks = document.getElementsByClassName("tablinks");
		for (i = 0; i < tablinks.length; i++) {
			tablinks[i].className = tablinks[i].className.replace(" active", "");
		}
		document.getElementById(cityName).style.display = "block";
		evt.currentTarget.className += " active";
	}
</script>