<?php
session_start();

require "mod_cookies.php";
$cookiemaking = make_cookies();

if(isset($_SESSION['flashformid'])){
	$flashformid=$_SESSION['flashformid'];
	global $NEW_SESSION;
	$NEW_SESSION = FALSE;
}else{
	global $NEW_SESSION;
	$NEW_SESSION = TRUE;
	$flashformid=rand(1, 10000);	
	$_SESSION['flashformid'] = $flashformid;
}

$months = array(1 => "januar", "februar", "mars", "april", "mai", "juni", "juli", "august", "september", "oktober", "november", "desember");	

$logtype = array("article" => 0, "comment" => 1, "flashforum" => 2, "user" => 3);
$eventdesc = array("editarticle" => "editarticle", "newarticle" => "newarticle", 
			"deletearticle" => "deletearticle", "newflash" => "newflash", "deleteflash" => "deleteflash",
			"newuser" => "newuser", "newcomment" => "newcomment", "delcomment" => "delcomment",
			"newdraft" => "newdraft");

$DEBUG = 0;
$anyone_comments = FALSE;
$magic_number = "ostepop";
$sitetitle = "Gutta fra Calcutta";
$siteslogan = "Smarte folk skriver smarte ting om mer eller mindre smarte tema";
$unknown_author = "ikke registrert bruker";
$no_articles_text = "Forsiden har for øyeblikket ingen artikler. Kontroller språkinnstillingene nede til høyre. ";
$article_author = "Forfatter";
$date = "Dato";
$time = "Tidspunkt";

$chars_showing_first_article = 1500;
$chars_showing_articles = 300;
$max_image_size = 1000000;
$max_profile_image_size = 250000;

$netpbmpath = "/usr/local/bin/";
$pic_max_height = 600;
$pic_max_width = 400;

$default_modules_left = array("module_recent_comments", "module_birthday", "module_poll", "module_listusers", "module_recentarticles", "module_archive");
//$default_m_c = "module_articles_frontpage";
global $default_m_c;
$default_m_c = "articlesFrontpage";
$default_module_right = "module_flashforum";
$default_page_title = "Gutta fra Calcutta";
$default_module_right_2 ="mod_pick_style";

$missing_function = "The content you attempted to reach is unavailable.";
$missing_function = "we fucked up. we are truly sorry. we grovel deeply and lick your boots.";
$missing_function = "Funksjonen er ikke tilgjengelig. Kontakt redpilot@online.no hvis du fulgte en link.";

$approved_functions = array("module_add_article",
	"module_articles_frontpage",
	"m_va",
	"module_recent_comments",
	"module_flashforum",
	"module_register_form",
	"module_register_user",
	"module_user_admin",
	"module_article_search",
	"module_delete_article", 
	"module_edit_profile",
	"mvp",
	"mcg", // Om Calcuttagutta
	"mod_pick_style",
	"module_listusers",
	"module_cancel_article", 
	"module_archive",
	"module_files",
	"module_admin",
	"module_admininput",
	"module_polladmin",
	"module_poll",
	"module_oldpolls",
	"module_memberlist",
	"module_birthday",
	"module_recentarticles",
	"mfa", // module flash archive
	"module_menu",
	"module_categoryadmin",
	"enterArticleGUI",
	"addArticleGUI",
	"addArticle",
	"daoCreateArticle",
	"listSearchesGUI",
	"monthSearchResultGUI",
	"textSearchResultGUI",
	"listCommentsSearchResultGUI",
	"articlesFrontpage",
	"va", // View article
	"editArticle",
	"deleteArticle",
	"showSettingsGUI"
	);

$href_edit_profile = 'index.php?m_c=module_edit_profile&amp;page_title=User+aprofile';

// ABOUT US
$about_text = 'Vi er en gjeng med gutter og jenter som skriver om alt mellom himmel og jord, anført av sjefsskribent Tor. Teknologien tar Are og Anders seg av; Calcuttagutta hviler på kilovis med PHP-spaghettikode og en SQL-database.<br/><br/>Vi har en butikk på cafepress.com: <a href="http://www.cafepress.com/calcuttagutta">http://www.cafepress.com/calcuttagutta</a>';

// MODULE GET ADDRESSES
$menu_profile_edit = '<a href="index.php?m_c=module_edit_profile&amp;page_title=User+aprofile">Min profil</a>';
$menu_index = '<a href="index.php">Forsiden</a>';
$menu_enter_article = '<a href="index.php?m_c=enterArticleGUI&amp;page_title=Add+article">Legg inn artikkel</a>';
$menu_article_search = '<a href="index.php?m_c=module_article_search&amp;page_title=Article+search">Søk</a>';
$menu_register_user = '<a href="index.php?m_c=module_register_form&amp;page_title=Register<+new+user">Registrer deg</a>';
$menu_admin = '<a href="index.php?m_c=module_admin&amp;page_title=Admin">Admin</a>';
$menu_about = '<a href="index.php?m_c=mcg">Om oss</a>';
 //<a href="index.php?=&m_c=module_user_admin&page_title=User+administration">User admin</a> | <a href="index.php">Index</a> | <a href="index.php?=&m_c=module_register_form&page_title=Register+new+user">Register new user</a> | <a href="index.php?=&m_c=module_enter_article&page_title=Add+article">Enter new article</a> | <a href="index.php?=&m_c=module_article_search&page_title=Article+search">Search for articles</a>

