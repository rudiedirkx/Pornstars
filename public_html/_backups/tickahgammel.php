<?

$tickertime = 5;

$pw = "danielx";

if ($password!=$pw) die("Password error!");

ob_start();

require "dblogon.php";

require "options.php";

if ($tickdif<$tickertime-1 && $override!="true") die("Less than $tickertime seconds since last tick!");

$fp = fopen("ticker.dat","w");
fputs($fp,time());
fclose($fp);

$time1 = time();

Function Roids_capped($pods,$croids,$mroids,$uiroids)
{
    $percent = 0.10;
    $aroids = ($croids + $mroids + $uiroids);
    if ($pods>($percent * $aroids))
    {$roids['crystal'] = floor($croids * $percent);
        $roids['metal'] = floor($mroids * $percent);
        $roids['ui'] = floor($uiroids * $percent);
        return $roids;}
    else{
        $roids['crystal'] = floor(($croids / $aroids) * $pods);
        $roids['metal'] = floor(($mroids / $aroids) * $pods);
        $roids['ui'] = floor(($uiroids / $aroids) * $pods);
        return $roids;}
}

Function dbr($txt) { global $dbr; $dbr = $dbr.$txt; }

$dbr = "";

$result = mysql_query("UPDATE ".$PA['table']." SET c_crystal=c_crystal-1 WHERE c_crystal>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET c_metal=c_metal-1 WHERE c_metal>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET c_airport=c_airport-1 WHERE c_airport>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET c_metal=c_metal-1 WHERE c_metal>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET c_abase=c_abase-1 WHERE c_abase>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET c_destfact=c_destfact-1 WHERE c_destfact>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET c_scorpfact=c_scorpfact-1 WHERE c_scorpfact>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET c_odg=c_odg-1 WHERE c_odg>1",$db); dbr(mysql_error());

$result = mysql_query("UPDATE ".$PA['table']." SET p_infinityst=p_infinityst-1 WHERE p_infinityst>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET p_warfrigst=p_warfrigst-1 WHERE p_warfrigst>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET p_wraithst=p_wraithst-1 WHERE p_wraithst>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET p_astropodst=p_astropodst-1 WHERE p_astropodst>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET p_destroyerst=p_destroyerst-1 WHERE p_destroyerst>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET p_cobrast=p_cobrast-1 WHERE p_cobrast>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET p_scorpionst=p_scorpionst-1 WHERE p_scorpionst>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET p_rcannonst=p_rcannonst-1 WHERE p_rcannonst>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET p_avengerst=p_avengerst-1 WHERE p_avengerst>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET p_lstalkerst=p_lstalkerst-1 WHERE p_lstalkerst>1",$db); dbr(mysql_error());

