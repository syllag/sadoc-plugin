<?php
	require("SadocServicesService.php");

	function parseEnv($xmlstr) {  
		$xml = simplexml_load_string($xmlstr);
		$xmlchilds=$xml->children('SOAP-ENV',true);
		$obj=$xmlchilds[1]->children();
		
		return (array) $obj[0]; 
	}

	function getOrCreateOwner($ws,$firstName,$lastName,$mail) {
		$ow=new createOwnerRequest();
		
		$ow->firstName=$firstName;
		$ow->lastName=$lastName;
		$ow->mail=$mail;
		
		$ws->createOwner($ow);

		$res=parseEnv($ws->__getLastResponse());
		
		return $res;
	}

	function signDocument($ws,$doc,$filename,$owner,$competence) { // renvoie une chaine base64
		$sdr = new signDocumentRequest();
		
		$sdr->doc=$doc;
		$sdr->name=$filename;
		$sdr->owner=$owner;
		$sdr->competence=$competence;

		$docSigned = $ws->signDocument($sdr);

		$res = $docSigned;

		//printSoapEnv($ws);exit();
		
		return $res;
	}
	
	function createCompetence($id, $name, $description, $acronym, $creationDate)
	{
		$comp = new competence();
		
		$comp->id = $id;
		$comp->name = $name;
		$comp->description = $description;
		$comp->acronym = $acronym;
		$comp->creationDate = $creationDate;
		
		return $comp;
	}


	function printSoapEnv($sc) {
		echo "\n****** Last Request ******\n";
		echo $sc->__getLastRequest (),"\n";
		
		echo "\n****** Last Response ******\n";
		echo $sc->__getLastResponse (),"\n";

	}

?>
