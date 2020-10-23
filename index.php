
<?php

	//日時設定
	$week = [
		'日',
		'月',
		'火',
		'水',
		'木',
		'金',
		'土',
	];
	$today = date( 'Y-m-d' );
	for($i = 1; $i <= 30; $i++){
		$day[$i] = date( 'Y-m-d', strtotime( $today . "+$i days"));
	}

// 変数の初期化
$page_flag = 0;
$clean = array();
$error = array();

// サニタイズ
if( !empty($_POST) ) {

	foreach( $_POST as $key => $value ) {
		$clean[$key] = htmlspecialchars( $value, ENT_QUOTES);
	} 
}

if( !empty($clean['btn_confirm']) ) {

	$error = validation($clean);

	if( empty($error) ) {

		$page_flag = 1;

		// セッションの書き込み
		session_start();
		$_SESSION['page'] = true;
	}

} elseif( !empty($clean['btn_submit']) ) {

	session_start();
	if( !empty($_SESSION['page']) && $_SESSION['page'] === true ) {

		// セッションの削除
		unset($_SESSION['page']);

		$page_flag = 2;

		// 変数とタイムゾーンを初期化
		$header = null;
		$body = null;
		$admin_body = null;
		$auto_reply_subject = null;
		$auto_reply_text = null;
		$admin_reply_subject = null;
		$admin_reply_text = null;
		date_default_timezone_set('Asia/Tokyo');
		
		//日本語の使用宣言
		mb_language("ja");
		mb_internal_encoding("UTF-8");
	
		//フォーム内容
		$contents_text = '';
		if( $clean['inquiry'] === "document" ) {
			$contents_text .= "お問い合わせ内容：資料請求\n";
		} elseif( $clean['inquiry'] === "lesson" ) {
			$contents_text .= "お問い合わせ内容：無料レッスンお申し込み\n";
		} elseif( $clean['inquiry'] === "other" ) {
			$contents_text .= "お問い合わせ内容：その他\n";
		}
		
		$contents_text .= "氏名：" . $clean['your_name'] . "\n";
		$contents_text .= "フリガナ：" . $clean['kana_name'] . "\n";
	
		if ( $clean['age'] === "1" ){
			$contents_text .= "年齢：〜19歳\n";
		} elseif ( $clean['age'] === "2" ){
			$contents_text .= "年齢：20歳〜29歳\n";
		} elseif ( $clean['age'] === "3" ){
			$contents_text .= "年齢：30歳〜39歳\n";
		} elseif ( $clean['age'] === "4" ){
			$contents_text .= "年齢：40歳〜49歳\n";
		} elseif( $clean['age'] === "5" ){
			$contents_text .= "年齢：50歳〜59歳\n";
		} elseif( $clean['age'] === "6" ){
			$contents_text .= "年齢：60歳〜\n";
		}
	
		$contents_text .= "電話番号：" . $clean['tell'] . "\n";
		$contents_text .= "メールアドレス：" . $clean['email'] . "\n";
		$contents_text .= "郵便番号：" . $clean['zip'] . "\n";
		$contents_text .= "住所：" . $clean['address'] . "\n";
		
		if( $clean['course'] === "level3" ) {
			$contents_text .= "ご希望のコース：3級コース\n";
		} elseif( $clean['course'] === "basic" ) {
			$contents_text .= "ご希望のコース：ベーシックコース\n";
		} elseif( $clean['course'] === "pro" ) {
			$contents_text .= "ご希望のコース：プロコース\n";
		} elseif( $clean['course'] === "advance" ) {
			$contents_text .= "ご希望のコース：アドバンスコース\n";
		} elseif( $clean['course'] === "short" ) {
			$contents_text .= "ご希望のコース：短期集中検定対策コース\n";
		}elseif( $clean['course'] === "self" ) {
			$contents_text .= "ご希望のコース：セルフジェルネイルコース\n";
		}
		
		$contents_text .= "無料レッスンのご希望日時\n";
		
		if( !empty($clean['first_choice']) ){
			$day = $clean['first_choice'];
			 $contents_text .= "第1希望：" .  date( 'Y年m月d日', strtotime( $day )) . '('.$week[date('w', strtotime( $day ))].')'; 
		}
		$contents_text .= $clean['first_time'] . "\n";
		

		if( !empty($clean['second_choice']) ){
			$day = $clean['second_choice'];
			 $contents_text .= "第2希望：" .  date( 'Y年m月d日', strtotime( $day )) . '('.$week[date('w', strtotime( $day ))].')'; 
		}
		$contents_text .= $clean['second_time'] . "\n";
		

		if( !empty($clean['third_choice']) ){
			$day = $clean['third_choice'];
			 $contents_text .= "第3希望：" .  date( 'Y年m月d日', strtotime( $day )) . '('.$week[date('w', strtotime( $day ))].')'; 
		}
		$contents_text .= $clean['third_time'] . "\n";




		
		$contents_text .= "その他お問い合わせ：\n" . $clean['contact'] . "\n\n";	
	
	
	
	
	
		$header = "MIME-Version: 1.0\n";
		$header = "Content-Type: multipart/mixed;boundary=\"__BOUNDARY__\"\n";
		$header .= "From: ネイルスクールドレア <'info@cullumdesign.com>\n";
		$header .= "Reply-To: ネイルスクールドレア <'info@cullumdesign.com>\n";
	
		// 件名を設定
		$auto_reply_subject = '【ネイルスクールDorea】お問い合わせありがとうございます';
		
		$auto_reply_text = $clean['your_name'] . "様\n";
		
	
		// 本文を設定
		$auto_reply_text .= "この度はお問い合せ頂き誠にありがとうございました。
改めて担当者よりご連絡をさせていただきます。\n\n";
		//$auto_reply_text .= "お問い合わせ日時：" . date("Y-m-d H:i") . "\n";
		
		$auto_reply_text .= "─ご送信内容の確認─────────────────\n";
		
		$auto_reply_text .= $contents_text;
		
		$auto_reply_text .= "──────────────────────────\n\n";
		
		$auto_reply_text .= "このメールに心当たりの無い場合は、お手数ですが
下記連絡先までお問い合わせください。

この度はお問い合わせ重ねてお礼申し上げます。\n\n";
		
		
		$auto_reply_text .= "━━━━━━━━━━━━━━━━━━━━━━━━
ネイルスクールドレア
〒604-8111
京都市中京区三条通高倉東入ル桝屋町57　京都三条ビル202号
TEL:075-255-1222
━━━━━━━━━━━━━━━━━━━━━━━━\n";
		
		// テキストメッセージをセット
		$body = "--__BOUNDARY__\n";
		$body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
		$body .= $auto_reply_text . "\n";
		$body .= "--__BOUNDARY__\n";
	
		// 自動返信メール送信
		mb_send_mail( $clean['email'], $auto_reply_subject, $body, $header);
	
		// 運営側へ送るメールの件名
		$admin_reply_subject = "【ネイルスクールDorea】お問い合わせがありました";
	
		// 本文を設定
		$admin_reply_text = "お問い合せフォームより以下の内容が届きました。\n\n";
		$admin_reply_text .= "──────────────────────────\n";
		$admin_reply_text .= $contents_text;
		$admin_reply_text .= "──────────────────────────\n\n";
		$admin_reply_text .= "━━━━━━━━━━━━━━━━━━━━━━━━\n
ネイルスクールドレア\n
〒604-8111\n
京都市中京区三条通高倉東入ル桝屋町57　京都三条ビル202号\n
TEL:075-255-1222\n
━━━━━━━━━━━━━━━━━━━━━━━━\n";

		
		// テキストメッセージをセット
		$body = "--__BOUNDARY__\n";
		$body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
		$body .= $admin_reply_text . "\n";
		$body .= "--__BOUNDARY__\n";
	
	
		// 管理者へメール送信
		mb_send_mail( 'info@cullumdesign.com', $admin_reply_subject, $body, $header);
		
	} else {
		$page_flag = 0;
	}	
}