$result = mysql_query("UPDATE ".$PA['table']." SET r_imcrystal=r_imcrystal-1 WHERE r_imcrystal>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET r_immetal=r_immetal-1 WHERE r_immetal>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET r_aaircraft=r_aaircraft-1 WHERE r_aaircraft>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET r_tbeam=r_tbeam-1 WHERE r_tbeam>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET r_uscan=r_uscan-1 WHERE r_uscan>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET r_oscan=r_oscan-1 WHERE r_oscan>1",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET r_odg=r_odg-1 WHERE r_odg>1",$db); dbr(mysql_error());

$result = mysql_query("UPDATE ".$PA['table']." SET wareta=20,war=-1,def=0 WHERE wareta=0 AND def>0",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET wareta=30,war=-1 WHERE wareta=0 AND war>0",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET wareta=0,war=0 WHERE wareta=0 AND war<0",$db); dbr(mysql_error());
$result = mysql_query("UPDATE ".$PA['table']." SET wareta=wareta-1 WHERE wareta>0",$db); dbr(mysql_error());

$result = mysql_query("SELECT * FROM ".$PA["table"]." ORDER BY score DESC",$db); dbr(mysql_error());
$rank = 1;
while ($myrow = mysql_fetch_array($result)){
    $nick = $myrow['nick'];
    $c_crystal = $myrow['c_crystal'];
    $c_metal = $myrow['c_metal'];
    $c_airport = $myrow['c_airport'];
    $c_abase = $myrow['c_abase'];
    $c_destfact = $myrow['c_destfact'];
    $c_scorpfact = $myrow['c_scorpfact'];
    $p_infinitys = $myrow['p_infinitys'];
    $p_infinityst = $myrow['p_infinityst'];
    $p_wraiths = $myrow['p_wraiths'];
    $p_wraithst = $myrow['p_wraithst'];
    $p_warfrigs = $myrow['p_warfrigs'];
    $p_warfrigst = $myrow['p_warfrigst'];
    $p_astropods = $myrow['p_astropods'];
    $p_astropodst = $myrow['p_astropodst'];
    $p_destroyers = $myrow['p_destroyers'];
    $p_destroyerst = $myrow['p_destroyerst'];
    $p_cobras = $myrow['p_cobras'];
    $p_cobrast = $myrow['p_cobrast'];
    $p_scorpions = $myrow['p_scorpions'];
    $p_scorpionst = $myrow['p_scorpionst'];
    $p_rcannons = $myrow["p_rcannons"];
    $p_rcannonst = $myrow["p_rcannonst"];
    $p_avengers = $myrow["p_avengers"];
    $p_avengerst = $myrow["p_avengerst"];
    $p_lstalkers = $myrow["p_lstalkers"];
    $p_lstalkerst = $myrow["p_lstalkerst"];
    $lstalkers = $myrow["lstalkers"];
    $avengers = $myrow["avengers"];
    $rcannons = $myrow["rcannons"];

    $r_imcrystal = $myrow['r_imcrystal'];
    $r_immetal = $myrow['r_immetal'];
    $r_qst = $myrow['r_qst'];
    $r_iafs = $myrow['r_iafs'];
    $r_aaircraft = $myrow['r_aaircraft'];
    $r_tbeam = $myrow['r_tbeam'];
    $cobras = $myrow['cobras'];
    $infinitys = $myrow['infinitys'];
    $wraiths = $myrow['wraiths'];
    $warfrigs = $myrow['warfrigs'];
    $destroyers = $myrow['destroyers'];
    $scorpions = $myrow['scorpions'];
    $astropods = $myrow['astropods'];
    $crystal = $myrow['crystal'];
    $metal = $myrow['metal'];
    $war = $myrow['war'];
    $wareta = $myrow['wareta'];
    $def = $myrow['def'];
    $defeta = $myrow['defeta'];
    $id = $myrow['id'];
    $timer = $myrow['timer'];
    $size = $myrow['size'];
    $crystalroid = $myrow['asteroid_crystal'];
    $metalroid = $myrow['asteroid_metal'];
    $ui_roids = $myrow['ui_roids'];
    $roids = $crystalroid + $metalroid + $ui_roids;
    $i_roids = $crystalroid + $metalroid;


    if ($c_crystal==1 && $r_imcrystal!=1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET crystal=crystal+".(25+$crystalroid*25)." WHERE id=$id",$db); dbr(mysql_error());}
    if ($c_crystal==1 && $r_imcrystal==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET crystal=crystal+".(45+$crystalroid*25)." WHERE id=$id",$db); dbr(mysql_error());}

    if ($c_metal==1 && $r_immetal!=1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET metal=metal+".(15+$metalroid*25)." WHERE id=$id",$db); dbr(mysql_error());}
    if ($c_metal==1 && $r_immetal==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET metal=metal+".(33+$metalroid*25)." WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_infinityst==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_infinityst='0',p_infinitys='0',infinitys=infinitys+$p_infinitys WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_warfrigst==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_warfrigst='0',p_warfrigs='0',warfrigs=warfrigs+$p_warfrigs WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_wraithst==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_wraithst='0',p_wraiths='0',wraiths=wraiths+$p_wraiths WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_astropodst==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_astropodst='0',p_astropods='0',astropods=astropods+$p_astropods WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_destroyerst==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_destroyerst='0',p_destroyers='0',destroyers=destroyers+$p_destroyers WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_cobrast==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_cobrast='0',p_cobras='0',cobras=cobras+$p_cobras WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_scorpionst==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_scorpionst='0',p_scorpions='0',scorpions=scorpions+$p_scorpions WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_rcannonst==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_rcannonst='0',p_rcannons='0',rcannons=rcannons+$p_rcannons WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_avengerst==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_avengerst='0',p_avengers='0',avengers=avengers+$p_avengers WHERE id=$id",$db); dbr(mysql_error());}

    if ($p_lstalkerst==1) {$result2 = mysql_query("UPDATE ".$PA["table"]." SET p_lstalkerst='0',p_lstalkers='0',lstalkers=lstalkers+$p_lstalkers WHERE id=$id",$db); dbr(mysql_error());}

    $result4 = mysql_query("SELECT * FROM ".$PA["table"]." WHERE id='$id'",$db);
#echo mysql_error();
    dbr(mysql_error());
    $myrow = mysql_fetch_array($result4);
    $nick = $myrow['nick'];
    $c_crystal = $myrow['c_crystal'];
    $c_metal = $myrow['c_metal'];
    $c_airport = $myrow['c_airport'];
    $c_abase = $myrow['c_abase'];
    $r_imcrystal = $myrow['r_imcrystal'];
    $r_immetal = $myrow['r_immetal'];
    $r_qst = $myrow['r_qst'];
    $r_iafs = $myrow['r_iafs'];
    $r_aaircraft = $myrow['r_aaircraft'];
    $cobras = $myrow['cobras'];
    $infinitys = $myrow['infinitys'];
    $wraiths = $myrow['wraiths'];
    $warfrigs = $myrow['warfrigs'];
    $destroyers = $myrow['destroyers'];
    $scorpions = $myrow['scorpions'];
    $astropods = $myrow['astropods'];
    $lstalkers = $myrow["lstalkers"];
    $avengers = $myrow["avengers"];
    $rcannons = $myrow["rcannons"];
    $crystal = $myrow['crystal'];
    $metal = $myrow['metal'];
    $war = $myrow['war'];
    $wareta = $myrow['wareta'];
    $def = $myrow['def'];
    $defeta = $myrow['defeta'];
    $id = $myrow['id'];
    $timer = $myrow['timer'];
    $size = $myrow['size'];
    $crystalroid = $myrow['asteroid_crystal'];
    $metalroid = $myrow['asteroid_metal'];
    $ui_roids = $myrow['ui_roids'];
    $roids = $crystalroid + $metalroid + $ui_roids;
    $i_roids = $crystalroid + $metalroid;

    $result2 = mysql_query("SELECT * FROM ".$PA["table"]." WHERE war=$id AND wareta<5",$db);
    dbr(mysql_error());
    if (mysql_num_rows($result2)>0)
    {
        $salvagem = 0;
        $salvagec = 0;
        $d_nick = $myrow2['nick'];

# Prepare attacking fleets

        $infinitys = 0;
        $wraiths = 0;
        $warfrigs = 0;
        $destroyers = 0;
        $cobras = 0;
        $scorpions = 0;
        $astropods = 0;

        $i = 0;
        $attack = Array();
        while($myrow2=mysql_fetch_array($result2)){
            $attack[$i]["id"] = $myrow2["id"];
            $attack[$i]["infinitys"] = $myrow2["infinitys"];
            $attack[$i]["wraiths"] = $myrow2["wraiths"];
            $attack[$i]["warfrigs"] = $myrow2["warfrigs"];
            $attack[$i]["destroyers"] = $myrow2["destroyers"];
#      $attack[$i]["cobras"] = $myrow2["cobras"];
            $attack[$i]["scorpions"] = $myrow2["scorpions"];
            $attack[$i]["astropods"] = $myrow2["astropods"];
            $infinitys += $myrow2["infinitys"];
            $wraiths += $myrow2["wraiths"];
            $warfrigs += $myrow2["warfrigs"];
            $destroyers += $myrow2["destroyers"];
#      $cobras =+ $myrow2["cobras"];
            $scorpions += $myrow2["scorpions"];
            $astropods += $myrow2["astropods"];

            $i++;
        }

        $i = 0;
        while($attack[$i]["id"]>0){
            if ($infinitys>0) $attack[$i]["p_infinitys"] = $attack[$i]["infinitys"] / $infinitys; else $attack[$i]["p_infinitys"] = 1;
            if ($wraiths>0) $attack[$i]["p_wraiths"] = $attack[$i]["wraiths"] / $wraiths; else $attack[$i]["p_wraiths"] = 1;
            if ($warfrigs>0) $attack[$i]["p_warfrigs"] = $attack[$i]["warfrigs"] / $warfrigs; else $attack[$i]["p_warfrigs"] = 1;
            if ($destroyers>0) $attack[$i]["p_destroyers"] = $attack[$i]["destroyers"] / $destroyers;  else $attack[$i]["p_destroyers"] = 1;
#      $attack[$i]["p_cobras"] = $attack[$i]["cobras"] / $cobras;
            if ($scorpions>0) $attack[$i]["p_scorpions"] = $attack[$i]["scorpions"] / $scorpions; else $attack[$i]["p_scorpions"] = 1;
            if ($astropods>0) $attack[$i]["p_astropods"] = $attack[$i]["astropods"] / $astropods; else $attack[$i]["p_astropods"] = 1;

            $i++;

        }

# Prepare defending fleets

        $defend = Array();

        if ($myrow['war']==0 && $myrow['def']==0){
            $d_infinitys = $myrow['infinitys'];
            $d_wraiths = $myrow['wraiths'];
            $d_warfrigs = $myrow['warfrigs'];
            $d_destroyers = $myrow['destroyers'];
            $d_cobras = $myrow['cobras'];
            $d_scorpions = $myrow['scorpions'];
            $d_astropods = $myrow['astropods'];
            $d_rcannons = $myrow['rcannons'];
            $d_avengers = $myrow['avengers'];
            $d_lstalkers = $myrow['lstalkers'];

            $defend[0]["id"] = $myrow['id'];
            $defend[0]["infinitys"] = $myrow['infinitys'];
            $defend[0]["wraiths"] = $myrow['wraiths'];
            $defend[0]["warfrigs"] = $myrow['warfrigs'];
            $defend[0]["destroyers"] = $myrow['destroyers'];
            $defend[0]["cobras"] = $myrow['cobras'];
            $defend[0]["scorpions"] = $myrow['scorpions'];
            $defend[0]["astropods"] = $myrow['astropods'];
            $defend[0]["sum"] = $myrow['infinitys'] + $myrow['wraiths'] + $myrow['warfrigs'] + $myrow['destroyers'] + $myrow['cobras'] + $myrow['scorpions'] + $myrow['astropods'];
            $d_sum = $defend[0]["sum"];
        } else {
            $d_infinitys = 0;
            $d_wraiths = 0;
            $d_warfrigs = 0;
            $d_destroyers = 0;
            $d_cobras = 0;
            $d_scorpions = 0;
            $d_astropods = 0;
            $d_rcannons = $myrow['rcannons'];
            $d_avengers = $myrow['avengers'];
            $d_lstalkers = $myrow['lstalkers'];

            $defend[0]["id"] = $myrow['id'];
            $defend[0]["infinitys"] = 0;
            $defend[0]["wraiths"] = 0;
            $defend[0]["warfrigs"] = 0;
            $defend[0]["destroyers"] = 0;
            $defend[0]["cobras"] = 0;
            $defend[0]["scorpions"] = 0;
            $defend[0]["astropods"] = 0;
            $defend[0]["sum"] = 0;
            $d_sum = 0;
        }

        $i = 1;
        $result2 = mysql_query("SELECT * FROM ".$PA["table"]." WHERE def=$id AND wareta<=10",$db);
        while($myrow2=mysql_fetch_array($result2)){
            $defend[$i]["id"] = $myrow2["id"];
            $defend[$i]["infinitys"] = $myrow2["infinitys"];
            $defend[$i]["wraiths"] = $myrow2["wraiths"];
            $defend[$i]["warfrigs"] = $myrow2["warfrigs"];
            $defend[$i]["destroyers"] = $myrow2["destroyers"];
            $defend[$i]["cobras"] = $myrow2["cobras"];
            $defend[$i]["scorpions"] = $myrow2["scorpions"];
            $defend[$i]["astropods"] = $myrow2["astropods"];
            $defend[$i]["sum"] = $myrow['infinitys'] + $myrow['wraiths'] + $myrow['warfrigs'] + $myrow['destroyers'] + $myrow['cobras'] + $myrow['scorpions'] + $myrow['astropods'];
            $d_sum += $defend[$i]["sum"];

            $d_infinitys += $myrow2["infinitys"];
            $d_wraiths += $myrow2["wraiths"];
            $d_warfrigs += $myrow2["warfrigs"];
            $d_destroyers += $myrow2["destroyers"];
            $d_cobras += $myrow2["cobras"];
            $d_scorpions += $myrow2["scorpions"];
            $d_astropods += $myrow2["astropods"];
            $i++;
        }

        $i = 0;
        while($defend[$i]["id"]>0){
            if ($d_infinitys>0) $defend[$i]["p_infinitys"] = $defend[$i]["infinitys"] / $d_infinitys; else $defend[$i]["p_infinitys"] = 1;
            if ($d_wraiths>0) $defend[$i]["p_wraiths"] = $defend[$i]["wraiths"] / $d_wraiths; else $defend[$i]["p_wraiths"] = 1;
            if ($d_warfrigs>0) $defend[$i]["p_warfrigs"] = $defend[$i]["warfrigs"] / $d_warfrigs; else $defend[$i]["p_warfrigs"] = 1;
            if ($d_destroyers>0) $defend[$i]["p_destroyers"] = $defend[$i]["destroyers"] / $d_destroyers; else $defend[$i]["p_destroyers"] = 1;
            if ($d_cobras>0) $defend[$i]["p_cobras"] = $defend[$i]["cobras"] / $d_cobras; else $defend[$i]["p_cobras"] = 1;
            if ($d_scorpions>0) $defend[$i]["p_scorpions"] = $defend[$i]["scorpions"] / $d_scorpions; else $defend[$i]["p_scorpions"] = 1;
            if ($d_astropods>0) $defend[$i]["p_astropods"] = $defend[$i]["astropods"] / $d_astropods; else $defend[$i]["p_astropods"] = 1;

            if ($d_sum>0) $defend[$i]["p_sum"] = $defend[$i]["sum"] / $d_sum; else $defend[$i]["p_sum"] = 1;
            $i++;
        }

        $d_crystalroid = $myrow['asteroid_crystal'];
        $d_metalroid = $myrow['asteroid_metal'];
        $d_ui_roids = $myrow['ui_roids'];
        $d_crystalroid2 = $myrow['asteroid_crystal'];
        $d_metalroid2 = $myrow['asteroid_metal'];
        $d_ui_roids2 = $myrow['ui_roids'];



        $infinitys2 = $infinitys;
        $wraiths2 = $wraiths;
        $warfrigs2 = $warfrigs;
        $destroyers2 = $destroyers;
        $astropods2 = $astropods;
        $cobras2 = $cobras;
        $scorpions2 = $scorpions;


        $d_infinitys2 = $d_infinitys;
        $d_wraiths2 = $d_wraiths;
        $d_warfrigs2 = $d_warfrigs;
        $d_destroyers2 = $d_destroyers;
        $d_astropods2 = $d_astropods;
        $d_cobras2 = $d_cobras;
        $d_scorpions2 = $d_scorpions;
        $d_rcannons2 = $d_rcannons;
        $d_avengers2 = $d_avengers;
        $d_lstalkers2 = $d_lstalkers;

# Ship statistics, $stats['inf']['wra'] = 0.01; sets the fire
# power of infinitys to 0.01 against wraiths.

        $stats['inf']['inf'] = 0.025 / 1.2;
        $stats['inf']['wra'] = 0.01 / 1.2;
        $stats['inf']['war'] = 0.015 / 1.2;
        $stats['inf']['ast'] = 0.015 / 1.2;
        $stats['inf']['des'] = 0.0025 / 1.2;
        $stats['inf']['sco'] = 0.0035 / 1.2;
        $stats['inf']['cob'] = 0.02 / 1.2;

        $stats['wra']['inf'] = 0.15 / 1.2;
        $stats['wra']['wra'] = 0.125 / 1.2;
        $stats['wra']['war'] = 0.025 / 1.2;
        $stats['wra']['ast'] = 0.015 / 1.2;
        $stats['wra']['des'] = 0.037 / 1.2;
        $stats['wra']['sco'] = 0.025 / 1.2;
        $stats['wra']['cob'] = 0.07 / 1.2;
        $stats['wra']['lst'] = 0.07 / 1.2;

        $stats['war']['inf'] = 1.5 / 1.2;
        $stats['war']['wra'] = 0.4 / 1.2;
        $stats['war']['war'] = 0.05 / 1.2;
        $stats['war']['ast'] = 0.055 / 1.2;
        $stats['war']['des'] = 0.025 / 1.2;
        $stats['war']['sco'] = 0.025 / 1.2;
        $stats['war']['cob'] = 0.015 / 1.2;
        $stats['war']['rca'] = 0.055 / 1.2;

        $stats['des']['inf'] = 0.8 / 1.2;
        $stats['des']['wra'] = 0.21 / 1.2;
        $stats['des']['war'] = 0.3 / 1.2;
        $stats['des']['ast'] = 0.11 / 1.2;
        $stats['des']['des'] = 0.05 / 1.2;
        $stats['des']['sco'] = 0.04 / 1.2;
        $stats['des']['cob'] = 0.25 / 1.2;
        $stats['des']['ave'] = 0.55 / 1.2;

        $stats['sco']['inf'] = 1.00 / 1.2;
        $stats['sco']['wra'] = 0.35 / 1.2;
        $stats['sco']['war'] = 0.125 / 1.2;
        $stats['sco']['ast'] = 0.135 / 1.2;
        $stats['sco']['des'] = 0.18 / 1.2;
        $stats['sco']['sco'] = 0.05 / 1.2;
        $stats['sco']['cob'] = 1.00 / 1.2;

        $stats['rca']['inf'] = 0.08;
        $stats['rca']['ast'] = 0.1;


        $stats['ave']['wra'] = 0.1;
        $stats['ave']['war'] = 0.07;

        $stats['lst']['des'] = 0.08;
        $stats['lst']['sco'] = 0.1;

        attack('infinitys',$d_rcannons   * $stats['rca']['inf']);
        attack('astropods',$d_rcannons   * $stats['rca']['ast']);

        attack('wraiths',$d_avengers     * $stats['ave']['wra']);
        attack('warfrigs',$d_avengers    * $stats['ave']['war']);

        attack('destroyers',$d_lstalkers * $stats['lst']['des']);
        attack('scorpions',$d_lstalkers  * $stats['lst']['sco']);

        attack('infinitys',$d_infinitys  * $stats['inf']['inf']);
        attack('wraiths',$d_infinitys    * $stats['inf']['wra']);
        attack('warfrigs',$d_infinitys   * $stats['inf']['war']);
        attack('astropods',$d_infinitys  * $stats['inf']['ast']);
        attack('destroyers',$d_infinitys * $stats['inf']['des']);
        attack('scorpions',$d_infinitys  * $stats['inf']['sco']);
#      attack('cobras',$d_infinitys     * 0.00);

        attack('infinitys',$d_wraiths    * $stats['wra']['inf']);
        attack('wraiths',$d_wraiths      * $stats['wra']['wra']);
        attack('warfrigs',$d_wraiths     * $stats['wra']['war']);
        attack('astropods',$d_wraiths    * $stats['wra']['ast']);
        attack('destroyers',$d_wraiths   * $stats['wra']['des']);
        attack('scorpions',$d_wraiths    * $stats['wra']['sco']);
#      attack('cobras',$d_wraiths       * 0.00);

        attack('infinitys',$d_warfrigs   * $stats['war']['inf']);
        attack('wraiths',$d_warfrigs     * $stats['war']['wra']);
        attack('warfrigs',$d_warfrigs    * $stats['war']['war']);
        attack('astropods',$d_warfrigs   * $stats['war']['ast']);
        attack('destroyers',$d_warfrigs  * $stats['war']['des']);
        attack('scorpions',$d_warfrigs   * $stats['war']['sco']);
#      attack('cobras',$d_warfrigs      * 0.00);

        attack('infinitys',$d_destroyers * $stats['des']['inf']);
        attack('wraiths',$d_destroyers   * $stats['des']['wra']);
        attack('warfrigs',$d_destroyers  * $stats['des']['war']);
        attack('astropods',$d_destroyers * $stats['des']['ast']);
        attack('destroyers',$d_destroyers* $stats['des']['des']);
        attack('scorpions',$d_destroyers * $stats['des']['sco']);
#      attack('cobras',$d_destroyers    * 0.00);

        attack('infinitys',$d_scorpions  * $stats['sco']['inf']);
        attack('wraiths',$d_scorpions    * $stats['sco']['wra']);
        attack('warfrigs',$d_scorpions   * $stats['sco']['war']);
        attack('astropods',$d_scorpions  * $stats['sco']['ast']);
        attack('destroyers',$d_scorpions * $stats['sco']['des']);
        attack('scorpions',$d_scorpions  * $stats['sco']['sco']);
#      attack('cobras',$d_scorpions     * 0.00);

# ---------------------------------------------

        attack('d_infinitys',$infinitys2 * $stats['inf']['inf']);
        attack('d_wraiths',$infinitys2   * $stats['inf']['wra']);
        attack('d_warfrigs',$infinitys2  * $stats['inf']['war']);
        attack('d_astropods',$infinitys2 * $stats['inf']['ast']);
        attack('d_destroyers',$infinitys2* $stats['inf']['des']);
        attack('d_scorpions',$infinitys2 * $stats['inf']['sco']);
        attack('d_cobras',$infinitys2    * $stats['inf']['cob']);

        attack('d_infinitys',$wraiths2   * $stats['wra']['inf']);
        attack('d_wraiths',$wraiths2     * $stats['wra']['wra']);
        attack('d_warfrigs',$wraiths2    * $stats['wra']['war']);
        attack('d_astropods',$wraiths2   * $stats['wra']['ast']);
        attack('d_destroyers',$wraiths2  * $stats['wra']['inf']);
        attack('d_scorpions',$wraiths2   * $stats['wra']['sco']);
        attack('d_cobras',$wraiths2      * $stats['wra']['cob']);
        attack('d_lstalkers',$wraiths2   * $stats['wra']['lst']);

        attack('d_infinitys',$warfrigs2  * $stats['war']['inf']);
        attack('d_wraiths',$warfrigs2    * $stats['war']['wra']);
        attack('d_warfrigs',$warfrigs2   * $stats['war']['war']);
        attack('d_astropods',$warfrigs2  * $stats['war']['ast']);
        attack('d_destroyers',$warfrigs2 * $stats['war']['des']);
        attack('d_scorpions',$warfrigs2  * $stats['war']['sco']);
        attack('d_cobras',$warfrigs2     * $stats['war']['cob']);
        attack('d_rcannons',$warfrigs2   * $stats['war']['rca']);

        attack('d_infinitys',$destroyers2* $stats['des']['inf']);
        attack('d_wraiths',$destroyers2  * $stats['des']['wra']);
        attack('d_warfrigs',$destroyers2 * $stats['des']['war']);
        attack('d_astropods',$destroyers2* $stats['des']['ast']);
        attack('d_destroyers',$destroyers2*$stats['des']['des']);
        attack('d_scorpions',$destroyers2* $stats['des']['sco']);
        attack('d_cobras',$destroyers2   * $stats['des']['cob']);
        attack('d_avengers',$destroyers2 * $stats['des']['ave']);

        attack('d_infinitys',$scorpions2 * $stats['sco']['inf']);
        attack('d_wraiths',$scorpions2   * $stats['sco']['wra']);
        attack('d_warfrigs',$scorpions2  * $stats['sco']['war']);
        attack('d_astropods',$scorpions2 * $stats['sco']['ast']);
        attack('d_destroyers',$scorpions2* $stats['sco']['des']);
        attack('d_scorpions',$scorpions2 * $stats['sco']['sco']);
        attack('d_cobras',$scorpions2    * $stats['sco']['cob']);

        $salvagec = floor($salvagec);
        $salvagem = floor($salvagem);

        //Roid capturing

        $t_block = 0;

        $i = 0;
        while($attack[$i]["id"]>0)
        {
            if ($d_cobras>=$attack[$i]["astropods"]) $block = $attack[$i]["astropods"];
            else $block = $d_cobras;
            $t_block += $block;

            $roidsc = Roids_capped($attack[$i]["astropods"]-$block,$d_crystalroid,$d_metalroid,$d_ui_roids);

            $roidscapped = $roidsc["crystal"] + $roidsc["metal"] + $roidsc["ui"];
            $result3 = mysql_query("UPDATE ".$PA["table"]." SET asteroid_metal=asteroid_metal+".$roidsc["metal"].",asteroid_crystal=asteroid_crystal+".$roidsc["crystal"].",ui_roids=ui_roids+".$roidsc["ui"]." WHERE id=".$attack[$i]["id"],$db);  dbr(mysql_error());
            $result3 = mysql_query("UPDATE ".$PA["table"]." SET asteroid_metal=asteroid_metal-".$roidsc["metal"].",asteroid_crystal=asteroid_crystal-".$roidsc["crystal"].",ui_roids=ui_roids-".$roidsc["ui"]." WHERE id=$id",$db);  dbr(mysql_error());

            $l_infinitys = round(round($infinitys2-$infinitys) * $attack[$i]["p_infinitys"]);
            $l_wraiths = round(round($wraiths2-$wraiths) * $attack[$i]["p_wraiths"]);
            $l_warfrigs = round(round($warfrigs2-$warfrigs) * $attack[$i]["p_warfrigs"]);
            $l_destroyers = round(round($destroyers2-$destroyers) * $attack[$i]["p_destroyers"]);
            $l_astropods = round(round($astropods2-$astropods) * $attack[$i]["p_astropods"]);
            if ($attack[$i]["astropods"]-$l_astropods-$roidscapped<0) $l_astropods = $attack[$i]["astropods"]; else $l_astropods = $l_astropods+$roidscapped;
            $l_scorpions = round(round($scorpions2-$scorpions) * $attack[$i]["p_scorpions"]);

            add_news("Combat report","<table border=\"1\" bordercolor=\"black\"><td>Defender(s) ($nick #$id):</td><td>Total:</td><td>Lost:</td><tr>\n
<td>Infinitys:</td><td>$d_infinitys2</td><td>".(number_format($d_infinitys2-$d_infinitys,0))."<tr>\n
<td>Wraiths:</td><td>$d_wraiths2</td><td>".(number_format($d_wraiths2-$d_wraiths,0))."<tr>\n
<td>Warfrigs:</td><td>$d_warfrigs2</td><td>".(number_format($d_warfrigs2-$d_warfrigs,0))."<tr>\n
<td>Destroyer:</td><td>$d_destroyers2</td><td>".(number_format($d_destroyers2-$d_destroyers,0))."<tr>\n
<td>Cobras:</td><td>$d_cobras2</td><td>".(number_format($d_cobras2-$d_cobras,0))."<tr>\n
<td>Astropods:</td><td>$d_astropods2</td><td>".(number_format($d_astropods2-$d_astropods,0))."<tr>\n
<td>Scorpions:</td><td>$d_scorpions2</td><td>".(number_format($d_scorpions2-$d_scorpions,0))."<tr>\n
<td>Reaper cannons:</td><td>$d_rcannons2</td><td>".(number_format($d_rcannons2-$d_rcannons,0))."<tr>\n
<td>Avengers:</td><td>$d_avengers2</td><td>".(number_format($d_avengers2-$d_avengers,0))."<tr>\n
<td>Lucius stalkers:</td><td>$d_lstalkers2</td><td>".(number_format($d_lstalkers2-$d_lstalkers,0))."<tr>\n
<td>Attacker(s):</td><tr>\n
<td>Infinitys:</td><td>$infinitys2</td><td>".(number_format($infinitys2-$infinitys,0))."<tr>\n
<td>Wraiths:</td><td>$wraiths2</td><td>".(number_format($wraiths2-$wraiths,0))."<tr>\n
<td>Warfrigs:</td><td>$warfrigs2</td><td>".(number_format($warfrigs2-$warfrigs,0))."<tr>\n
<td>Destroyers:</td><td>$destroyers2</td><td>".(number_format($destroyers2-$destroyers,0))."<tr>\n
<td>Astropods:</td><td>$astropods2 ($block blocked)</td><td>".(number_format($astropods2-$astropods,0))."<tr>\n
<td>Scorpions:</td><td>$scorpions2</td><td>".(number_format($scorpions2-$scorpions,0))."<tr>\n
<td>Yours:</td><tr>\n
<td>Infinitys:</td><td>".$attack[$i]["infinitys"]."</td><td>$l_infinitys<tr>\n
<td>Wraiths:</td><td>".$attack[$i]["wraiths"]."</td><td>$l_wraiths<tr>\n
<td>Warfrigs:</td><td>".$attack[$i]["warfrigs"]."</td><td>$l_warfrigs<tr>\n
<td>Destroyers:</td><td>".$attack[$i]["destroyers"]."</td><td>$l_destroyers<tr>\n
<td>Astropods:</td><td>".$attack[$i]["astropods"]." ($block blocked)</td><td>$l_astropods<tr>\n
<td>Scorpions:</td><td>".$attack[$i]["scorpions"]."</td><td>$l_scorpions<tr>\n
<tr>
<td>Asteroid captures:</td><tr>
<td>Metal:</td><td>".$roidsc["metal"]."/$d_metalroid</td><tr>
<td>Crystal:</td><td>".$roidsc["crystal"]."/$d_crystalroid</td><tr>
<td>Resource:</td><td>".$roidsc["ui"]."/$d_ui_roids</td><tr>
</table>\n
",$attack[$i]["id"]);

            $result3 = mysql_query("UPDATE ".$PA["table"]." SET infinitys=infinitys-$l_infinitys,wraiths=wraiths-$l_wraiths,warfrigs=warfrigs-$l_warfrigs,destroyers=destroyers-$l_destroyers,astropods=astropods-$l_astropods,scorpions=scorpions-$l_scorpions WHERE id=".$attack[$i]["id"],$db);  dbr(mysql_error());
            $i++;

            $d_crystalroid -= $roidsc["crystal"];
            $d_metalroid -= $roidsc["metal"];
            $d_ui_roids -= $roidsc["ui"];
        }

        /*
           Primary defender calcs
           ----------------------
           */
        $i = 0;

        $l_infinitys = round(round($d_infinitys2-$d_infinitys) * $defend[$i]["p_infinitys"]);
        $l_wraiths = round(round($d_wraiths2-$d_wraiths) * $defend[$i]["p_wraiths"]);
        $l_warfrigs = round(round($d_warfrigs2-$d_warfrigs) * $defend[$i]["p_warfrigs"]);
        $l_destroyers = round(round($d_destroyers2-$d_destroyers) * $defend[$i]["p_destroyers"]);
        $l_astropods = round(round($d_astropods2-$d_astropods) * $defend[$i]["p_astropods"]);
#  if ($attack[$i]["astropods"]-$l_astropods-$roidscapped<0) $l_astropods = 0; else $l_astropods = $l_astropods+$roidscapped;
        $l_scorpions = round(round($d_scorpions2-$d_scorpions) * $defend[$i]["p_scorpions"]);
        $l_cobras = round(round($d_cobras2-$d_cobras) * $defend[$i]["p_cobras"]);
        $l_rcannons = round($d_rcannons2-$d_rcannons);
        $l_avengers = round($d_avengers2-$d_avengers);
        $l_lstalkers = round($d_lstalkers2-$d_lstalkers);
        $salvm = round($salvagem * $defend[$i]["p_sum"]);
        $salvc = round($salvagec * $defend[$i]["p_sum"]);

        add_news("Combat report","<table border=\"1\" bordercolor=\"black\"><td>Defender(s) ($nick #$id):</td><td>Total:</td><td>Lost:</td><tr>\n
<td>Infinitys:</td><td>$d_infinitys2</td><td>".(number_format($d_infinitys2-$d_infinitys,0))."<tr>\n
<td>Wraiths:</td><td>$d_wraiths2</td><td>".(number_format($d_wraiths2-$d_wraiths,0))."<tr>\n
<td>Warfrigs:</td><td>$d_warfrigs2</td><td>".(number_format($d_warfrigs2-$d_warfrigs,0))."<tr>\n
<td>Destroyer:</td><td>$d_destroyers2</td><td>".(number_format($d_destroyers2-$d_destroyers,0))."<tr>\n
<td>Cobras:</td><td>$d_cobras2</td><td>".(number_format($d_cobras2-$d_cobras,0))."<tr>\n
<td>Astropods:</td><td>$d_astropods2</td><td>".(number_format($d_astropods2-$d_astropods,0))."<tr>\n
<td>Scorpions:</td><td>$d_scorpions2</td><td>".(number_format($d_scorpions2-$d_scorpions,0))."<tr>\n
<td>Attacker(s):</td><tr>\n
<td>Infinitys:</td><td>$infinitys2</td><td>".(number_format($infinitys2-$infinitys,0))."<tr>\n
<td>Wraiths:</td><td>$wraiths2</td><td>".(number_format($wraiths2-$wraiths,0))."<tr>\n
<td>Warfrigs:</td><td>$warfrigs2</td><td>".(number_format($warfrigs2-$warfrigs,0))."<tr>\n
<td>Destroyers:</td><td>$destroyers2</td><td>".(number_format($destroyers2-$destroyers,0))."<tr>\n
<td>Astropods:</td><td>$astropods2 ($t_block blocked)</td><td>".(number_format($astropods2-$astropods,0))."<tr>\n
<td>Scorpions:</td><td>$scorpions2</td><td>".(number_format($scorpions2-$scorpions,0))."<tr>\n
<td>Yours:</td><tr>\n
<td>Infinitys:</td><td>".$defend[$i]["infinitys"]."</td><td>$l_infinitys<tr>\n
<td>Wraiths:</td><td>".$defend[$i]["wraiths"]."</td><td>$l_wraiths<tr>\n
<td>Warfrigs:</td><td>".$defend[$i]["warfrigs"]."</td><td>$l_warfrigs<tr>\n
<td>Destroyers:</td><td>".$defend[$i]["destroyers"]."</td><td>$l_destroyers<tr>\n
<td>Cobras:</td><td>".$defend[$i]["cobras"]."</td><td>$l_cobras<tr>\n
<td>Astropods:</td><td>".$defend[$i]["astropods"]."</td><td>$l_astropods<tr>\n
<td>Scorpions:</td><td>".$defend[$i]["scorpions"]."</td><td>$l_scorpions<tr>\n
<td>Reaper cannons:</td><td>$d_rcannons2</td><td>".(number_format($d_rcannons2-$d_rcannons,0))."<tr>\n
<td>Avengers:</td><td>$d_avengers2</td><td>".(number_format($d_avengers2-$d_avengers,0))."<tr>\n
<td>Lucius stalkers:</td><td>$d_lstalkers2</td><td>".(number_format($d_lstalkers2-$d_lstalkers,0))."<tr>\n
<tr>
<td>Metal salvage:</td><td>$salvm</td><tr>
<td>Crystal salvage:</td><td>$salvc</td><tr>
<td>Asteroid loss:</td><tr>
<td>Metal:</td><td>".($d_metalroid2-$d_metalroid)."/$d_metalroid</td><tr>
<td>Crystal:</td><td>".($d_crystalroid2-$d_crystalroid)."/$d_crystalroid</td><tr>
<td>Resource:</td><td>".($d_ui_roids2-$d_ui_roids)."/$d_ui_roids</td><tr>
</table>\n
",$defend[$i]["id"]);

        $result3 = mysql_query("UPDATE ".$PA["table"]." SET infinitys=infinitys-$l_infinitys,wraiths=wraiths-$l_wraiths,warfrigs=warfrigs-$l_warfrigs,destroyers=destroyers-$l_destroyers,astropods=astropods-$l_astropods,scorpions=scorpions-$l_scorpions,cobras=cobras-$l_cobras,rcannons=rcannons-$l_rcannons,avengers=avengers-$l_avengers,lstalkers=lstalkers-$l_lstalkers,crystal=crystal+$salvc,metal=metal+$salvm WHERE id=".$defend[$i]["id"],$db);  dbr(mysql_error());
        $i++;


        /*
           END PRIMARY DEFENDER CALCS
           --------------------------
           */
        while($defend[$i]["id"]>0)
        {
            $l_infinitys = round(round($d_infinitys2-$d_infinitys) * $defend[$i]["p_infinitys"]);
            $l_wraiths = round(round($d_wraiths2-$d_wraiths) * $defend[$i]["p_wraiths"]);
            $l_warfrigs = round(round($d_warfrigs2-$d_warfrigs) * $defend[$i]["p_warfrigs"]);
            $l_destroyers = round(round($d_destroyers2-$d_destroyers) * $defend[$i]["p_destroyers"]);
            $l_astropods = round(round($d_astropods2-$d_astropods) * $defend[$i]["p_astropods"]);
#  if ($attack[$i]["astropods"]-$l_astropods-$roidscapped<0) $l_astropods = 0; else $l_astropods = $l_astropods+$roidscapped;
            $l_scorpions = round(round($d_scorpions2-$d_scorpions) * $defend[$i]["p_scorpions"]);
            $l_cobras = round(round($d_cobras2-$d_cobras) * $defend[$i]["p_cobras"]);
            $salvm = round($salvagem * $defend[$i]["p_sum"]);
            $salvc = round($salvagec * $defend[$i]["p_sum"]);

            add_news("Combat report","<table border=\"1\" bordercolor=\"black\"><td>Defender(s) ($nick #$id):</td><td>Total:</td><td>Lost:</td><tr>\n
<td>Infinitys:</td><td>$d_infinitys2</td><td>".(number_format($d_infinitys2-$d_infinitys,0))."<tr>\n
<td>Wraiths:</td><td>$d_wraiths2</td><td>".(number_format($d_wraiths2-$d_wraiths,0))."<tr>\n
<td>Warfrigs:</td><td>$d_warfrigs2</td><td>".(number_format($d_warfrigs2-$d_warfrigs,0))."<tr>\n
<td>Destroyer:</td><td>$d_destroyers2</td><td>".(number_format($d_destroyers2-$d_destroyers,0))."<tr>\n
<td>Cobras:</td><td>$d_cobras2</td><td>".(number_format($d_cobras2-$d_cobras,0))."<tr>\n
<td>Astropods:</td><td>$d_astropods2</td><td>".(number_format($d_astropods2-$d_astropods,0))."<tr>\n
<td>Scorpions:</td><td>$d_scorpions2</td><td>".(number_format($d_scorpions2-$d_scorpions,0))."<tr>\n
<td>Reaper cannons:</td><td>$d_rcannons2</td><td>".(number_format($d_rcannons2-$d_rcannons,0))."<tr>\n
<td>Avengers:</td><td>$d_avengers2</td><td>".(number_format($d_avengers2-$d_avengers,0))."<tr>\n
<td>Lucius stalkers:</td><td>$d_lstalkers2</td><td>".(number_format($d_lstalkers2-$d_lstalkers,0))."<tr>\n
<td>Attacker(s):</td><tr>\n
<td>Infinitys:</td><td>$infinitys2</td><td>".(number_format($infinitys2-$infinitys,0))."<tr>\n
<td>Wraiths:</td><td>$wraiths2</td><td>".(number_format($wraiths2-$wraiths,0))."<tr>\n
<td>Warfrigs:</td><td>$warfrigs2</td><td>".(number_format($warfrigs2-$warfrigs,0))."<tr>\n
<td>Destroyers:</td><td>$destroyers2</td><td>".(number_format($destroyers2-$destroyers,0))."<tr>\n
<td>Astropods:</td><td>$astropods2 ($t_block blocked)</td><td>".(number_format($astropods2-$astropods,0))."<tr>\n
<td>Scorpions:</td><td>$scorpions2</td><td>".(number_format($scorpions2-$scorpions,0))."<tr>\n
<td>Yours:</td><tr>\n
<td>Infinitys:</td><td>".$defend[$i]["infinitys"]."</td><td>$l_infinitys<tr>\n
<td>Wraiths:</td><td>".$defend[$i]["wraiths"]."</td><td>$l_wraiths<tr>\n
<td>Warfrigs:</td><td>".$defend[$i]["warfrigs"]."</td><td>$l_warfrigs<tr>\n
<td>Destroyers:</td><td>".$defend[$i]["destroyers"]."</td><td>$l_destroyers<tr>\n
<td>Cobras:</td><td>".$defend[$i]["cobras"]."</td><td>$l_cobras<tr>\n
<td>Astropods:</td><td>".$defend[$i]["astropods"]."</td><td>$l_astropods<tr>\n
<td>Scorpions:</td><td>".$defend[$i]["scorpions"]."</td><td>$l_scorpions<tr>\n
<tr>
<td>Metal salvage:</td><td>$salvm</td><tr>
<td>Crystal salvage:</td><td>$salvc</td><tr>
</table>\n
",$defend[$i]["id"]);

            $result3 = mysql_query("UPDATE ".$PA["table"]." SET infinitys=infinitys-$l_infinitys,wraiths=wraiths-$l_wraiths,warfrigs=warfrigs-$l_warfrigs,destroyers=destroyers-$l_destroyers,astropods=astropods-$l_astropods,scorpions=scorpions-$l_scorpions,cobras=cobras-$l_cobras,crystal=crystal+$salvc,metal=metal+$salvm WHERE id=".$defend[$i]["id"],$db);  dbr(mysql_error());
            $i++;
        }


    }


    $result2 = mysql_query("UPDATE ".$PA["table"]." SET size='$roids',rank=$rank WHERE id=$id",$db); dbr(mysql_error());
    $rank++;
}

$result3 = mysql_query("UPDATE ".$PA["table"]." SET score=(crystal+metal)/200+size*1500+infinitys*200+wraiths*2000+warfrigs*3000+astropods*1600+cobras*3000+destroyers*5000+scorpions*7000+rcannons*800+avengers*1400+lstalkers*6000");

?>
Tick done!
<?
if ($os=="windows") echo "<meta HTTP-EQUIV=\"Refresh\" content=\"$tickertime\">";

Logging("ticker","Ticker done in ".(time()-$time1)." seconds. MySQL Report: $dbr\nOB: ".ob_get_contents());
#Tror dette funker
mysql_close("earthdoom");
mysql_close($db);
mysql_close();


#
# Uncomment this to enable FTP uploading of complete universe
#

#include("cuniverse.php");

?>
