<?php
	require 'lib/function.php';
	require 'lib/rpg.php';

	if(!intval($u)) die();
	$user=mysql_fetch_array(mysql_query("SELECT name,posts,regdate,users_rpg.* FROM users,users_rpg WHERE id='$u' AND uid=id"));
	$p=$user[posts];
	$d=(ctime()-$user[regdate])/86400;
	if(!$it) $it=0;
	if(!$ne) {
		$eqitems=mysql_query("SELECT * FROM items WHERE id=$user[eq1] OR id=$user[eq2] OR id=$user[eq3] OR id=$user[eq4] OR id=$user[eq5] OR id=$user[eq6] OR id=$it") or print mysql_error();
		while($item=mysql_fetch_array($eqitems)) $items[$item[id]]=$item;
	}
	if(!$nc) {
		$class=mysql_fetch_array(mysql_query("SELECT * FROM `rpg_classes` WHERE `id` = '". $user['class'] ."'"), MYSQL_ASSOC) or print mysql_error();
	}
	if($ct){
		 $GPdif=floor($items[$user['eq'.$ct]][coins]*0.6)-$items[$it][coins];
		 $user['eq'.$ct]=$it;
	}

	$st=getstats($user,$items,$class);
	$st[GP]+=$GPdif;
	if($st[lvl]>0) $pct=1-calcexpleft($st[exp])/totallvlexp($st[lvl]);

	if (!$class) {
		$class['name']	= "None";
	}

	$img=ImageCreate(256,224 - (8 * 0));
	imagesavealpha($img, true);
	imagealphablending($img, false);
	$c[bg]=  ImageColorAllocatealpha($img, 40, 40, 90, 127);
	$c[bxb0]=ImageColorAllocate($img,  0,  0,  0);
	
// $c[bxb1]=ImageColorAllocate($img,225,200,180);
// $c[bxb2]=ImageColorAllocate($img,190,160,130);
// $c[bxb3]=ImageColorAllocate($img,130,110, 90);

	$c[bxb1]=ImageColorAllocate($img, 200, 180, 225);
	$c[bxb2]=ImageColorAllocate($img, 160, 130, 190);
	$c[bxb3]=ImageColorAllocate($img,  90, 110, 130);

	for($i=0;$i<100;$i++)
		 $c[$i]=ImageColorAllocate($img,  15+$i/1.5,  8, 20+$i);

	$c[barE1]=ImageColorAllocate($img,120,150,180);
	$c[barE2]=ImageColorAllocate($img, 30, 60, 90);
/*
	$c[bar1][ 1]	= ImageColorAllocate($img, 215,  91, 129);
	$c[bar1][ 2]	= ImageColorAllocate($img, 255, 136, 154);
	$c[bar1][ 3]	= ImageColorAllocate($img, 255, 139,  89);
	$c[bar1][ 4]	= ImageColorAllocate($img, 255, 251,  89);
	$c[bar1][ 5]	= ImageColorAllocate($img,  89, 255, 139);
	$c[bar1][ 6]	= ImageColorAllocate($img,  89, 213, 255);
	$c[bar1][ 7]	= ImageColorAllocate($img, 196,  33,  33);
	$c[bar1][ 8]	= ImageColorAllocate($img, 255,   0, 255);
	$c[bar1][ 9]	= ImageColorAllocate($img, 255,   0, 255);
	$c[bar1][10]	= ImageColorAllocate($img, 255,   0, 255);
*/
	$c[bar1][ 1]	= ImageColorAllocate($img, 215,  91, 129);
	$c[bar1][ 2]	= ImageColorAllocate($img, 255, 136, 154);
	$c[bar1][ 3]	= ImageColorAllocate($img, 255, 139,  89);
	$c[bar1][ 4]	= ImageColorAllocate($img, 255, 251,  89);
	$c[bar1][ 5]	= ImageColorAllocate($img,  89, 255, 139);
	$c[bar1][ 6]	= ImageColorAllocate($img,  89, 213, 255);
	$c[bar1][ 7]	= ImageColorAllocate($img, 196,  33,  33);
	$c[bar1][ 8]	= ImageColorAllocate($img, 196,  66, 196);
	$c[bar1][ 9]	= ImageColorAllocate($img, 100,   0, 155);
	$c[bar1][10]	= ImageColorAllocate($img, 255, 255, 255);
	$c[bar1][10]	= ImageColorAllocate($img,  55,   0,  55);

//	for ($i = 1; $i <= 10; $i++) {
//		imagefilledrectangle($img, 11 * ($i - 1), 48, 11 * $i, 60, $c[bar1][$i]);
//	}