function validation($data) {

	$error = array();

	// 氏名のバリデーション
	if( empty($data['your_name']) ) {
		$error['your_name'] = "「氏名」は必ず入力してください。";

	} 
	/*
	elseif( 20 < mb_strlen($data['your_name']) ) {
		$error[] = "「氏名」は20文字以内で入力してください。";
	}
	*/
	
	if( empty($data['kana_name']) ) {
		$error['kana_name'] = "「フリガナ」は必ず入力してください。";
	}
	
	// 電話番号のバリデーション
	if( empty($data['tell']) ) {
		$error['tell'] = "「電話番号」は必ず入力してください。";

	} elseif( !preg_match( '/\d{2,4}-?\d{2,4}-?\d{3,4}$/', $data['tell']) ) {
		$error['tell1'] = "「電話番号」は正しい形式で入力してください。(半角数字とハイフンが使用できます)";
	}


	// メールアドレスのバリデーション
	if( empty($data['email']) ) {
		$error['email'] = "「メールアドレス」は必ず入力してください。";

	} elseif( !preg_match( '/^[0-9a-z_.\/?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$/', $data['email']) ) {
		$error['email1'] = "「メールアドレス」は正しい形式で入力してください。";
	}

	if( empty($data['address']) ) {
		$error['address'] = "「住所」は必ず入力してください。";
	}



	return $error;
}
?>







<!DOCTYPE html>
<html lang="ja">

<head>



<meta name="viewport" content="width=device-width,initial-scale=1">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="./script.js?date=<?php echo date( "YmdHi", filemtime('./script.js')); ?>"></script>
<link rel="stylesheet" href="./style.css?date=<?php echo date( "YmdHi", filemtime('./style.css')); ?>" type="text/css">

<title>Dorea</title>

<meta charset="utf-8">
</head>
<body>
	
	
<section class="front1">
	<header>
		<div class="flex nowrap between align-center">
			<img class="logo" src="./img/logo.jpg">
			
			
			<div class="head_navi">
				<nav class="flex nowrap between align-center">
					<a class="menu" href="#about"><img src="./img/navi_01.png"><p>ドレアについて</p></a>
					<a class="menu" href="#reason"><img src="./img/navi_02.png"><p>選ばれる理由</p></a>
					<a class="menu" href="#voice"><img src="./img/navi_03.png"><p>実績・生徒さんの声</p></a>
					<a class="menu" href="#course"><img src="./img/navi_04.png"><p>コース紹介</p></a>
					<a class="menu" href="#teacher"><img src="./img/navi_05.png"><p>講師紹介</p></a>
					<a class="menu" href="#access"><img src="./img/navi_06.png"><p>アクセス</p></a>
					<a class="menu" href="#question"><img src="./img/navi_07.png"><p>よくある質問</p></a>
					<a class="menu navi_siryou" href="#contact"><span>資料請求はこちら</span></a>
				
				</nav>
			</div>
			<div class="menu-trigger sp">
				<span></span>
				<span></span>
				<span></span>
			</div>
			
			
			
		</div>
	</header>

	<section id="about">
		<div class="relative flex nowrap between flex-start">
				<img class="main_image" src="./img/main.jpg">
				<div class="relative">
					<img class="main_image2 pc" src="./img/main02.png">
					<h1 class="pc">ネイルスクール<span>Dorea</span></h1>
					<p class="main_text"><span>少人数制</span>で学びやすい！<br />
						徹底した<span>検定対策！</span><br />
						実践が学べる<span>ネイルサロン併設！</span><br />
						好立地！好<span>アクセス！</span></p>				
				</div>
				<img class="parts1_01" src="./img/parts1_01.png">
				<img class="parts1_02" src="./img/parts1_02.png">
				<a class="parts1_03" href="#contact"><img src="./img/parts1_03.png"></a>
				
		</div>
	</section>
	
	
	<section>
		<div class="text-bord">
			<h3>新型コロナウイルス対策について</h3>
			<h4>当スクールは生徒様に安心して通って頂けるよう<br class="sp">次の対策を行なっております。</h4>
			<p>・ 講師のマスク着用(生徒様にもマスク着用をお願いしております。)<br>
			・ スクールのドア・ドアノブ入口の消毒<br>
			・ 机や椅子の消毒<br>
			・ 定期的な換気・空気清浄機での常時空気清浄<br>
			・ 生徒様同士の感覚を空けての授業<br>
			今後も衛星・感染対策を徹底し通って頂けるよう最善を尽くしてまいりますので、安心して<br class="pc">お問い合わせ、体験見学いらっしゃって下さい。</p>
		</div>
	</section>
	
	
	
	

	<div class="balloon-wrap">
		<section class="relative">
			<div class="balloon parts1_05">
				<p>これから独立開業したいけれど、何からしていいかわからない。。
				<img src="./img/parts1_05.png"></p>
			</div>
			<div class="balloon parts1_06">
				<p>もっとネイルの技術を身につけたい！
				<img src="./img/parts1_06.png"></p>
			</div>
			<div class="balloon parts1_07">
				<p>ネイルスクールは若い子が多いイメージ·····
				<img src="./img/parts1_07.png"></p>
			</div>
			<div class="balloon parts1_08">
				<p>ネイルを副業として身に付けたいけど何からすればいいか。。
				<img src="./img/parts1_08.png"></p>
			</div>
			<div class="balloon parts1_09">
				<p>習うだけじゃなくて実践的なノウハウが知りたい！
				<img src="./img/parts1_09.png"></p>
			</div>
			<img class="parts1_04" src="./img/parts1_04.png">
		</section>
	</div>
		<div class="balloon_bottom">
			<img class="parts1_10" src="./img/parts1_10_sp.png">
		</div>

	
	
	
