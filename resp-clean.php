<?php 
function processRespToJson($out) {
	$final = [];
	$begin = '<H3>License Information *</H3>';
	$end = '<div class ="note">';
	$end2 = '<div class="note">';
	$line_delim = '<BR>';
	$invalid = 'check your input';
	if(strpos($out, $invalid)!== FALSE) {
		echo 'No match found' . PHP_EOL;
	}
	else {
		echo 'Writing record' . PHP_EOL;
	
	$final['errorcode'] = 0;
	$final['status'] = 'OK';

	if (strpos($out, $begin)!== FALSE) {
		$out = strstr($out, $begin);
		$out = cleanme($out);
		if(strpos($out, $end)!== FALSE) {
			$out =strstr($out, $end, TRUE); 
		}
		if(strpos($out, $end2)!== FALSE) {
			$out =strstr($out, $end2, TRUE); 
		}
		if(stripos($out, '<BR>')!== FALSE) {
			$lines = explode('<br>', $out);
			foreach($lines as $line) {
				$line = cleanme($line);
				if(strpos($line, ':')!==FALSE) {
					$dataline = explode(':', trim($line));
					$attr = trim(strip_tags(cleanme($dataline[0])));
					if (!empty($attr)) {
						$v = trim(strip_tags(cleanme($dataline[1])));
						if(is_date($attr, $v)) {
							$v = date("Y-m-d\TH:i:s\Z", strtotime($v));
						}
						
						else {

						}
						$final[$attr] = $v; 
					}
				}
				else {
					$v =trim(cleanme(strip_tags($line)));
					if (is_licenseinfo(cleanme($v))){
						$attr = 'date of license information';
						$testv = trim(str_replace('license information * ','', cleanme($v)));
						if(is_date('date', $testv)) {
							$v = date("Y-m-d\TH:i:s\Z", strtotime($testv));
						}
						$final[$attr] = $v;
					}
					else {
						$attr = 'unknown_attr_' . uniqid(); 
						$v =trim(cleanme(strip_tags($line)));
						if(!empty($v)) {
							$final[$attr] = $v;
						}
					}

				}
			}
		} 
	
		return $final;
	} 
	}
}




function cleanme($in) {
	$pattern = '/\s\s+/';
	$in = preg_replace($pattern, ' ', $in);
	return trim(str_replace("degree date", '',
			strtolower(

				str_replace("\r\n", '', 
					str_replace('&nbsp;', '', 
						trim($in)
					)
				)
			))
		);
}

/** 
assume date if "date" is found in $k, and $v matches a date format 
*/
function is_date($k, $v) {
	$validatedate = validateDate($v);
	return strpos($k, 'date')!== FALSE && $validatedate;
}


function is_licenseinfo($v) {
	return strpos($v, 'license information')!==FALSE;
}
function getTestLicenseNum() {
	return rand(100000,999999);
}
function validateDate($date, $format = 'm/d/Y')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}
/* 
curl -k -d "profcd=60&plicno=123" \
-H "Content-Type: application/x-www-form-urlencoded" \
-X POST http://www.nysed.gov/COMS/OP001/OPSCR2 \
-H "Connection: keep-alive" \
-H "Cache-Control: max-age=0" \
-H "Origin: http://www.op.nysed.gov" \
-H "Upgrade-Insecure-Requests: 1"



POST /COMS/OP001/OPSCR2 HTTP/1.1
> Host: www.nysed.gov
> User-Agent: curl/7.64.1
> Accept: *
> Content-Type: application/x-www-form-urlencoded
> Connection: keep-alive
> Cache-Control: max-age=0
> Origin: http://www.op.nysed.gov
> Upgrade-Insecure-Requests: 1
> Content-Length: 20


*/

$ch = curl_init(); //"http://www.nysed.gov/COMS/OP001/OPSCR2");
$fp = fopen( __DIR__ . "/example_homepage.txt", "w+");
//$fp2 = fopen( __DIR__ . "/example_errors.txt", "a+");
curl_setopt($ch, CURLOPT_URL,"http://www.nysed.gov/COMS/OP001/OPSCR2");



curl_setopt($ch, CURLOPT_TCP_NODELAY, 1);
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$headers = [
	"Content-Type: application/x-www-form-urlencoded",
	"Host: www.nysed.gov",
	"Connection: keep-alive",
	"Content-Length: 20",
	"Accept: */*",
	"Cache-Control: max-age=0",
	"Origin: http://www.op.nysed.gov",
	"Upgrade-Insecure-Requests: 1",
	//"Referer: http://www.example.com/index.php", //Your referrer address
	"User-Agent: curl/7.64.1",
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$w = [];
for ($j = 0; $j < 100; $j++) {
	$w[] = doWrite($fp, $ch);
}

$json = json_encode($w, JSON_PRETTY_PRINT);
fwrite($fp, $json);
curl_close($ch);

fclose($fp);



function doWrite($fp, $ch) {
	$licensenum = getTestLicenseNum();
	$postdata = [
		'profcd'=>60,
		'plicno'=>$licensenum
	];
	//die(var_dump($postdata));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));  //Post Fields

	$resp = curl_exec($ch);
	$final = [];
	$json = processRespToJson($resp, $final);
	
	if(curl_error($ch)) {
	    $s = curl_error($ch);
	    return [$s];
	}
	else {
		if (!empty($json)) { 
			if($json!='null') {
				return $json;
			}
		}
		else {
			return [
				"errorcode" => 1,
				"status" => "No match.",
				"date of license information"=> "",
				"name"=> "",
				"address"=> "",
				"profession"=> "",
				"license no"=> $licensenum,
				"date of licensure"=> "",
				"additional qualification"=> "",
				"registered through last day of"=> "",
				"medical school"=> ""
			];
		}
	}
}