<?php
class Sene_Email_Sender{
/*
* Cara penggunaan
* ====================================
* $this->load("Sene_Email_Sender","lib");
*
* Identitas pengirim
* $this->Sene_Email_Sender->from("nama","email");
*
* Menambah judul email.
* $this->Sene_Email_Sender->subject("test kirim email");
*
* Menambah penerima, atau banyak penerima cukup diulang saja.
* $this->Sene_Email_Sender->to("email@domain.com");
*
* Menambahkan isi email
* 1. HTML
*   $this->Sene_Email_Sender->html("<h1>Test</h1>");
* ATAU
* 2. Teks Biasa
*   $this->Sene_Email_Sender->text("ini teks biasa");
*
* Mengirimkan email.
* $this->Sene_Email_Sender->send();
*
*
*/

	var $log = "";
	var $to = array();
	var $toname = array();
	var $cc = array();
	var $bcc = array();
	var $attachment = array();
	var $boundary = "";
	var $header = "";
	var $subject = "";
	var $body = "";
	var $eol = PHP_EOL;

	public function from($name,$mail){
		$this->boundary = md5(uniqid(time()));
		$this->header .= "From: $name <$mail>".$this->eol;
		$this->log .= "adding From $name <$mail>".$this->eol;
	}
	public function replyto($name,$mail){
		$this->header .= "Reply-To: $name <$mail>".$this->eol;
		$this->log .= "adding Reply-To $name <$mail>".$this->eol;
	}
	public function to($mail,$name=""){
		if(!empty($name)){
			$this->to[] = $mail;
			$this->toname[] = $name;
			$this->log .= "adding To $name <$mail>".$this->eol;
		}else{
			$this->to[] = $mail;
			$this->toname[] = "";
			$this->log .= "adding To $name <$mail>".$this->eol;
		}
	}

	public function cc($mail){
		$this->cc[] = $mail;
		$this->log .= "adding cc $mail".$this->eol;
	}

	public function bcc($mail){
		$this->bcc[] = $mail;
		$this->log .= "adding bcc $mail".$this->eol;
	}

	public function attachment($file){
		$this->attachment[] = $file;
		$this->log .= "adding attachment".$this->eol;
	}

	public function subject($subject){
		$this->subject = $subject;
		$this->log .= "adding subject $subject".$this->eol;
	}

	public function text($text){
		$this->body = "Content-Type: text/plain; charset=ISO-8859-1".$this->eol;
		$this->body .= "Content-Transfer-Encoding: 8bit".$this->eol;
		$this->body .= $text."".$this->eol;
		$this->log .= "adding text content".$this->eol;
	}

	public function html($html,$type="windows"){
		if($type=="windows"){
			$this->body = "Content-Type: text/html; charset=ISO-8859-1".$this->eol;
		}else{
			$this->body = "Content-Type: text/html; charset=utf-8".$this->eol;
		}

		$this->body .= "Content-Transfer-Encoding: quoted-printable".$this->eol;
		$this->body .= "<html><body>".$this->eol."".$html."".$this->eol."</body></html>".$this->eol;
		$this->log .= "adding html content \n";
	}
	public function html2($html,$type="windows"){
		if($type=="windows"){
			$this->body = "Content-Type: text/html; charset=ISO-8859-1".$this->eol;
		}else{
			$this->body = "Content-Type: text/html; charset=utf-8".$this->eol;
		}
		$this->body .= "Content-Transfer-Encoding: quoted-printable".$this->eol;
		$this->body .= $html."".$this->eol;
		$this->log .= "adding html content ".$this->eol;
	}
	public function send(){
		$max = count($this->cc);
		if($max>0){
			$this->header .= "Cc: ".$this->cc[0];
			for($i=1;$i<$max;$i++){
				$this->header .= ", ".$this->cc[$i];
			}
			$this->header .= "".$this->eol;
		}
		$max = count($this->bcc);
		if($max>0){
			$this->header .= "Bcc: ".$this->bcc[0];
			for($i=1;$i<$max;$i++){
					$this->header .= ", ".$this->bcc[$i];
			}
			$this->header .= "".$this->eol;
		}
		$this->header .= "MIME-Version: 1.0".$this->eol;
		$this->header .= "Content-Type: multipart/mixed; boundary=$this->boundary".$this->eol;
		$this->header .= "This is a multi-part message in MIME format".$this->eol;
		$this->header .= "--$this->boundary".$this->eol;
		$this->header .= $this->body;

		$max = count($this->attachment);
		if($max>0){
			for($i=0;$i<$max;$i++){
				$file = fread(fopen($this->attachment[$i], "r"), filesize($this->attachment[$i]));
				$this->header .= "--".$this->boundary."".$this->eol;
				$this->header .= "Content-Type: application/x-zip-compressed; name=".$this->attachment[$i]."".$this->eol;
				$this->header .= "Content-Transfer-Encoding: base64".$this->eol;
				$this->header .= "Content-Disposition: attachment; filename=".$this->attachment[$i]."".$this->eol;
				$this->header .= chunk_split(base64_encode($file))."".$this->eol;
				$file = "";
			}
		}
		$this->header .= "--".$this->boundary."--".$this->eol;


		$this->log .= "===== send email starting ======= ".$this->eol;

		foreach($this->to as $mail){
			$res = mail($mail,$this->subject,"",$this->header);
			if($res){
				$this->log .= "sending to $mail success".$this->eol;
			}else{
				$this->log .= "sending to $mail failed".$this->eol;
			}
		}
	}
	public function getLog(){
		return $this->log;
	}
}