</section>


<section class="front2">
	<section id="reason">
		<img class="heading" src="./img/h_reason.png">
		<h2>選ばれる<span class="gold"><span class="big">4</span>つ</span>の理由</h2>
		
		<div class="reason_wrap flex wrap between">
			<div>
				<img src="./img/reason_01.jpg">
				<h3>少人数制</h3>
				<p>当ネイルスクールでは、プロのネイリスト育成のために少人数制をとり、生徒さん一人ひとりに丁寧に指導をしていきます。
				補講も設け、納得のいくまで学んでいただける環境を大切にしております。</p>
			</div>
			<div>
				<img src="./img/reason_02.jpg">
				<h3>幅広いカリキュラム</h3>
				<p>独立開業をめざして通われる方や、既にプロとして活躍されているネイリストさんがスキルアップのために通われたり、さまざまな生徒さんがいらっしゃいます。
				皆様にあわせて、細かな指導をさせていただきます。
				ご自身でネイルを楽しみたい方には、セルフネイル講座も開設しております。</p>
			</div>
			<div>
				<img src="./img/reason_03.jpg">
				<h3>使える技術が学べる</h3>
				<p>資格は取得したけど使えない、なぜ試験に落ちたかわからない、検定以外の事に対応できない。。
				そんな事が無いようにドレアでは資格を取得するだけではなく正しい技術と正しい知識を学んでいただく事により、
				技術に自信を持てて幅広いお客様のニーズに対応できるネイリストを育てます。</p>
			</div>
			<div>
				<img src="./img/reason_04.jpg">
				<h3>確実な検定対策</h3>
				<p>当ネイルスクールで常任本部認定講師が試験官経験者の目線で本番同様の検定対策があり、
				当ネイルスクールの生徒さんは、検定試験の合格率も非常に高いのも特徴です。</p>
			</div>
		
		</div>
		
		
	</section>


</section>


	<section class="front3">
		<a href="#contact"><img src="./img/form_u_1.png"></a>
		<a href="#contact"><img src="./img/form_u_2.png"></a>
		
		<img class="parts3_01" src="./img/parts3_01.png">
		<img class="parts3_02" src="./img/parts3_02.png">
	</section>



