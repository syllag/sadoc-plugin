<?php
	require_once ("SADOC/sadocUtils.php");   // librairie de fonction sur sadoc 
	
	function cherche_ligne_dans_bd($codeCompetence)
	{
		try
		{	
			$PARAM_hote = 'localhost';
			$PARAM_nom_bd = 'SADOCCO';
			$PARAM_utilisateur = 'root';
			$PARAM_mot_passe = 'Nordine1$';
						
			$connexion = new PDO('mysql:host='.$PARAM_hote.';dbname='.$PARAM_nom_bd, $PARAM_utilisateur, $PARAM_mot_passe); //;port='.$PARAM_port.';

		$resultats=$connexion->query("SELECT * FROM COMPETENCE where CODECOMPETENCE='".$codeCompetence."'");
			$resultats->setFetchMode(PDO::FETCH_OBJ); // on dit qu'on veut que le résultat soit récupérable sous forme d'objet
			
			
			$ligne = $resultats->fetch() ;			
			$resultats->closeCursor(); // on ferme le curseur des résultats
		}
		catch(Exception $e)
		{
				echo 'Une erreur est survenue dans cherche_ligne_dans_bd !';
				die();
		}
		return $ligne ;
	}
	
	function creer_competence($codeCompetence)
	{
		$comp = cherche_ligne_dans_bd($codeCompetence);
		
		$name = substr($codeCompetence, 0, -2);
						
		$competence->id=$comp->ID;
		$competence->name=$name;
		$competence->description=$comp->DESCRIPTION;		
		$competence->acronym="C2I:2012-02-22:".str_replace(".",":",$name);	

		return $competence ;
	}
	

	function gere_bouton_export($activite_id,$ref_referentiel,$userid,$competences_activite,$context_id,$id_referentiel,$date_creation,$local)
	{		
		// On désactive la mise en cache du wsdl (pour le test)
		ini_set('soap.wsdl_cache_enabled', 0);
			
		
		// AFFICHER LA LISTE DES DOCUMENTS
		$records_document = referentiel_get_documents($activite_id);
		
		if($records_document)
		{	
			$dirname = './SADOC/PDF/';
			
			foreach ($records_document as $record_d) // PARCOUR DES DOCUMENT À SIGNER
			{
				$dir = opendir($dirname);
				$url_document = $record_d->url_document;

				$trouver = "false";
				
				/* PERMET UNE UNICITÉ DES NOMS DE FICHIERs, CE QUI PERMET DE LES STOCKERS SUR LE SERVEUR SANS RISQUE D'ÉCRASEMENT DE FICHIERs*/
				$url_tmp = str_replace("/","_",$url_document);
				$nom_fichier = referentiel_get_user_nom($userid)."_".referentiel_get_user_prenom($userid)."_".$url_tmp.".pdf";
				
				/* cherche l'existance du fichier sur le serveur */
				while($file = readdir($dir))
				{					
					if($file != '.' && $file != '..' && !is_dir($dirname.$file))
					{
						if($file==$nom_fichier)
						{
							//echo "trouver sur serveur";
							$trouver="true";
							echo '<br /><a href="'.$dirname.$file.'">'.'<img src="SADOC/IMG/SADoc.jpeg"  title="Exporter">'.'</a>'.'<br />';								
						}
					}
				}
				closedir($dir);


				if($trouver=="false") /* ON RENTRE ICI UNIQUEMENT SI LE FICHIER N'EXISTE PAS SUR LE SERVEUR */
				{
					//echo "Fichier non trouver donc il auras création de celui-ci";
					$document_id=$record_d->id;
					if (isset($record_d->etiquette_document))
					{
						$fileName=$record_d->etiquette_document;
					}
					else
					{
						$fileName='';
					}					
					
					/* il me faut la fin de url du document car elle contient le vrai nom du fichier à aller cherché dans $file = .... */
					$tmp = explode("/",$url_document);
					$fs = get_file_storage();				
					$file = $fs->get_file($context_id, 'mod_referentiel', 'document',$record_d->id, "/",$tmp[count($tmp)-1]);
					$contents = $file->get_content();

					try
					{
						/*  CONNECTION AU WEB SERVICE SADoc */
						$ws = new SoapClient('http://sadoc-ws.univ-artois.fr:8080/sadocWeb/services/sadoc.wsdl',array('trace' => 1));
						

						/*        LE OWNER RETOURNER VIA LE WEB SERVICE SADoc         */
						$user->firstName = referentiel_get_user_nom($userid);    // nom d'utilisateur
						$user->lastName = referentiel_get_user_prenom($userid);  //prenom de l'utilisateur
						$user->mail = referentiel_get_user_mail($userid);        //@ mail de l'utilisateur
						$user->id=$userid;
						$owner = $ws->createOwner($user);
						
						
						/*       COMPETENCE(S) VALIDÉE(S)         */
						
						$tableauDeCompetences = explode("/", $competences_activite);
						$array_res;
						for($i=0;$i<count($tableauDeCompetences)-1;$i++)
						{			
							$comp = creer_competence($tableauDeCompetences[$i]);
							$arrray_res[$i] = $comp ;
						}
		
		/*echo "<br>.....";
		foreach ($arrray_res as $comp){
			echo "<br>";
			print_r($comp);
			echo "<br>";
		}
		echo ".....<br>";	*/		
						
						
						
						/*$competence1 = creer_competence('C2I:2012-02-22:D1:1');		
						$competence2 = creer_competence('C2I:2012-02-22:D2:2');						
						$competence3 = creer_competence('C2I:2012-02-22:D3:3');												
						$competence4 = creer_competence('C2I:2012-02-22:D4:4');						
						$competence5 = creer_competence('C2I:2012-02-22:D5:5');*/
						
						/*        SIGNATURE DOCUMENT VIA SADoc         */
						$ret2=$ws->signDocument(array(
												"doc"=>$contents,
												"name"=>$fileName,
												"owner"=>$user,
												"competence"=>$array_res)
												);		
		/* "competence"=>array($competence1,$competence2,$competence3,$competence4,$competence5) */										
												
						/*     SAUVEGARDE SUR SERVEUR DU FICHIER PDF    */
						$fp = fopen($dirname.$nom_fichier, 'w');
						fwrite($fp,$ret2->doc);
						
						
						/*  CRÉATION DU BOUTON D'EXPORTATION  */
						$adresse = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
echo "<form Method=\"POST\" >
		<center>
			<a href=\"".$dirname.$nom_fichier."\"><INPUT type='image'src='SADOC/IMG/SADoc.jpeg' onClick='window.location.replace(".$adresse.")'  title='Exporter'>
			</a>
		</center>
	  </form>" ;

					}catch (Exception $e)
					{
						echo "<br><font color='RED'><b>ERREUR WEB_SERVICE</b></font><br>";
						//echo '<br>Exception ',$e->getCode(),' reçue : ',  $e->getMessage(), "\n";
						//printSoapEnv($ws);
					}
				}
			}
		}
	}	
?>
