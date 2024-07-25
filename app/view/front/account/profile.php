<!-- Main Content -->
   <div class="container m-t-3">
     <div class="row">

       <!-- Account Sidebar -->
				<?php $this->getThemeElement("account/sidebar",$__forward); ?>
       <!-- End Account Sidebar -->

       <!-- My Profile Content -->
       <div class="col-sm-8 col-md-9"style="padding-right: 1.5em;">
         <div class="wijet-profile-main">
           <div class="title m-b-2"><span>My Profile</span></div>
           <div class="row">
             <div class="col-xs-12">
               <ul class="list-group list-group-nav">
                 <li class="list-group-item">
                   <strong>First Name</strong>
                   <p>John</p>
                 </li>
                 <li class="list-group-item">
                   <strong>Last Name</strong>
                   <p>Thor</p>
                 </li>
                 <li class="list-group-item">
                   <strong>Email Address</strong>
                   <p>john.thor@example.com</p>
                 </li>
                 <li class="list-group-item">
                   <strong>Phone Number</strong>
                   <p>+123-456-789</p>
                 </li>
               </ul>
               <a href="<?php echo base_url(); ?>account/edit_profile" class="btn btn-theme pull-right"><i class="fa fa-pencil"></i> Edit My Profile</a>
             </div>
           </div>
         </div>
       </div>
       <!-- End My Profile Content -->

     </div>
   </div>
   <!-- End Main Content -->
