
       <div class="row">
         <div class="col-sm-12" style="padding-right: 1.5em;">
           <div class="wijet-profile-main">
             <div class="title m-b-2"><span>Profil</span></div>
             <div class="row">
               <div class="col-md-6 col-sm-6">
                 <ul class="list-group list-group-nav">
                   <li class="list-group-item">
                     <strong>Nama</strong>
                     <p><?php echo $sess->user->fnama; ?></p>
                   </li>
                   <li class="list-group-item">
                     <strong>Email</strong>
                     <p><?php echo $sess->user->email; ?></p>
                   </li>
                   <li class="list-group-item">
                     <strong>No Telp</strong>
                     <p><?php echo (!empty($sess->user->telp) ? $sess->user->telp : '-'); ?></p>
                   </li>
                   <li class="list-group-item">
                     <strong>Jenis Kelamin</strong>
                     <p><?php echo ($sess->user->kelamin == '1' ? 'Laki-laki' : 'Perempuan'); ?></p>
                   </li>
                 </ul>
               </div>
               <div class="col-md-6 col-sm-6">
                 <ul class="list-group list-group-nav">
                   <li class="list-group-item">
                     <strong>Tgl Lahir</strong>
                     <p><?php echo (!empty($sess->user->bdate) ? $this->__dateIndonesia($sess->user->bdate) : '-'); ?></p>
                   </li>
                   <li class="list-group-item">
                     <strong>Status Akun</strong>
                     <p>
                       <?php
                       if(!empty($sess->user->is_confirmed)){
                         if(!empty($sess->user->is_premium)){
                           echo 'Premium sampai <time class="timeago" datetime="'.$sess->user->edate.'">'.$this->__dateIndonesia($sess->user->edate).'</time>';
                         }else{
                           echo 'Aktif';
                         }
                       }else{
                         echo 'Belum konfirmasi, <a id="aconfirm_email_re" href="#" title="Konfirmasi ulang" style="color: #ff0000; text-decoration: underline;">konfirmasi ulang</a>';
                       }
                       ?>
                     </p>
                   </li>
                   <li class="list-group-item hidden">
                     <strong>Alamat</strong>
                     <p>
                       <?php echo (!empty($sess->user->alamat) ? $sess->user->alamat : '-'); ?>,
                       <?php echo (!empty($sess->user->kecamatan) ? $sess->user->kecamatan : '-'); ?>,
                       <?php echo (!empty($sess->user->kabkota) ? $sess->user->kabkota : '-'); ?><br />
                       <?php echo (!empty($sess->user->provinsi) ? $sess->user->provinsi : '-'); ?>,
                       <?php echo (!empty($sess->user->negara) ? $sess->user->negara : '-'); ?>
                       <?php echo (!empty($sess->user->kodepos) ? $sess->user->kodepos : '-'); ?>
                     </p>
                   </li>
                 </ul>

               </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="btn-group pull-left">
                  <a id="aprofil_foto" href="#" target="_blank" class="btn btn-success"><i class="fa fa-file-picture-o"></i> Ganti Foto Profil</a>
                </div>
              </div>
               <div class="col-md-6">
                 <div class="btn-group pull-right">
                   <a id="aemail_buka" href="//<?php $pe = explode('@',$sess->user->email); $pe = end($pe); echo $pe; ?>" target="_blank" class="btn btn-danger">Buka email</a>
                   <a id="aprofil_edit" href="#" class="btn btn-primary "><i class="fa fa-pencil"></i> Edit Profile</a>
                   <a id="aprofil_alamat" href="#" class="btn btn-warning" style="display:none;"><i class="fa fa-pencil"></i> Edit Alamat</a>
                   <a id="aprofil_password" href="#" class="btn btn-info"><i class="fa fa-pencil"></i> Ganti Password</a>
                 </div>
               </div>

             </div>
           </div>
         </div>
       </div>


       <div id="profil_foto_modal" class="modal fade" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-sm">
           <div class="modal-content">
             <div class="modal-header">
               <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
               <h4 class="modal-title">Ganti Foto Profil</h4>
             </div>
             <div class="modal-body">
               <form id="fprofil_foto" name="fprofil_foto" action="<?php echo base_url('api_web/account/foto_ganti/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
                 <div class="form-group">
                   <div class="col-md-12 text-center">
                     <img id="preview" src="" class="img-circle img-offline img-responsive img-profile" style="max-width: 200px;" />
                   </div>
                 </div>
                 <div class="form-group">
                   <div class="col-md-12">
                     <p id="pprofil_foto" style="display:none;">Loading...</p>
                     <label for="ifoto" class="control-label">Pilih Gambar * <small>maks ukuran 300 x 300 px</small></label>
                     <input id="ifoto" type="file" name="foto" class="form-control input-lg" value="" required accept="image/*" />
                   </div>
                 </div>
                 <div class="form-group" style="display:none;">
                   <div class="col-md-12">
                     <input type="submit" value="Simpan Perubahan" class="btn btn-block" style="visibility: hidden;" />
                   </div>
                 </div>
               </form>
             </div>
           </div>
         </div>
       </div>


 <div id="modal_profil" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Profil</h4>
      </div>
      <div class="modal-body">
        <form id="fprofil" action="post" method="<?php echo base_url('account/profile/'); ?>">
          <div class="form-group">
            <label for="ifnama">Nama</label>
            <input type="text" id="ifnama" name="fnama" class="form-control" value="<?php echo $sess->user->fnama; ?>" required />
          </div>
          <div class="form-group">
            <label for="itelp">No Telp</label>
            <input type="text" id="itelp" name="telp" class="form-control" value="<?php echo $sess->user->telp; ?>" required />
          </div>
          <div class="form-group">
            <label for="ikelamin">Jenis Kelamin</label>
            <select id="ikelamin" name="kelamin" class="form-control">
              <option value="1" <?php if((int) $sess->user->kelamin == 1) echo 'selected'; ?>>Laki-laki</option>
              <option value="0" <?php if((int) $sess->user->kelamin == 0) echo 'selected'; ?>>Perempuan</option>
            </select>
          </div>
          <div>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input id="" type="submit" class="btn btn-primary" value="Simpan perubahan" />
          </div>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="modal_alamat" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Profil</h4>
      </div>
      <div class="modal-body">
        <form id="falamat" action="post" method="<?php echo base_url('account/profile/'); ?>">
          <div class="form-group">
            <label for="ialamat">Nama</label>
            <textarea id="ialamat" name="alamat" required rows="5" class="form-control"><?php echo $sess->user->alamat; ?></textarea>
          </div>
          <div class="form-group">
            <label for="iprovinsi">No Telp</label>
            <select id="iprovinsi" name="provinsi" class="form-control" required>
              <option value="">---</option>
            </select>
          </div>
          <div class="form-group">
            <label for="ikabkota">No Telp</label>
            <select id="ikabkota" name="kabkota" class="form-control" required>
              <option value="">---</option>
            </select>
          </div>
          <div class="form-group">
            <label for="ikecamatan">No Telp</label>
            <select id="ikecamatan" name="ikecamatan" class="form-control" required>
              <option value="">---</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal_password" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Profil</h4>
      </div>
      <div class="modal-body">
        <form id="fpassword" action="post" method="<?php echo base_url('account/profile/'); ?>">
          <div class="form-group">
            <label for="ioldpassword">Password lama *</label>
            <input type="password" id="ioldpassword" name="oldpassword" class="form-control" value="" required/>
          </div>
          <div class="form-group">
            <label for="ipassword">Password baru *</label>
            <input type="password" id="ipassword" name="password" class="form-control" minlength="6" value="" required />
          </div>
          <div class="form-group">
            <label for="irepassword">Ulangi password baru *</label>
            <input type="password" id="irepassword" name="repassword" class="form-control" minlength="6" value="" required />
          </div>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        </form>
      </div>
      <div class="modal-footer">
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
