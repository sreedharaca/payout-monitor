<?php

namespace Katana\SyncBundle\Tests\RedirectUrlFinder;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Katana\SyncBundle\Lib\Curl\ClickkyJsRedirUrlFinder;


class ClickkyJsRedirUrlFinderTest extends WebTestCase
{

    public function testMetaRedirFinder()
    {

        $html = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<link type="text/css" rel="stylesheet" media="all" href="/modules/API/css/mobTransition.css">
	<link rel="stylesheet" type="text/css" href="/modules/API/css/jquery.bxslider.css" />
	<title>Mobile</title>
	<script src="/modules/Common/js/Common.js"></script>
	<script src="/modules/Common/js/jquery-1.7.1.min.js"></script>
	<script src="/modules/Common/js/jquery.json-2.3.min.js"></script>
	<script src="/modules/API/js/jquery.bxslider.min.js"></script>

	<!-- Add fancyBox -->
	<link rel="stylesheet" href="/modules/Common/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen"/>
	<script type="text/javascript" src="/modules/Common/js/jquery.fancybox-1.3.4.js"></script>

	<script type="text/javascript">
		var fullLink = "http://cli.bz/offer/3795/55119";
		var device = "iPad users only";
		var shortLink = "cli.bz/short/52eb9e82";
		var locale = "en-US";
		var fancyList1 = "Open the e-mail from your iPad";
		var fancyList2 = "Follow the link to the App Store";
		var fancyList3 = "Install the Total Domination US UK AU CA iPad (Video 3)";
		var fancyList4 = "Open it and enjoy the game";
		var checkEmail = "Please check you e-mail box!";
		var sendLink = "We have send you the link to ";
		var nextStep = "Please follow the next steps to get your bonus:";
		var noEmail = "If you do not see an e-mail from Clickky please check your Spam folder or type at your mobile browser";

		var brake = false;
		$(document).ready(function () {
			$("#sendButton").on("click", function () {
				if (!brake) {
					brake = true;
					$("#emailInput").attr("disabled", "");
					var data = {};
					data.email = $("#emailInput").val();
					data.fullLink = fullLink;
					data.device = device;
					data.locale = locale;
					data.fancyList1 = fancyList1;
					data.fancyList2 = fancyList2;
					data.fancyList3 = fancyList3;
					data.fancyList4 = fancyList4;
					data.checkEmail = checkEmail;
					data.sendLink = sendLink;
					data.nextStep = nextStep;
					data.noEmail = noEmail;
//					var instruction="";
//					instruction = $("ul").html();
//					instruction=instruction.replace("<li>"+$("ul li:nth-child(1)").html()+"</li>","");
//						var popUpInstruction=instruction.replace("<li>"+$("ul li:first-child").html()+"</li>","");
//					data.instruction=instruction.replace("<li>"+$("ul li:nth-child(2)").html()+"</li>","");

					if (isValidEmailAddress(data.email)) {
						$("#emailInput").css("color", "#666666");
						ajax("/mobileTransit/sendMail", data, function (resp) {
							if (resp.status)
								$.fancybox("<div id='fancyboxContent'>" +
												"<div id='moveRight'>" +
													"<div id='first'>"+data.checkEmail+"</div>" +
													"<div style='margin-top: 10px;color:#000;font-size: 18px;'>" + data.sendLink + "<b>" + data.email + "</b></div>" +
													"<br>" +
													"<div style='margin-top: 20px;font-size: 18px;color: #000;'>" + data.nextStep + "</div>" +
													"<br>" +
													"<ul>"+"<li>"+ data.fancyList1 + "</li>"
															+"<li>"+ data.fancyList2 + "</li>"
															+"<li>"+ data.fancyList3 + "</li>"
															+"<li>"+ data.fancyList4 + "</li>"
													+"</ul>"+
													"</div>" +
													"<br>" +
													"<div id='shortLinkInstruct'>" +
													"<img src=\"/modules/API/img/warning.gif\"/>"+
													"<div>" + data.noEmail + " \"" + shortLink + "\" </div>" +
													"</div>"+
											"</div>");
							//$("#emailInput").css("color","yellowgreen");
							else
								$("#emailInput").css("color", "tomato");

							$("#emailInput").removeAttr("disabled");
							brake = false;
						});
					}
					else {
						$("#emailInput").removeAttr("disabled");
						$("#emailInput").css("color", "tomato");
						brake = false;
					}
				}
			});
			$('.slider').bxSlider({
				slideWidth: 600,
				minSlides: 4,
				maxSlides: 4,
				moveSlides: 1
			});
		});
	</script>



	<script type="text/javascript">

		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-34695640-1']);
		_gaq.push(['_trackPageview']);
		_gaq.push(['_trackEvent', 'Offer', 'Traffic Back', 'offer_traffic_back_page']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();

	</script>


</head>
<body>
<div id="upperBackground">
	<div id="timerBlock">
		<a id="timerClose" href="javascript:void(0);"><img src="/tpl/Clickky3/img/white_cros.png"></a>
		<span id="timer"></span>
	</div>
</div>
<div id="lowerBackground"></div>

<div id="transparentBack">
	<div id="whiteBack">
			<div id="offerDesc">
				<div id="topLogoLine" class="iOSTopLine">
					<div id="free"></div>
					<span id="topLeftLogoText" class="iOSTopText">iPad users only</span>
					<img id="topRightMiniLogo" src="/modules/API/img/iOSMiniLogo.gif"/>
				</div>
				<div id="padding">
					<div id="title">Total Domination US UK AU CA iPad (Video 3)</div>
					<div id="description">The best MMO strategy game is now available for iOS. Over 30 million installs on the web – Total Domination™: Reborn is a standalone mobile game completely independent from the web version.


Civilization has fallen, and war is now the only world-order. Rise from the ashes of the wasteland, take command, form your clan and begin your quest towards Total Domination!

For the first time, the popular Total Domination™ franchise has been extended to iOS as a standalone epic adventure, featuring all new groundbreaking graphics and gameplay.
</div>
					<img id="adPreviewImage" src="http://n2.clickky.biz/storage/files/c1/u1002458/banners/total_domination_copy7.png">
				</div>
			</div>
			<div class="share">
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_default_style ">
					<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
					<a class="addthis_button_tweet"></a>
				</div>
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-521b746521bcb0b8"></script>
				<!-- AddThis Button END -->
							</div>

			<div id="instruct">
				<span id="qr_info"><b>SCAN QR CODE</b> with your device </br> to install an app on it.</span>

			</div>
			<div id="instruct_arrow"></div>
			<div class="email_block" style="border-radius: ;">
				<div id="left_block">
					<img src="https://chart.googleapis.com/chart?chs=205x205&cht=qr&chl=http://cli.bz/offer/3795/55119"/>
				</div>
				<div id="right_block">
					<div>
						<span>Don't have QR reader?</span>
						<p>Just type in your e-mail address in the form below and we will send you the direct download link to your device immediately. </p>
					</div>
					<div class="email_form">
						<input type="email" id="emailInput" placeholder="Type your e-mail address..."/>
						<div id="sendButton">SEND LINK</div>
					</div>
					<span id="qr_reader">or <a href="https://itunes.apple.com/us/app/qr-reader-for-iphone/id368494609?mt=8">get QR reader here</a><span>
				</div>
			</div>
			<div id="app_link">
				<span>Enjoy the Game!</span>
			</div>
	</div>
	<div id="other_apps">
		<span>Check out other Apps you may be interested in!</span>
		<ul class="slider">
						<li><a href="https://www.cpactions.com/offer/3282/54880"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u1002389/banners/images.jpg" /></a></li>
						<li><a href="https://www.cpactions.com/offer/3282/54664"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u1002260/banners/90-90lp.png" /></a></li>
						<li><a href="https://www.cpactions.com/offer/3282/54579"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u619/banners/jelly_splash_copy1.jpg" /></a></li>
						<li><a href="https://www.cpactions.com/offer/3282/53708"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u1001120/banners/90-90spell_copy1.png" /></a></li>
						<li><a href="https://www.cpactions.com/offer/3282/53718"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u1001769/banners/booking.png" /></a></li>
						<li><a href="https://www.cpactions.com/offer/3282/53837"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u1001611/banners/90-90_4kindoms.png" /></a></li>
						<li><a href="https://www.cpactions.com/offer/3282/54253"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u1002049/banners/90-90shape_copy1.png" /></a></li>
						<li><a href="https://www.cpactions.com/offer/3282/54269"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u1001110/banners/game_of_war_-_fire_age.jpg" /></a></li>
						<li><a href="https://www.cpactions.com/offer/3282/54424"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u1001848/banners/castle_clash_copy1.jpg" /></a></li>
						<li><a href="https://www.cpactions.com/offer/3282/54427"><img width="80" height="80" src="http://n2.clickky.biz/storage/files/c1/u1001983/banners/happy_kids.jpg" /></a></li>
					</ul>
	</div>
</div>

</body>
</html>
<script>
	var url = "https://itunes.apple.com/app/id523660889?mt=8&ls=1";
	var market = "App Store";

	$(document).ready(function(){
		var delay = 30;
		function countdown() {
			var timerID = setTimeout(countdown, 1000);
			var data = {};
			data.url = url;
			data.market = market;
			$('#timer').html("in "  + delay  + " seconds you will be redirected on the " + data.market + " or click " + "<a class='landingUrl' href='"+ data.url +"'>" + "here" + "</a>");
			delay --;
			if (delay < 0 ) {
				window.location = data.url;
				delay = 0 ;
			}
			$('#timerClose').live('click',function(){
			$('#timerBlock').slideUp('slow');
				clearTimeout (timerID);
			});
		}
		countdown();
	});
</script>
HTML;
        $finder = new ClickkyJsRedirUrlFinder();
        $finder->setHtml($html);

        $expecting_url = 'https://itunes.apple.com/app/id523660889?mt=8&ls=1';

        $this->assertTrue($expecting_url == $finder->findUrl($html), '**Error parsing url from Js **');
    }

}