<div class="front4_back">
</div>
<section class="front4">
	<section id="voice">
		<img class="heading" src="./img/h_voice.png">
		<h2>実績・生徒さんの声</h2>
		
		<div class="flex between flex-start wrap voice_box">
			<div class="voice_wrap voice_1">
				<img src="./img/voice_1.png">
				<h4>卒業生/30代ネイルサロン経営</h4>
				<blockquote>初めてちゃんとなりたい自分が見つかりました！
				<br>20代後半で転職を考えていた時にネイルは好きだし何となくで見学に行ってスクールの和気藹々とした雰囲気が素敵だなと思って通い始めましたが、一人一人に親身になって御指導下さる先生のおかげで具体的な目標を持ってステップアップでき、自分でサロンを開業するという夢が叶いました！今はまた先生の様な講師になるという目標を目指し頑張っています！</blockquote>
			</div>
			<div class="voice_wrap voice_2">
				<img src="./img/voice_2.png">
				<h4>卒業生/20代ネイルサロン勤務</h4>
				<blockquote>ドレアに通って良かった事は、1日の人数が多くないので先生に細かくしっかりと教えて頂け、やり方や見本も先生が実際にやってくれるので、言葉だけでなく目で見ても学べました！
					<br>先生は面白くて、冗談もスクール内でよく飛び交っていて、緊張せずリラックスして気楽に学べ、練習できました。検定前になると、検定のように先生が点数をつけてアドバイスをしてくれるので、そこも凄くいいなと思います。
					<br>とにかく先生も生徒さんも皆さんいい人達ばかりで、年齢関係なく色々な話もするので、スクール内で最年少だった私も気楽に通えました。
					<br>アットホームな感じで技術もしっかり教えて頂け、凄くいい環境で学ぶ事ができたなぁと思います！</blockquote>
			</div>
			<div class="voice_wrap voice_3">
				<img src="./img/voice_3.png">
				<h4>スクール生/40代販売店勤務</h4>
				<blockquote>わたしの通ってるスクールは少人数制なので、予約をとって好きな曜日で通えます！
					<br>わたしの場合は、仕事をして子育てしているので平日で通える所を探していました。たくさん資料請求した中で、少人数制で先生が男性というところに興味を持ちました。最初、色々スクールは悩みましたが、見学体験させていただき、先生も優しく生徒さん達との雰囲気も良かったので、ここに決めました。
					<br>みっちり4時間、一人一人に合った指導をJNA常任本部認定講師の先生に丁寧に教えて頂けます。
					<br>気軽に質問疑問に答えてもらえるのもドレアネイルスクールに良さですよ。</blockquote>
			</div>
			<div class="voice_wrap voice_4">
				<img src="./img/voice_4.png">
				<h4>卒業生Tさん/30代認定講師</h4>
				<blockquote>ドレアネイルスクールに通い、中元寺先生の親切で的確なご指導アドバイスを受け、何度チャレンジしてもダメだった認定講師試験に見事合格することができました！！
					<br>ドレアネイルスクールに通って、本当に良かったと思います。プロのネイリストに必ずなれますよ！！
					<br>認定講師の表彰式に、中元寺先生と、一緒に行くことができて、最高に幸せでした。</blockquote>
			</div>
			<div class="voice_wrap voice_5">
				<h4>卒業生Mさん/20代美容関係勤務</h4>
				<blockquote>「一度見学に来てみませんか？」とメールでお声掛け頂いて恐る恐る行ったのですがアットホームな雰囲気が心地よく、私には合っていると思いこちらに決めました。
					<br>先生は基礎を大事にされているので授業内容も基礎から応用をしっかり教えてくれます。
					<br>趣味の範囲では気付かない細かな技術を得ることができ、アットホームで少人数だからこそ叶う授業なのかなと思います。
					<br>試験に合格した後でも通いたくなるようなスクールは珍しいのではないでしょうか。
					<br>ネイルの知識だけではなく日々の苦戦や成長に細やかに気付いてくれるネイル初心者やスキルアップを目指す方におすすめスクールです。</blockquote>
			</div>
			<div class="voice_wrap voice_6">
				<h4>スクール生Nさん/40代販売店勤務</h4>
				<blockquote>昔からネイルが大好きで自分でやってみたいと思い子育てが一段落した頃ドレアに見学に行きました。
					<br>年齢的に無理かなぁっていうのが一番不安だったのですが　先生に遅いなんてことはないと言っていただき入学しました。
					<br>ネイルの勉強は奥が深くて意外に地味な作業も多くなかなかうまくいかないこともありますが　できるまで先生がしっかりアドバイスしてくださいます。
					<br>アットホームなスクールで他の生徒さんや卒業された方とも交流できたりで楽しんでスクールに通わせてもらってます。
					<br>ネイルスクールはたくさんありますが　ドレアに決めてよかったです！</blockquote>
			</div>
			
			
			
			
			
		</div>
	


	</section>
</section>

