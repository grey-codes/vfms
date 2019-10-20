<?php
include('shared.php');
if (!logged_in()) {
    die("Not logged in!");
}

class Result {
    public $method="default";
    public $result="";
    public $result_num=0;
    public $time=0;
}

function addDecimalStrings($s1,$s2) {   
	$s1=str_pad($s1,strlen($s2),"0",STR_PAD_LEFT);
    $s2=str_pad($s2,strlen($s1),"0",STR_PAD_LEFT);
    //split
    $s1ar=str_split($s1);
    $s2ar=str_split($s2);
    //initial setup
	$length = strlen($s1);
    $carry = 0;
    $baseOrd=ord('0');
    $result="";

	for ($i = $length-1 ; $i >= 0 ; $i--) 
	{ 
		$c1 = ord($s1ar[$i]) - $baseOrd; 
        $c2 = ord($s2ar[$i]) - $baseOrd; 
        //sum
        $cres=($c1+$c2+$carry)%10; 
        //concat
        $result = $cres . $result; 
        //calculate carry
		$carry = intdiv($c1+$c2+$carry,10); 
	} 
	$result=$carry.$result;
    return ltrim($result,"0");
}

function multDecimalStrings($s1,$s2) {
	$s1=str_pad($s1,strlen($s2),"0",STR_PAD_LEFT);
    $s2=str_pad($s2,strlen($s1),"0",STR_PAD_LEFT);
    //split
    $s1ar=str_split($s1);
    $s2ar=str_split($s2);
    //initial setup
	$length = strlen($s1);
	//intcache
	$res="";
    set_error_handler(function ($severity, $message, $file, $line) {
        throw new \ErrorException($message, $severity, $severity, $file, $line);
    });
	for ($i=0;$i<$length;$i++) {
		$res=$res."0";
		$d1=$s1ar[$i];
		$rowRes="";
		$carry=0;
		for ($j=$length-1;$j>=0;$j--) {
            $d2=$s2ar[$j];
            $dmul=0;
            try {
            $dmul=$d1*$d2+$carry;
            } catch(Exception $e) {
                echo("$d1|$d2|$carry<br/>");
            }
			$rowRes=($dmul%10).$rowRes;
			$carry=intdiv($dmul,10);
		}
		$rowRes=ltrim($carry.$rowRes,"0");
		$res=addDecimalStrings($res,$rowRes);
	}
    restore_error_handler();
    return ltrim($res,"0");
}

function subDecimalStrings($s1,$s2) {
	$s1=str_pad($s1,strlen($s2),"0",STR_PAD_LEFT);
    $s2=str_pad($s2,strlen($s1),"0",STR_PAD_LEFT);
    //split
    $s1ar=str_split($s1);
    $s2ar=str_split($s2);
    //initial setup
    $length = strlen($s1);
    $borrow=0;
    $res="";
	for ($i=$length-1;$i>=0;$i--) {
        $d1=$s1ar[$i];
        $d2=$s2ar[$i];
        $dr=$d1-$d2-$borrow;
        $borrow=0;
        while ($dr<0) {
            $borrow++;
            $dr+=10;
        }
        $res=$dr.$res;
    }
    return ltrim($res,"0");
}

function powStrings($s1,$s2) {
    $catch=0;
    $res="1";
    while (((int)$s2>0) && $catch<100000) {
        $res=multDecimalStrings($res,$s1);
        $s2=subDecimalStrings($s2,"1");
        $catch++;
    }
    return ltrim($res,"0");
}