//	$c[bar2][1]=ImageColorAllocate($img, 90, 22, 43);
//	$c[bar2][2]=ImageColorAllocate($img,151,  0, 38);
//	$c[bar2][3]=ImageColorAllocate($img,125, 37,  0);
//	$c[bar2][4]=ImageColorAllocate($img, 83, 81,  0);
//	$c[bar2][5]=ImageColorAllocate($img,  0,100, 30);
//	$c[bar2][6]=ImageColorAllocate($img,  0, 66, 93);
//	$c[bar2][7]=ImageColorAllocate($img, 70, 12, 12);
//	$c[bar2][8]=ImageColorAllocate($img,  0,255,255);
//	ImageColorTransparent($img,0);

	box( 0, 0,2+strlen($user[name]),3);
	box( 0, 3,2+strlen($class['name']),3);
	box( 0, 7,32, 4);
	box( 0,12,32, 9);
	box( 0,22,18, 6);
	box(19,22,13, 6);
// box( 0,26,32, 2);


	$fontY=fontc(255,250,240, 255,240, 80,  0, 0, 0);
	$fontR=fontc(255,230,220, 240,160,150,  0, 0, 0);
	$fontG=fontc(190,255,190,  60,220, 60,  0, 0, 0);
	$fontB=fontc(160,240,255, 120,190,240,  0, 0, 0);
	$fontW=fontc(255,255,255, 210,210,210,  0, 0, 0);

	twrite($fontW, 1, 1,0,"$user[name]");
	if ($class) {
		twrite($fontB, 1, 4,0,$class['name']);
	}

	$st[CHP]	= max($st[HP] - $user['damage'], 0);

	twrite($fontB, 1, 8,0,'HP       /');
	twrite($fontR, 3, 8,7,nlimiter($st[CHP]));
	twrite($fontY, 9, 8,7,nlimiter($st[HP]));
	twrite($fontB, 1, 9,0,'MP       /');
	twrite($fontR, 3, 9,7,nlimiter($st[MP]));
	twrite($fontY, 9, 9,7,nlimiter($st[MP]));

	for($i=2;$i<9;$i++){
		 twrite($fontB, 1,11+$i,0,"$stat[$i]");
		 twrite($fontY, 3,11+$i,7,nlimiter($st[$stat[$i]]));
	}

	twrite($fontB, 1,23, 0,'Level');
	twrite($fontY, 1,23,16,$st[lvl]);
	twrite($fontB, 1,25, 0,'EXP:');
	twrite($fontY, 1,25,16,$st[exp]);
	twrite($fontB, 1,26, 0,'Next:');
	twrite($fontY, 1,26,16,calcexpleft($st[exp]));

	twrite($fontB,20,23, 0,'Coins:');
	twrite($fontY,20,25, 0,chr(0));
	twrite($fontG,20,26, 0,chr(0));
	twrite($fontY,21,25,10,max(0, $st[GP]));
	twrite($fontG,21,26,10,max(0, $user['gcoins']));

// twrite($fontW, 1,26.5, 0,"What could go here?");

	$sc[ 1]=     1;
	$sc[ 2]=     5;
	$sc[ 3]=    25;
	$sc[ 4]=   100;
	$sc[ 5]=   250;
	$sc[ 6]=   500;
	$sc[ 7]=  1000;
	$sc[ 8]=  2500;
	$sc[ 9]=  5000;
	$sc[10]= 10000;
	$sc[11]= 99999999;

	bars();

	header('Content-type:image/png');
	ImagePNG($img);
	ImageDestroy($img);