<section class="front5">
	<img class="course_back pc" src="./img/back06.png">
	<section id="course" class="flag">
		<img class="heading" src="./img/h_course.png">
		<h2>コース紹介</h2>
		
		<div class="course_box flex wrap between">
			<div class="course_wrap">
				<p class="course_title1">Beginner’s class</p>
				<img src="./img/course_01.jpg">
				<div class="course_name">
					<h3>3級コース</h3>
					<p>検定3級・ジェル検定初級対応</p>
				</div>
				
				
				<p class="note">ネイルの基本であるケア・カラーリング・ソフトジェルを学んで頂きます。検定3級・ジェル検定初級に対応したカリキュラムとなっております。</p>
				<ul>
					<h4>授業内容</h4>
					<li>爪の構造</li>
					<li>皮膚科学</li>
					<li>生理解剖学</li>
					<li>爪の病気</li>
					<li>ネイルケア</li>
					<li>カラーリング</li>
					<li>ジェル（初級）</li>
					<h4>授業時間・費用</h4>
					<p>全12回（授業時間：48時間）</p>
					<li>入学金：10,000円</li>
					<li>受講料：140,000円</li>
					<li>合計　：150,000円＋教材費</li>
					<p class="gray">※受講期限 3ヶ月</p>
				</ul>
			</div>
			<div class="course_wrap">
				<p class="course_title2">basic course</p>
				<img src="./img/course_02.jpg">
				<div class="course_name">
					<h3>ベーシックコース</h3>
					<p>検定2級・ジェル検定中級&nbsp;対応</p>
				</div>
				<p class="note">3級コースより高度なケア・カラーリングを学んで頂き、爪のリペアとなるチップラップやジェルフレンチ・グラデーションがカリキュラムに入っております。
				検定試験の2級・ジェル中級に対応しています。</p>
				<ul>
					<h4>授業内容</h4>
					<li>リペア</li>
					<li>チップラップ</li>
					<li>ジェルフレンチ</li>
					<li>ジェルグラデーション</li>
					<p class="purple">※こちらのコースには、3級コースまでの内容が含まれます。</p>
					<h4>授業時間・費用</h4>
					<p>全25回（授業時間：100時間）</p>
					<li>入学金：10,000円</li>
					<li>受講料：300,000円</li>
					<li>合計　：310,000円＋教材費</li>
					<p class="gray">※受講期限6ヶ月</p>
				</ul>
			</div>
			<div class="course_wrap">
				<p class="course_title3">Pro course</p>
				<img src="./img/course_03.jpg">
				<div class="course_name">
					<h3>プロコース</h3>
					<p>検定1級・ジェル検定上級&nbsp;対応</p>
				</div>
				<p class="note">プロとしてサロンワークの即戦力の基本と応用も学んで頂きます。検定1級（スカルプチュア）・ジェル検定上級に対応したカリキュラムとなっております。</p>
				<ul>
					<h4>授業内容</h4>
					<li>スカルプチュア</li>
					<li>チップオーバーレイ</li>
					<li>3Dアート</li>
					<li>フィルイン</li>
					<li>ジェルイクステンション</li>
					<p class="red">※こちらのコースには、ベーシックコースまでの内容が含まれます。</p>
					<h4>授業時間・費用</h4>
					<p>全50回（授業時間：200時間）</p>
					<li>入学金：10,000円</li>
					<li>受講料：550,000円</li>
					<li>合計　：560,000円＋教材費</li>
					<p class="gray">※受講期限 12ヶ月</p>
				</ul>
			</div>
			<div class="course_wrap">
				<p class="course_title4">Advance course</p>
				<img src="./img/course_04.jpg">
				<div class="course_name">
					<h3>アドバンスコース</h3>
					<p>トータルテクニック・独立開業</p>
				</div>
				<p class="note">独立や認定講師試験にも対応したカリキュラムとなっております。サロンワークでの即戦力など、さらに高度な技術を学んで頂けます。</p>
				<ul>
					<h4>授業内容</h4>
					<li>フレンチカラー</li>
					<li>サロン実習独立サポート</li>
					<li>フレンチスカルプチュア</li>
					<li>デザインスカルプチュア</li>
					<li>マシーン</li>
					<li>サロンワークテクニック</li>
					<p class="yellow">※こちらのコースには、プロコースまでの内容が含まれます。</p>
					<h4>授業時間・費用</h4>
					<p>全75回（授業時間：300時間）</p>
					<li>入学金:10,000円</li>
					<li>受講料:850,000円</li>
					<li>合計　:860,000円＋教材費</li>
					<p class="gray">※受講期限 18ヶ月</p>
				</ul>
			</div>
		</div>
		<div class="course_box2 flex wrap between flex-start">
			<div class="course_wrap2 flex flex-start">
				<img class="pc" src="./img/course_05.jpg">
				<div>
					<p class="course_title5">Exam course</p>
					<div class="course_name">
						<h3>短期集中検定対策コース</h3>
					</div>
					<p class="note flex-start">
						<img class="sp" src="./img/course_05.jpg">
						<span>検定試験に特化したコースです。独学や職業訓練校・他校卒業の方を対象に検定試験を合格する為のポイントを理解して
						ネイリスト検定試験・ジェル検定試験に合格する為のコースです。
						※トレーニングハンドをご持参ください。
					</span></p>
				<ul>
					<h4>授業時間・費用</h4>
					<li>入学金：10,000円（６ヶ月有効）</li>
					<li>受講料：４時間×４回 30,000円 （有効期限１ヶ月）</li>
				</ul>					
				</div>
			</div>
			<div class="course_wrap2 flex flex-start">
				<img class="pc" src="./img/course_06.jpg">
				<div>
					<p class="course_title6">Self gel nail course</p>
					<div class="course_name">
						<h3>セルフジェルネイルコース</h3>
					</div>
					<p class="note flex-start">
						<img class="sp" src="./img/course_06.jpg">
						<span>個人でジェルネイルを楽しむ方が増えています。「自分でするのは難しそう・・・爪がボロボロになりそう・・・」
						と敬遠している人も、基本技術や知識を覚えれば大丈夫！安全にジェルネイルを楽しんでいただく為の講座です。
					</span></p>
				<ul>
					<h4>授業時間・費用</h4>
					<li>受講料：1回 4時間…8,000円<br />
							　　　　 5回 4時間×5…30,000円<br />
							道具は持参してください(レンタル可　１回　1,000円)<br />
							材料は購入する事も可能です。</li><br />
				</ul>					
				</div>
			</div>
		</div>
		
	
	</section>
</section>


	<section class="front3 second">
		<a href="#contact"><img src="./img/form_u_1.png"></a>
		<a href="#contact"><img src="./img/form_u_2.png"></a>
		
		<img class="parts3_01" src="./img/parts3_01.png">
		<img class="parts3_02" src="./img/parts3_02.png">
	</section>


<section class="front5">
	<section id="teacher" class="ac padding">
		<img class="heading" src="./img/h_teacher.png">
		<h2>講師紹介</h2>
		<div class="teacher_wrap relative">
			<img class="teacher_img" src="./img/teacher_sp.png">
			<div>JNA日本ネイリスト協会 常任本部認定講師
				<br />JNA認定 衛生管理指導員
				<br />JNA認定ネイルサロン 技術指導者
				<ul>
					<li>Presto エデュケーター</li>
					<li>Nail de dance インストラクター</li>
					<li>Peking エデュケーター</li>
					<li>エクセレントマニキュア 指導員</li>
				</ul>
			</div>
			<p>技術や知識はスクール（企業・施設・肩書）では無く講師（人）から学ぶものと考えています。スクールに通う事だけで満足にならないように、技術や知識を得る楽しみや満足が得れるように入学から卒業まで一人の講師が個々に対応したカリキュラムで「わからない」が無いよう一人一人細かく指導します。
				<br />一度ご見学にお越しいただき、たくさんの夢と希望、そして悩みや不安をお聞かせ下さい。お会いできることを心より楽しみにしています。
				<br /><span>代表　中元寺 寛</span>
			</p>
		</div>
		
		
		
		<img id="access" class="heading" src="./img/h_access.png">
		<h2>アクセス</h2>
		<div class="access_wrap">
			<img src="./img/map.png">
			<p>〒604-8111<br />
				京都市中京区三条通高倉東入ル桝屋町57<br class="sp"> 京都三条ビル202号<br />
				TEL:075-255-1222</p>
		</div>
		
	</section>
</section>


	<section class="front3">
		<a href="#contact"><img src="./img/form_u_1.png"></a>
		<a href="#contact"><img src="./img/form_u_2.png"></a>
		
		<img class="parts3_01" src="./img/parts3_01.png">
		<img class="parts3_02" src="./img/parts3_02.png">
	</section>