function karatsubaMultStrings($s1,$s2)
{
    //echo("$s1*$s2:<br/>");
    //escape case: one of the numbers is single-digit
    $m = min(strlen($s1), strlen($s2));
    if ($m < 2) {
        return multDecimalStrings($s1, $s2);
    }

    //pad strings to equal length
	$s1=str_pad($s1,strlen($s2),"0",STR_PAD_LEFT);
    $s2=str_pad($s2,strlen($s1),"0",STR_PAD_LEFT);

    //calculate split position
    $m = min(strlen($s1), strlen($s2));
	$s1=str_pad($s1,ceil($m/2)*2,"0",STR_PAD_LEFT);
    $s2=str_pad($s2,ceil($m/2)*2,"0",STR_PAD_LEFT);
    $m2 = ceil($m/2);

    //split strings in half
    $high1=substr($s1,0,$m2);
    $low1=substr($s1,$m2);
    $high2=substr($s2,0,$m2);
    $low2=substr($s2,$m2);

    //recursive karatsuba calls
    $z0 = karatsubaMultStrings($low1, $low2);
    $z1 = karatsubaMultStrings(addDecimalStrings($low1, $high1), addDecimalStrings($low2, $high2));
    $z2 = karatsubaMultStrings($high1, $high2);

    //pre-calculate exponents
	$exp1=str_pad("1",$m2*2+1,"0",STR_PAD_RIGHT);
	$exp2=str_pad("1",$m2+1,"0",STR_PAD_RIGHT);
    //$exp1=(string)pow(10,$m2*2);//powStrings("10",(string)($m2 * 2));
    //$exp2=(string)pow(10,$m2);//powStrings("10",(string)($m2));

    //use arbitrary-length string operations to calculate result

    return addDecimalStrings(multDecimalStrings($z2, $exp1),addDecimalStrings(multDecimalStrings(subDecimalStrings(subDecimalStrings($z1,$z2),$z0), $exp2), $z0));
}

function pruneUnsignedString($s) {
    $var = str_replace(["+","-"],"",filter_var($s, FILTER_SANITIZE_NUMBER_INT ));
    return (strlen($var)>0) ? $var : "0";
}

function multPost() {
    if ( ! empty( $_POST ) ) {
        if ( isset( $_POST['s1'] ) && isset( $_POST['s2'] )) {
            $s1 = pruneUnsignedString($_POST['s1']);
            $s2 = pruneUnsignedString($_POST['s2']);
            $s1i=(int)$s1;
            $s2i=(int)$s2;
            $method = "default";
            if (isset( $_POST['method'] ) ) {
                $method=strtolower($_POST['method']);
            } else {
                if (($s1i)>=PHP_INT_MAX || ($s2i)>=PHP_INT_MAX) {
                    $method="karatsuba";
                }
            }
            $r=new Result();
            $r->method=$method;
            $start_time=microtime(true);
            switch ($method) {
                case "karatsuba":
                    $r->result=karatsubaMultStrings($s1,$s2);
                    $r->result_num=(double)($r->result);
                    break;
                case "long":
                    $r->result=multDecimalStrings($s1,$s2);
                    $r->result_num=(double)($r->result);
                    break;
                default:
                    $r->result_num=$s1i*$s2i;
                    $r->result=(string)($r->result);
                    break;
            }
            $end_time=microtime(true);
            $r->time=$end_time-$start_time;
            $r_json=json_encode($r);
            die($r_json);
        }
    }
}

multPost();

$userID = $_SESSION['userid'];
$username = $_SESSION['username'];
$sessUser = getUserByID($userID);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Virtual File Management System</title>
        <link rel="stylesheet" href="index.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/099a1cb3f3.js" crossorigin="anonymous"></script>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
    <body>
    <div class="outerContainer">
    <div class="container">
        <div class="header-title">
			<h1>Virtual File Management System</h1>
        </div>
        <div class="header-inner">
            <span id="back">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span id="text">Logged in as <?php echo($username) ?><a href="logout.php">Logout</a></span>    
            <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
            <script>
                $("#back").click(function() {
                    window.location.href = "index.php";
                })
            </script>
        </div>
        <div class="centerContent">
            <form action="" method="post" id="calcForm" name="calcForm" >
                <input type="text" name="s1" id="s1" placeholder="Number 1" />
                <input type="text" name="s2" id="s2" placeholder="Number 2" />
                <select name="method" id="method">
                    <option value="default">Standard</option>
                    <option value="long">Long Multiplication</option>
                    <option value="karatsuba" selected="true">Karatsuba</option>
                </select>
                <input type="submit" name="sub" id="sub" value="Calculate" />
                <script>
                    $("#calcForm").submit(function(evt){	
                        evt.preventDefault();
                        var formData = new FormData($(this)[0]);
                        $.ajax({
                            url: 'calculator.php',
                            type: 'POST',
                            data: formData,
                            async: true,
                            cache: false,
                            contentType: false,
                            processData: false,
                            dataType: "json",
                            success: function (response) {
                                alert("Result: " + response.result + "\nCalculated in " + response.time + " seconds.");
                            }
                        });
                    });
                </script>
            </form>
        </div>
        <div class="footer">
            <p>Virtual File Management System designed by CYBERGANG 2019</p>
        </div>
    </div>
    </body>
</html>