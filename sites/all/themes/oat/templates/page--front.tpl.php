<div class="splash">
  <!-- Login -->
  <?php if(!user_is_logged_in()) : ?>
      <?php if ($page['content']['user_login']): ?>
          <?php echo render($page['content']['user_login']); ?>
      <?php endif; ?>
  <?php endif; ?>
  <!-- End login -->

  <div class="splash-container">
    <header role="banner">
      <div>
        <a href="http://www.cmore.no/" class="cmore-logo" target="_blank">
          <img src="<?=$theme_address?>images/splash/cmore-splash.png" alt="CMORE logo">
        </a>
        <a href="http://www.fotball.no/Landslag_og_toppfotball/Toppfotball/tippeligaen/" class="tippe-ligaen-logo" target="_blank">
          <img src="<?=$theme_address?>images/splash/tippe-ligaen-splash.png" alt="Tippe ligaen logo">
        </a>
      </div>

      <p class="logo">
        <img src="<?=$theme_address?>images/12man-white.png" alt="">
      </p>
      <p>
          Støtt laget ditt, få tilgang til eksklusivt innhold og delta i konkurranser gjennom hele sesongen!
      </p>
    </header>


    <div id="main" role="main">
      <?php
        $destination = 'registrer-deg';
        require_once('includes/team-list.inc');
      ?>
      <section class="user-content">
        <!-- <ul>
          <li>Se eksklusive intervjuer og innslag.</li>
          <li>Stille spørsmål til spillere, trenere eller C Mores ekspertpanel.</li>
          <li>Kommunisere med venner og andre supportere.</li>
          <li>Stemme, kommentere og tippe resultater. </li>
          <li>samle poeng for å vinne premier Og mye, mye mer. </li>
        </ul> -->
        <h3>På Den 12. Mann kan du:</h3>
        <p>- Få poeng ved å se på mål, høydepunkter, intervjuer og annet innhold.</p>
        <p class="yellow">- Stille spørsmål til spillere, trenere eller C More-eksperter - og ditt spørsmål kan være det som blir stilt på direkten!</p>
        <p>- Gi din tilbakemelding - hva mener du om ditt lags prestasjon!</p>
        <p class="yellow">- Se hvor mye støtte ditt lag får på Den 12. Mann og sammenligne med de andre klubbene i Tippeligaen.
        Og mye mye mer!</p>
        <p>- Registrer deg på Den 12. Mann nå!</p>
      </section>
    </div>

  </div>

  <!-- FOOTER -->
  <?php require_once('includes/footer.inc'); ?>

  <div class="splash-login-button">
    <a href="logg-inn" class="container login-button">Logg inn</a>
  </div>

</div>
      


<?php //echo render($page['content']); ?>