<div class="front4_back second">
</div>
<section class="front4 second">
	<section id="question">
		<img class="heading" src="./img/h_question.png">
		<h2>よくある質問</h2>
		
		
		<div class="faq_box padding">
		
			<dl class="faq_wrap">
				<dt>ネイルスクールは若い生徒さんが多いイメージですが・・</dt>
				<dd>ドレアでは２０代から４０代の生徒さんが多くいらっしゃいます、主婦の方や働きながら通学される方も多く、落ち着いた雰囲気で学んでいただけます。</dd>
			</dl>
			<dl class="faq_wrap">
				<dt>卒業後の進路に悩みます・・・</dt>
				<dd>スクールではサロンワークで使える技術も学んでいただきますのでネイルサロンへの就職・開業独立や、ホームサロンなどご相談にのりバックアップさせていただきます。</dd>
			</dl>
			<dl class="faq_wrap">
				<dt>まずはベーシックコースから始めたいのですが・・・</dt>
				<dd>入学するまではネイルのことやスクールの雰囲気も分からず不安になります。まずはベーシックコースからスタートして後にコースを変更することも可能です。</dd>
			</dl>
			<dl class="faq_wrap">
				<dt>スクール選びに悩んでいます</dt>
				<dd>ドレアでは一人の講師が入学から卒業まで変わらず指導いたしますので混乱することなく、少人数で個々に合わせたカリキュラムで進めていきますので「わからない」が無いように正しく技術と知識を学んでいただけます。
				検定試験取得だけのスクールには無いサロンワークで使える技術と知識も学んでいただけるのもドレアの特徴です。</dd>
			</dl>
			<dl class="faq_wrap">
				<dt>初心者ですが大丈夫でしょうか・・・</dt>
				<dd>大丈夫です、最初は皆さん初心者です。最初のスクール選びが今後とても大切な事と考えています。</dd>
			</dl>
			<dl class="faq_wrap">
				<dt>検定試験などの資格は必要ですか？</dt>
				<dd>検定試験取得は就職やお客様に対して一つの目安となりますので取得されるほうが賢明かと思います。資格がなくてもネイリストになれないことはないですが資格以外にも使える技術と知識があれば、
				「手に職」として様々な状況に対応ができます。</dd>
			</dl>
			
			
		</div>
		
		
	</section>
</section>
<div class="front4_back second bottom">
</div>

	<section id="contact">
		<img class="heading h_contact" src="./img/h_contact.png">
	</section>
	<section class="form_wrap">
		<img class="contact_01 sp" src="./img/parts_f1.png">
		<img class="contact_02 sp" src="./img/parts_f2.png">
		<img class="contact_03" src="./img/parts_f3.png">
		<img class="contact_04" src="./img/parts_f4.png">
		<div>
		<h3>お問い合わせフォーム</h3>
		
		
	







<?php if( $page_flag === 1 ): 
// 確認ページ
?>

<form method="post" action="#form" id="form">
	<div class="element_wrap">
		<label>お問い合わせ内容</label>
		<p><?php if( $_POST['inquiry'] === "document" ){ echo '資料請求'; }
		elseif( $_POST['inquiry'] === "lesson" ){ echo '無料レッスンお申し込み'; }
		elseif( $_POST['inquiry'] === "other" ){ echo 'その他'; } ?></p>
	</div>
	<div class="element_wrap">
		<label>氏名</label>
		<p><?php echo $_POST['your_name']; ?></p>
	</div>
	<div class="element_wrap">
		<label>フリガナ</label>
		<p><?php echo $_POST['kana_name']; ?></p>
	</div>
	<div class="element_wrap">
		<label>年齢</label>
		<p><?php if( $_POST['age'] === "1" ){ echo '〜19歳'; }
		elseif( $_POST['age'] === "2" ){ echo '20歳〜29歳'; }
		elseif( $_POST['age'] === "3" ){ echo '30歳〜39歳'; }
		elseif( $_POST['age'] === "4" ){ echo '40歳〜49歳'; }
		elseif( $_POST['age'] === "5" ){ echo '50歳〜59歳'; }
		elseif( $_POST['age'] === "6" ){ echo '60歳〜'; } ?></p>
	</div>
	<div class="element_wrap">
		<label>電話番号</label>
		<p><?php echo $_POST['tell']; ?></p>
	</div>
	<div class="element_wrap">
		<label>メールアドレス</label>
		<p><?php echo $_POST['email']; ?></p>
	</div>
	<div class="element_wrap">
		<label>郵便番号</label>
		<p><?php echo $_POST['zip']; ?></p>
	</div>
	<div class="element_wrap">
		<label>住所</label>
		<p><?php echo $_POST['address']; ?></p>
	</div>
	<div class="element_wrap">
		<label>ご希望のコース</label>
		<p><?php if( $_POST['course'] === "level3" ){ echo '3級コース'; }
		elseif( $_POST['course'] === "basic" ){ echo 'ベーシックコース'; }
		elseif( $_POST['course'] === "pro" ){ echo 'プロコース'; }
		elseif( $_POST['course'] === "advance" ){ echo 'アドバンスコース'; }
		elseif( $_POST['course'] === "short" ){ echo '短期集中検定対策コース'; }
		elseif( $_POST['course'] === "self" ){ echo 'セルフジェルネイルコース'; } ?></p>
	</div>
	<div class="element_wrap">
		<label>無料体験レッスンの<br class="pc">ご希望日時</label>
		<div>
			<label>第1希望</label>
			<p>
				<?php
					if( !empty($_POST['first_choice']) ){
						$day = $_POST['first_choice'];
						 echo date( 'Y年m月d日', strtotime( $day )) . '('.$week[date('w', strtotime( $day ))].')'; 
					 }
					
				?>
			<?php echo $_POST['first_time']; ?>
			</p>
		</div>
		<div>
			<label>第2希望</label>
			<p>
				<?php
					if( !empty($_POST['second_choice']) ){
						$day = $_POST['second_choice'];
						 echo date( 'Y年m月d日', strtotime( $day )) . '('.$week[date('w', strtotime( $day ))].')'; 
					 }
					
				?>
			<?php echo $_POST['second_time']; ?>
			</p>
		</div>
		<div>
			<label>第3希望</label>
			<p>
				<?php
					if( !empty($_POST['third_choice']) ){
						$day = $_POST['third_choice'];
						 echo date( 'Y年m月d日', strtotime( $day )) . '('.$week[date('w', strtotime( $day ))].')'; 
					 }
					
				?>
			<?php echo $_POST['third_time']; ?>
			</p>
		</div>
	</div>
	
	<div class="element_wrap">
		<label>その他お問い合わせ</label>
		<p><?php echo nl2br($_POST['contact']); ?></p>
	</div>
	
	<div class="submit_wrap">
		<div class="back">
			<input type="submit" name="btn_back" value="　戻る　">
		</div>
		<div>
			<input type="submit" name="btn_submit" value="送信する">
		</div>
	</div>
	
	<input type="hidden" name="inquiry" value="<?php echo $_POST['inquiry']; ?>">
	<input type="hidden" name="your_name" value="<?php echo $_POST['your_name']; ?>">
	<input type="hidden" name="kana_name" value="<?php echo $_POST['kana_name']; ?>">
	<input type="hidden" name="age" value="<?php echo $_POST['age']; ?>">
	<input type="hidden" name="tell" value="<?php echo $_POST['tell']; ?>">
	<input type="hidden" name="email" value="<?php echo $_POST['email']; ?>">
	<input type="hidden" name="zip" value="<?php echo $_POST['zip']; ?>">
	<input type="hidden" name="address" value="<?php echo $_POST['address']; ?>">
	<input type="hidden" name="course" value="<?php echo $_POST['course']; ?>">
	<input type="hidden" name="first_choice" value="<?php echo $_POST['first_choice']; ?>">
	<input type="hidden" name="first_time" value="<?php echo $_POST['first_time']; ?>">
	<input type="hidden" name="second_choice" value="<?php echo $_POST['second_choice']; ?>">
	<input type="hidden" name="second_time" value="<?php echo $_POST['second_time']; ?>">
	<input type="hidden" name="third_choice" value="<?php echo $_POST['third_choice']; ?>">
	<input type="hidden" name="third_time" value="<?php echo $_POST['third_time']; ?>">
	<input type="hidden" name="contact" value="<?php echo $_POST['contact']; ?>">
