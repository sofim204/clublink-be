			<!-- Chat demo functionality initialized in js/app.js -> chatUi() -->
			<a href="#" class="sidebar-title">
				<i class="gi gi-comments pull-right"></i> <strong>Obrolan</strong>
			</a>
			<!-- Chat Users -->
			<ul id="chat_user_list" class="chat-users clearfix">
				<li>
					<a href="javascript:void(0)" class="chat-user-online">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar12.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="chat-user-online">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar15.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="chat-user-online">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar10.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="chat-user-online">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar4.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="chat-user-away">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar7.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="chat-user-away">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar9.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="chat-user-busy">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar16.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar1.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar4.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar3.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar13.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<span></span>
						<img src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar5.jpg" alt="avatar" class="img-circle">
					</a>
				</li>
			</ul>
			<!-- END Chat Users -->

			<!-- Chat Talk -->
			<div class="chat-talk display-none">
				<!-- Chat Info -->
				<div class="chat-talk-info sidebar-section">
					<button id="chat-talk-close-btn" class="btn btn-xs btn-default pull-right">
						<i class="fa fa-times"></i>
					</button>
					<img id="to_user_picture" src="<?php echo $this->skins->admin; ?>img/placeholders/avatars/avatar5.jpg" alt="avatar" class="img-circle pull-left">
					<span id="to_user_nama"><strong>John</strong> Doe</span>
				</div>
				<!-- END Chat Info -->

				<!-- Chat Messages -->
				<ul id="chat_conversation_list" class="chat-talk-messages">
					<li class="text-center"><small>Yesterday, 18:35</small></li>
					<li class="chat-talk-msg animation-slideRight">Hey admin?</li>
					<li class="chat-talk-msg animation-slideRight">How are you?</li>
					<li class="text-center"><small>Today, 7:10</small></li>
					<li class="chat-talk-msg chat-talk-msg-highlight themed-border animation-slideLeft">I'm fine, thanks!</li>
				</ul>
				<!-- END Chat Messages -->

				<!-- Chat Input -->
				<form action="index.html" method="post" id="sidebar-chat-form" class="chat-form">
					<input id="icto_user_id" type="hidden" name="to_user_id" />
					<input id="icto_user_nama" type="hidden" name="to_user_nama" />
					<input id="icto_picture" type="hidden" name="to_picture" value="" />
					<input id="icutype" type="hidden" name="utype" value="pesan" />
					<input id="icattachment" type="hidden" name="attachment" value="" />
					<input id="icbalasan_ke_id" type="hidden" name="balasan_ke_id" value="" />
					<input type="text" id="sidebar-chat-message" name="pesan" class="form-control form-control-borderless" placeholder="Type a message..">
				</form>
				<!-- END Chat Input -->
			</div>
			<!--  END Chat Talk -->
