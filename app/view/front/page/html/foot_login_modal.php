<style>

</style>
    <!--LOGIN MODAL-->
    <div class="boxmodal fade text-dark" id="loginModal">
			<div class="boxmodal-dialog">
				<div class="boxmodal-content">
					<div class="boxmodal-header">
						<h5 class="boxmodal-title" id="signinModalTitle">Login</h5>
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span>
							<span class="sr-only">Close</span>
						</button>
					</div>
					<div class="boxmodal-body">
						<div id="flogin_warning" class="alert alert-warning" role="alert" style="display:none;"> </div>
						<div id="flogin_info" class="alert alert-info" role="alert" style="display:none;"> </div>
						<form id="flogin" class="" action="<?php echo base_url("account/auth/"); ?>" method="post">
							<div class="form-group">
								<label for="iemail">Email *</label>
								<input id="iemail" class="form-control form-control-lg" placeholder="Email" type="text" name="email" required />
							</div>
							<div class="form-group">
								<label for="ipassword">Password *</label>
								<input id="ipassword" class="form-control form-control-lg" placeholder="Password" type="password" name="password" required />
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="is_remember"> Remember Me
								</label>
							</div>
							<div>
								<button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
							</div>
						</form>
					</div>
					<div class="boxmodal-footer">
						<button data-toggle="modal"  data-dismiss="modal" data-target="#forgotModal"
						type="button" class="btn btn-link">Lost Password?</button>
						<a href="<?=base_url('account/register/')?>" class="btn btn-link">Register</a>
					</div>
				</div>
			</div>
		</div>
