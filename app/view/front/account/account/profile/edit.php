<!-- Main Content -->
   <div class="container m-t-3">
     <div class="row">

       <!-- Account Sidebar -->
				<?php $this->getThemeElement("account/sidebar",$__forward); ?>
       <!-- End Account Sidebar -->

       <!-- My Profile Content -->
       <div class="col-sm-8 col-md-9">
         <div class="title m-b-2"><span>Edit Profile</span></div>
         <div class="row">
           <div class="col-xs-12">
             <form>
               <div class="form-group">
                 <label for="editFirstName">First Name</label>
                 <input type="text" class="form-control" id="editFirstName" placeholder="First Name">
               </div>
               <div class="form-group">
                 <label for="editLastName">Last Name</label>
                 <input type="text" class="form-control" id="editLastName" placeholder="Last Name">
               </div>
               <div class="form-group">
                 <label for="editEmail">Email Address</label>
                 <input type="email" class="form-control" id="editEmail" placeholder="Email Address">
               </div>
               <div class="form-group">
                 <label for="editPhone">Phone Number</label>
                 <input type="text" class="form-control" id="editPhone" placeholder="Phone Number">
               </div>
               <button type="submit" class="btn btn-default btn-theme"><i class="fa fa-check"></i> Save Changes</button>
             </form>
           </div>
         </div>
       </div>
       <!-- End My Profile Content -->

     </div>
   </div>
   <!-- End Main Content -->
