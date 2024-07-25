
<section class="main-container">
	<div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
        <div id="flogin_warning" class="alert alert-warning" role="alert" style="display:none;"> </div>
        <div id="flogin_info" class="alert alert-info" role="alert" style="display:none;"> </div>
        <form id="fregister" name="fregister" action="<?php echo base_url("account/register"); ?>" method="post">
          <div class="content">
            <h1 style="font-size:2em; font-weight:800;">Create Account</h1>
            <ul class="form-list">
              <li>
                <label for="ifnama" class="label-login">Nama Lengkap</label>
                <br>
                <input id="ifnama" type="text" title="Masukan Nama" placeholder="" class="input-text input-login required-entry" value="" name="fnama" autocorrect="off" autocapitalize="off" autofocus required />
              </li>
              <li>
                <label for="iemail" class="label-login">Email</label>
                <br>
                <input id="iemail" type="text" title="Masukan Email" placeholder="" class="input-text input-login required-entry" value="" name="email" autocorrect="off" autocapitalize="off" autofocus required />
              </li>
              <li>
                <label for="itelp" class="label-login">Nomor Hp</label>
                <br>
                <input id="itelp" type="text" title="Masukan Email" placeholder="" class="input-text input-login required-entry" value="" name="telp" autocorrect="off" autocapitalize="off" autofocus required />
              </li>
              <li>
                <label for="ipass" class="label-login">Password</label>
                <br>
                <input id="ipass" type="password" title="Masukan Password" placeholder="password" class="input-text input-login required-entry validate-password" name="password" required>
              </li>
            </ul>
            <!-- <p class="required">* Wajib Diisi</p> -->
            <div class="buttons-set" style="margin-bottom:120px;">
              <button id="send2" name="send" type="submit" class="button-style"><span>Create</span></button>
            </div>

          </div>
        </form>
      </div>
    </div>
	</div>
</section>
