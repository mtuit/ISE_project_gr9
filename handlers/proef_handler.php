<?php 
	session_start();
	include '../includes/database_functions.php';

	db_open();
	$proef_id = $_SESSION['proef'];
	if (isset($_GET['value'])) {

		if($_GET['value'] == 'opslaan') {
			
			/* Check if researchName doesn't already exist */
			$sql = "SELECT PROEF_NAAM FROM PROEF";
			$result = db_query($sql);
			$isValid = True;
			while ($row = db_fetchAssoc($result)) {
				if ($row['PROEF_NAAM'] == $_POST['researchName']) {
					$isValid = False;
					header('Location: ../index.php?m=5#nieuw_proef');
					unset($_SESSION['vars']);
				} 
			}

			/* if researchName is valid */
			if ($isValid) {
				/* Insert proef gegegevens in de database */
				$PROEF_NAAM = $_POST['researchName'];
				$sql = "INSERT INTO PROEF ([PROEF_NAAM]) VALUES ('{$PROEF_NAAM}')";
				db_query($sql);

				/* Select proef_id van de proef*/
				/*$stmt = db_query("SELECT PROEF_ID FROM PROEF WHERE PROEF_NAAM = '$PROEF_NAAM'");
				$result = db_fetchAssoc($stmt);
				$proef_id = $result['PROEF_ID'];*/

				/* Insert proef bij onderzoek in de database */
				$onderzoek_id = $_SESSION['onderzoek'];
				$sql = "INSERT INTO PROEFVOORONDERZOEK ([ONDERZOEK_ID], [PROEF_ID]) VALUES ('{$onderzoek_id}', '{$proef_id}')";
				db_query($sql);

				/* Insert velden in de database */
				foreach ($_SESSION['vars'] as $var) {
					$vars = explode('||', $var);

					$veld_naam = $vars[0];

					$datatypenaam = $vars[1];
					$stmt = db_query("SELECT DATATYPE_ID FROM DATATYPES WHERE DATATYPE_NAAM = '$datatypenaam'");
					sqlsrv_fetch($stmt);
					$datatype_id = sqlsrv_get_field($stmt, 0);

					$sql = "INSERT INTO VELD ([DATATYPE_ID], [PROEF_ID], [VELD_NAAM]) VALUES ('{$datatype_id}', '{$proef_id}', '{$veld_naam}')";
					db_query($sql);
				}

				unset($_SESSION['vars']);
				header('Location: ../index.php?#nieuw_proef');
			}

		} else if ($_GET['value'] == 'opslaanBeheerProef') {
			echo 'Nieuwe Velden: ';
			var_dump($_SESSION['proefbeheer_vars']);
			echo '</br>';

			db_open();
			$bestaandeVelden = array();
			$sql = "SELECT VELD_NAAM, DATATYPE_NAAM FROM VELD V JOIN DATATYPES D ON V.DATATYPE_ID =  D.DATATYPE_ID WHERE PROEF_ID = $proef_id";
			$stmt = db_query($sql);
			while ($result = db_fetchAssoc($stmt)) {
				array_push($bestaandeVelden, implode('||', $result));
			}
			echo 'Bestaande Velden: ';
			var_dump($bestaandeVelden);
			echo '</br>';
			echo 'Verwijderde Velden: ';
			$arrayDiff = array_diff($bestaandeVelden, $_SESSION['proefbeheer_vars']);
			$_SESSION['arrayDiff'] = $arrayDiff;
			var_dump($_SESSION['arrayDiff']);

			if (empty($arrayDiff)) {
				echo 'ArrayDiff is empty';
				// Sla op
			} else {
				//header('Location: ../index.php?m=6#beheer_proef');
			}
		} else if ($_GET['value'] == 'opslaanBeheerProefDefinitief') {
			// Verwijder kolommen in WAARDE tabel
			foreach($_SESSION['arrayDiff'] as $var) {
				$vars = explode('||', $var);
				$sql = "BEGIN TRAN DELETE FROM WAARDE WHERE VELD_ID IN (SELECT VELD_ID
														 FROM VELD 
														 WHERE PROEF_ID = $proef_id AND VELD_NAAM = '$vars[0]') ROLLBACK TRAN";
				var_dump($sql);
				$stmt = db_query($sql);
			}

			// Delete kolommen in de VELD tabel
			foreach($_SESSION['arrayDiff'] as $var) {
				$vars = explode('||', $var);
				$sql = "BEGIN TRAN DELETE FROM VELD WHERE PROEF_ID = $proef_id AND VELD_NAAM = '$vars[0]' ROLLBACK TRAN";
				var_dump($sql);
				$stmt = db_query($sql);
			}

			// Voeg kolommen toe die nieuw zijn 
			// TODO 
		}
	} else {
		echo 'GET not set!'; 
	}

	db_close();	
?>