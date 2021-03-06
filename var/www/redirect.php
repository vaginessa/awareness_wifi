<?php
include 'settings.php';
$iptables="sudo /sbin/iptables";
//Client MAC
	if (!isset($_POST["mac"])) {exit;}
$client_mac = strtoupper(base64_decode($_POST["mac"]));

if (isset($_POST["logout"]))
{
	shell_exec("$iptables -t mangle -D $portal_chain -m mac --mac-source $client_mac -j RETURN");
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://10.0.0.1/portal.php");
}

if (isset($_POST["fire"]))
{

	/////////////
	//enable client on firewall
	/////////////

	//check if mac is allowed already 
	$add_firewall_exception=True;
	$testmac=shell_exec("$iptables -t mangle -L $portal_chain | grep $client_mac");
	//echo "Testmac: ".$testmac;
	if (strlen($testmac) > 0) $add_firewall_exception=False; //iptables Entry already exists

	//add firewall exception
	if ($add_firewall_exception == True)
	{
		shell_exec("$iptables -t mangle -I $portal_chain -m mac --mac-source $client_mac -j RETURN");
	}


	//decode URL for redirection (nice - no https :-) )
	$target_url = base64_decode($_POST["b64redirurl"]);
	//echo $target_url;
	//echo strlen($target_url);
	
	if (strlen($target_url) > 0)
	{
		$target_url="http://".$target_url;
	}
	else
	{
		$target_url=$default_redirect_target;
	}

	//echo $target_url;

	header("HTTP/1.1 301 Moved Permanently");
	header("Location: $target_url");


}

?>