function twrite($font,$x,$y,$l,$text){
		global $img;
		$x*=8;
		$y*=8;
		$text.='';
		if(strlen($text)<$l) $x+=($l-strlen($text))*8;
		for($i=0;$i<strlen($text);$i++)
		  ImageCopy($img,$font,$i*8+$x,$y,(ord($text[$i])%16)*8,floor(ord($text[$i])/16)*8,8,8);
//    ImageCopyResized($img,$font,$i*8+$x,$y,(ord($text[$i])%16)*8,floor(ord($text[$i])/16)*8,8,8,8,8);
}
function fontc($r1,$g1,$b1,$r2,$g2,$b2,$r3,$g3,$b3){
		$font=ImageCreateFromPNG('images/rpg/font.png');
		ImageColorTransparent($font,1);
		ImageColorSet($font,6,$r1,$g1,$b1);
		ImageColorSet($font,5,($r1*2+$r2)/3,($g1*2+$g2)/3,($b1*2+$b2)/3);
		ImageColorSet($font,4,($r1+$r2*2)/3,($g1+$g2*2)/3,($b1+$b2*2)/3);
		ImageColorSet($font,3,$r2,$g2,$b2);
		ImageColorSet($font,0,$r3,$g3,$b3);
		return $font;
}
function box($x,$y,$w,$h){
		global $img,$c;
		$x*=8;
		$y*=8;
		$w*=8;
		$h*=8;
		ImageRectangle($img,$x+0,$y+0,$x+$w-1,$y+$h-1,$c[bxb0]);
		ImageRectangle($img,$x+1,$y+1,$x+$w-2,$y+$h-2,$c[bxb3]);
		ImageRectangle($img,$x+2,$y+2,$x+$w-3,$y+$h-3,$c[bxb1]);
		ImageRectangle($img,$x+3,$y+3,$x+$w-4,$y+$h-4,$c[bxb2]);
		ImageRectangle($img,$x+4,$y+4,$x+$w-5,$y+$h-5,$c[bxb0]);
		for($i=5;$i<$h-5;$i++){
		  $n=(1-$i/$h)*100;
		  ImageLine($img,$x+5,$y+$i,$x+$w-6,$y+$i,$c[$n]);
		}
	}

	function bars(){
		global $st,$img,$c,$sc,$pct,$stat,$user;

		for($s=1;@(max($st[HP],$st[MP])/$sc[$s])>113;$s++){}
		if(!$sc[$s]) $sc[$s]=1;
		if ($st['HP'] > 0) {
			ImageFilledRectangle($img,137,41+24,136+$st[HP]/$sc[$s],47+24,$c[bxb0]);
			ImageFilledRectangle($img,136,40+24,135+$st[HP]/$sc[$s],46+24,$c[bar1][$s]);
			if ($user['damage'] > 0) {
				$dmg	= max($st[HP] - $user['damage'], 0) / $sc[$s];
				$ctemp	= imagecolorsforindex($img, $c[bar1][$s]);
				$df		= 0.6;
				ImageFilledRectangle($img,135 + $st[HP]/$sc[$s],40+24,135+$dmg,46+24,imagecolorallocate($img, $ctemp['red'] * $df, $ctemp['green'] * $df, $ctemp['blue'] * $df));
			}
				
		}
		if ($st['MP'] > 0) {
			ImageFilledRectangle($img,137,49+24,136+$st[MP]/$sc[$s],55+24,$c[bxb0]);
			ImageFilledRectangle($img,136,48+24,135+$st[MP]/$sc[$s],54+24,$c[bar1][$s]);
		}

		for($i=2;$i<9;$i++) $st2[$i]=$st[$stat[$i]];
		for($s=1;@(max($st2)/$sc[$s])>161;$s++){}
		if(!$sc[$s]) $sc[$s]=1;
		for($i=2;$i<9;$i++){
			if (floor($st[$stat[$i]]/$sc[$s]) > 0) {
				ImageFilledRectangle($img,89,65+$i*8+24,89+$st[$stat[$i]]/$sc[$s], 71+$i*8+24,$c[bxb0]);
				ImageFilledRectangle($img,88,64+$i*8+24,88+$st[$stat[$i]]/$sc[$s], 70+$i*8+24,$c[bar1][$s]);
			}
		}

		$e2	= 16 * 8;	// width of bar
		$e1	= $e2 * $pct;
		$y	= 168+1+24;
		ImageFilledRectangle($img,9,$y + 1, 8 + $e2, $y + 4, $c[bxb0]);
		ImageFilledRectangle($img,8,$y    , 7 + $e2, $y + 3, $c[barE2]);
		ImageFilledRectangle($img,8,$y    , 7 + $e1, $y + 3, $c[barE1]);
}


	function nlimiter($n) {
		if ($n <=           0) return "???";
		if ($n <=           0) $n = abs($n);
		if ($n <=       99999) return $n;
		if ($n <=     9999999) return number_format(floor($n / 1000), 0, ".", "") ."K";
		if ($n <=    99999999) return number_format(floor($n / 100000) / 10, 1, ".", "") ."M";
		if ($n <=  9999999999) return number_format(floor($n / 1000000), 0, ".", "") ."M";
		if ($n <= 99999999999) return number_format(floor($n / 100000000) / 10, 1, ".", "") ."B";
		return number_format(floor($n / 1000000000), 0, ".", "") ."B";
	}