</form>



<?php elseif( $page_flag === 2 ): 
// 完了ページ
?>

<p class="complete">お問い合わせありがとうございました。<br />
返信までお待ちくださいますようお願い申し上げます。</p>

<?php else: 
//入力ページ
?>

<form method="post" action="#form" id="form">



	<div class="element_wrap">
		<label>お問い合わせ内容</label>
		<div>
			<label class="checkbox" for="inquiry_document"><input id="inquiry_document" type="checkbox" name="inquiry" value="document" <?php if( ($_POST['inquiry'] == 'document' ) ){ echo 'checked'; } ?>><span></span>資料請求</label>
			<label class="checkbox" for="inquiry_lesson"><input id="inquiry_lesson" type="checkbox" name="inquiry" value="lesson" <?php if( ($_POST['inquiry'] == 'lesson' ) ){ echo 'checked'; } ?>><span></span>無料レッスンお申し込み</label>
			<label class="checkbox" for="inquiry_other"><input id="inquiry_other" type="checkbox" name="inquiry" value="other" <?php if( ($_POST['inquiry'] == 'other' ) ){ echo 'checked'; } ?>><span></span>その他</label>
		</div>
	</div>
	<div class="element_wrap">
		<label>氏名</label>
		<div>
			<input type="text" name="your_name" value="<?php if( !empty($_POST['your_name']) ){ echo $_POST['your_name']; } ?>" placeholder="入力してください">
			<?php if($error['your_name']){ echo '<p class="error">' . $error['your_name'] . '</p>'; } ?>
		</div>
	</div>
	<div class="element_wrap">
		<label>フリガナ</label>
		<div>
			<input type="text" name="kana_name" value="<?php if( !empty($_POST['kana_name']) ){ echo $_POST['kana_name']; } ?>" placeholder="入力してください">
			<?php if($error['kana_name']){ echo '<p class="error">' . $error['kana_name'] . '</p>'; } ?>
		</div>
	</div>
	<div class="element_wrap">
		<label>年齢</label>
		<div>
			<div class="selectbox">
				<select name="age">
					<option value="">選択してください</option>
					<option value="1" <?php if( ($_POST['age'] == '1' ) ){ echo 'selected'; } ?>>〜19歳</option>
					<option value="2" <?php if( ($_POST['age'] == '2' ) ){ echo 'selected'; } ?>>20歳〜29歳</option>
					<option value="3" <?php if( ($_POST['age'] == '3' ) ){ echo 'selected'; } ?>>30歳〜39歳</option>
					<option value="4" <?php if( ($_POST['age'] == '4' ) ){ echo 'selected'; } ?>>40歳〜49歳</option>
					<option value="5" <?php if( ($_POST['age'] == '5' ) ){ echo 'selected'; } ?>>50歳〜59歳</option>
					<option value="6" <?php if( ($_POST['age'] == '6' ) ){ echo 'selected'; } ?>>60歳〜</option>
				</select>
			</div>
		</div>
	</div>
	<div class="element_wrap">
		<label>電話番号</label>
		<div>
			<input type="text" name="tell" value="<?php if( !empty($_POST['tell']) ){ echo $_POST['tell']; } ?>" placeholder="入力してください">
			<?php if($error['tell']){ echo '<p class="error">' . $error['tell'] . '</p>'; } ?>
			<?php if($error['tell1']){ echo '<p class="error">' . $error['tell1'] . '</p>'; } ?>
		</div>
	</div>
	<div class="element_wrap">
		<label>メールアドレス</label>
		<div>
			<input type="text" name="email" value="<?php if( !empty($_POST['email']) ){ echo $_POST['email']; } ?>" placeholder="入力してください">
			<?php if($error['email']){ echo '<p class="error">' . $error['email'] . '</p>'; } ?>
			<?php if($error['email1']){ echo '<p class="error">' . $error['email1'] . '</p>'; } ?>
		</div>
	</div>
	<div class="element_wrap">
		<label>郵便番号</label>
		<div>
			<input type="text" name="zip" value="<?php if( !empty($_POST['zip']) ){ echo $_POST['zip']; } ?>">
		</div>
	</div>
	<div class="element_wrap">
		<label>住所</label>
		<div>
			<input type="text" name="address" value="<?php if( !empty($_POST['address']) ){ echo $_POST['address']; } ?>">
			<?php if($error['address']){ echo '<p class="error">' . $error['address'] . '</p>'; } ?>
		</div>
	</div>
	<div class="element_wrap">
		<label>ご希望のコース</label>
		<div>
			<label class="checkbox" for="course_level3"><input id="course_level3" type="checkbox" name="course" value="level3" <?php if( ($_POST['course'] == 'level3' ) ){ echo 'checked'; } ?>><span></span>3級コース</label>
			<label class="checkbox" for="course_basic"><input id="course_basic" type="checkbox" name="course" value="basic" <?php if( ($_POST['course'] == 'basic' ) ){ echo 'checked'; } ?>><span></span>ベーシックコース</label>
			<label class="checkbox" for="course_pro"><input id="course_pro" type="checkbox" name="course" value="pro" <?php if( ($_POST['course'] == 'pro' ) ){ echo 'checked'; } ?>><span></span>プロコース</label>
			<label class="checkbox" for="course_advance"><input id="course_advance" type="checkbox" name="course" value="advance" <?php if( ($_POST['course'] == 'advance' ) ){ echo 'checked'; } ?>><span></span>アドバンスコース</label>
			<label class="checkbox" for="course_short"><input id="course_short" type="checkbox" name="course" value="short" <?php if( ($_POST['course'] == 'short' ) ){ echo 'checked'; } ?>><span></span>短期集中検定対策コース</label>
			<label class="checkbox" for="course_self"><input id="course_self" type="checkbox" name="course" value="self" <?php if( ($_POST['course'] == 'self' ) ){ echo 'checked'; } ?>><span></span>セルフジェルネイルコース</label>
		</div>
	</div>
	
	<div class="element_wrap">
		<label>無料体験レッスンの<br class="pc">ご希望日時</label>
		<div>
			<div class="flex">
				<label>第1希望</label>
				<div class="selectbox">
					<select name="first_choice">
						<option value="">選択してください</option>
						
						<?php
							for($i = 1; $i <= 30; $i++){
								echo '<option value="'. $day[$i].'"';
								if( ($_POST['first_choice'] == $day[$i] ) ){
									echo 'selected';
								}
								echo '>'. date( 'Y年m月d日', strtotime( $day[$i])) . '(' .$week[date('w', strtotime( $day[$i] ))]. ')</option>';
							}
						?>

						
					</select>
				</div>
				<input type="text" name="first_time" value="<?php if( !empty($_POST['first_time']) ){ echo $_POST['first_time']; } ?>" placeholder="希望時間をご入力ください">
			</div>
			<div class="flex">
				<label>第2希望</label>
				<div class="selectbox">
					<select name="second_choice">
						<option value="">選択してください</option>
						<?php
							for($i = 1; $i <= 30; $i++){
								echo '<option value="'. $day[$i].'"';
								if( ($_POST['second_choice'] == $day[$i] ) ){
									echo 'selected';
								}
								echo '>'. date( 'Y年m月d日', strtotime( $day[$i])) . '(' .$week[date('w', strtotime( $day[$i] ))]. ')</option>';
							}
						?>
					</select>
					</div>
				<input type="text" name="second_time" value="<?php if( !empty($_POST['second_time']) ){ echo $_POST['second_time']; } ?>" placeholder="希望時間をご入力ください">
			</div>
			<div class="flex">
				<label>第3希望</label>
				<div class="selectbox">
					<select name="third_choice">
						<option value="">選択してください</option>
						<?php
							for($i = 1; $i <= 30; $i++){
								echo '<option value="'. $day[$i].'"';
								if( ($_POST['third_choice'] == $day[$i] ) ){
									echo 'selected';
								}
								echo '>'. date( 'Y年m月d日', strtotime( $day[$i])) . '(' .$week[date('w', strtotime( $day[$i] ))]. ')</option>';
							}
						?>
					</select>
				</div>
				<input type="text" name="third_time" value="<?php if( !empty($_POST['third_time']) ){ echo $_POST['third_time']; } ?>" placeholder="希望時間をご入力ください">
			</div>
		</div>
	</div>



	<div class="element_wrap">
		<label>その他お問い合わせ</label>
		<div>
			<textarea name="contact" rows="12" placeholder="入力してください"><?php if( !empty($_POST['contact']) ){ echo $_POST['contact']; } ?></textarea>
		</div>
	</div>
	<div class="submit_wrap">
		<div>
			<input type="submit" name="btn_confirm" value="確認する">
		</div>
	</div>
</form>

<?php endif; ?>











		</div>
	</section>








	<div id="fixed-btn" class="sp"><a href="#contact">資料請求はこちら</a></div>

	<footer class="ac">
		<img class="foot_logo" src="./img/logo.jpg">
		
		<p>〒604-8111 
		<br>京都市中京区三条通高倉東入ル桝屋町57
		<br>京都三条ビル202号 
		<br>TEL.FAX:075-255-1222
		<br>営業時間：12:00～20:00 定休日：不定休
		<br>ネイルスクールドレアWEB　<a href="https://www.dorea.jp/" target="_blank">https://www.dorea.jp/</a>
		</p>
		
		<p class="copy">Copyright © Dorea Nail School All Rights Reserved. </p>
		
	</footer>
	
	<div class="menu-wraper"></div>
	
		<img class="contact_01 pc" src="./img/parts_f1.png">
		<img class="contact_02 pc" src="./img/parts_f2.png">

</body>

</html>



