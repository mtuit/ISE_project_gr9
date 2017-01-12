<?php 
  include '../includes/database_functions.php'; 
  session_start();
?>
<div class="container">
<div class="row">
<div class="column large-12">
  <h3>Nieuw onderzoek</h3>
</div>
<div class="column large-12">
  <form action="handlers/research_handler.php?a=n" name="newResearch" method="POST" enctype="multipart">
    <label for="researchName">Naam</label>
    <input type="text" name="researchName" required>
    Start datum: <input type="text" name="researchStart" id="datepickerB">
    Eind datum: <input type="text" name="reasearchEnd" id="datepickerE">
    <script>
      $( "#datepickerB" ).datepicker();
      $( "#datepickerE" ).datepicker();
    </script>
  <!-- </div> -->
  <hr>
  <!-- <div class="column large-12"> -->
    <label for="share">Onderzoekers toevoegen</label>
    <input type="text" name="share" id="searchInput" onkeyup="showResult(this.value, 1, 'livesearch')" autocomplete='off'>
    <table>
      <thead>
          <tr>
            <th>Naam</th>
            <th>Tussenvoegsel</th>
            <th>Achternaam</th>
            <th>Selecteer</th>
          </tr>
        </thead>
        <tbody id="livesearch">
         <!-- Live reloaded content -->
         <?php
         db_open();
            $stmt = db_query("SELECT voornaam, tussenvoegsel, achternaam FROM gebruiker WHERE gebruikersnaam = '" . $_SESSION['username'] . "'");
            $pin = db_fetchAssoc($stmt);
            echo "<tr>
                  <td>" . $pin['voornaam'] . "</td>
                  <td>" . $pin['tussenvoegsel'] . "</td>
                  <td>" . $pin['achternaam'] . "</td>
                  <td><input type=\"checkbox\" name=\"select" . $i . "\" value=" . $_SESSION['username'] . " onchange=\"managePin('" . $_SESSION['username'] . "')\" checked disabled=\"\"></td>
                </tr>";
            echo "<script>managePin('" . $_SESSION['username'] . "');</script>";
         db_close();
         ?>
      </tbody>
    </table>
    <input type="submit" name="submit" value="Opslaan" class="button">
  </form>
</div>
</div>
</div>