// LOGINBUTTON, JAVASCRIPT
$login_js = "<span id='loginlink'><a href=\"javascript:showDiv('loginform', 'errorandlogout')\">Logg inn</a></span>";
// LOGOUTLINK
$logout_link = '<a href="index.php?logmeout=logout&amp;logout=Logg+ut">Logg ut</a>';

// NEW ARTICLE CREATION PAGE
//$enterArticleGUI = '<a href="index.php?m_c=enterArticleGUI">Ny artikkel (beta)</a>';

// New search page
$listSearchesGUI= '<a href="index.php?m_c=listSearchesGUI">Søk (beta)</a>';

// MENUS
$adminmenu = $menu_index . " | " . $menu_about . " | " . $menu_enter_article . " | " . $menu_article_search . " | " . $menu_profile_edit . " | "  . $menu_admin .  " | " .$logout_link . " | "  . $listSearchesGUI; 
$usermenu = $menu_index . " | " . $menu_about . " | " . $menu_enter_article . " | " . $menu_article_search . " | " . $menu_profile_edit . " | " . $logout_link . " | " . $listSearchesGUI;

// A user without posting rights
$cusermenu = $menu_index . " | " . $menu_about . " | " . $menu_article_search . " | " . $menu_profile_edit . " | " . $logout_link . " | " . $listSearchesGUI;

// Menu you see when not logged in
$freemenu = $menu_index . " | " . $menu_about . " | " . $menu_register_user . " | "  . $menu_article_search . " | " . $login_js;


//LANGUAGE
$lang = "no";


$jokes = array("For en god del år siden sto en striledame midt i trikkesporet på Engen i Bergen og så seg tvilrådig rundt. Til slutt praiet hun en tjuagutt og spurte: Du - kan du sei meg kor eg får trikken til Møhlenpris henne?
Tjuagutten så på henne og svarte at: Hvis du står dær du står en stund til - så får du 'an midt i ræven!",
"Vegane i Sogn og Fjordane har alltid vore berykta. Smale og svingete kronglar de seg fram langs fjellsidene. På ein slik veg møttes to store bussar. Sjåførane prøvde så godt dei kunne å komma seg forbi kvarandre, men vegen var smal - Det gjekk så langt at dei fekk framendane på bussane opp på sida av kvarandre, slik at vindaugo til sjåførene stod rett overfor kvarandre. Då opna dei rutene begge to og gliste, og den eine nikka og sa:
- Jasso, du! Du e' ute og kjøyre, kar!",
"I Bergen gikk en kjuagutt og skjøv en håndkjerre oppover gaten - midt i trikkesporet. Bak kom trikken, og føreren ringte iltert med klokken for å få gutten ut av sporet, så han kunne komme frem. Men kjuagutten reagerte overhode ikke. Etter endel ilter bjelling, lente trikkeføreren seg ut og brølte:
- Hei du! Kan du ikje komme deg ut av spporet - ?
Kjuagutten snudde seg: - Jo, EG kan, men DU kan ikje!",
"Q: Why did the tachyon cross the road?<br/>
A: Because it was on the other side.<br/>",
"Canadianism: You have two cows. Vous avez deux vaches.<br/>
Communism–Reality: You have two cows. Technically, everyone owns all the cows and everyone is equal. If you happen to be in charge of everyone and their cows, you own more of the cows than everyone else because you are more equal than they are.<br/>
Democracy: You have two cows. They outvote you 2-1 to ban all meat and dairy products.<br/>
Dictatorship: You have two cows. The government takes both and shoots you.<br/>
Dyslexia: You have two woks.<br/>
Political Correctness: You are associated with (the concept of 'ownership' is a symbol of the phallocentric, war-mongering, intolerant past) two differently aged (but no less valuable to society) bovines of non-specified gender.<br/>
Totalitarianism: You have two cows. The government takes them and denies they ever existed. Milk is banned.<br/>",
"A man goes to an exotic tropical island for a vacation. As the boat nears the island, he notices the constant sound of drumming coming from the island. As he gets off the boat, he asks the first native he sees how long the drumming will go on. The native casts about nervously and says 'very bad when the drumming stops.'. At the end of the day, the drumming is still going and is starting to get on his nerves. So, he asks another native when the drumming will stop. The native looks as if he's just been reminded of something very unpleasant. 'Very bad when the drumming stops,' he says, and hurries off. After a couple of days with little sleep, our traveller is finally fed up, grabs the nearest native, slams him up against a tree, and shouts 'What happens when the drumming stops?!!'. 'Bass solo.' he replies.");


?>