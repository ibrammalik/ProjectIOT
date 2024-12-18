<!DOCTYPE html>
<html lang="en">

<head>
  <title>Praktikum IOT</title>
  <link rel="stylesheet" href="./assets/styles.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/chart.min.js" type="text/javascript"></script>
</head>

<body>
  <div class="container-sm" style="max-width: 700px;">
    <div class="ui--screen-section">
      <div class="ui--section-title">
        <span>Sensors</span>
      </div>
      <div class="ui--horizontal-scroll">
        <div class="ui--card ui--card-square">
          <span class="ui-card--title">Temperature</span>
          <div class="value-unit-container">
            <span class="ui-card--value" id="temp-value">18</span>
            <span class="suffix">°C</span>
          </div>
        </div>
        <div class="ui--card ui--card-square">
          <span class="ui-card--title">Humidity</span>
          <div class="value-unit-container">
            <span class="ui-card--value" id="humd-value">46</span>
            <span class="suffix">%</span>
          </div>
        </div>
      </div>
    </div>
    <div class="ui--screen-section">
      <div class="ui--section-title">
        <span>Temperature Level</span>
        <span style="font-size: 16px; padding-right: 1rem;" id="ket-temp">Temperature Level</span>
      </div>
      <div class="ui--horizontal-scroll">
        <div class="level-bar">
          <div class="level low1">Low</div>
          <div class="level medium1">Normal</div>
          <div class="level high1">High</div>
        </div>
      </div>
    </div>
    <div class="ui--screen-section">
      <div class="ui--section-title">
        <span>Humidity Level</span>
        <span style="font-size: 16px; padding-right: 1rem;" id="ket-humid">Humidity Level</span>
      </div>
      <div class="ui--horizontal-scroll">
        <div class="level-bar">
          <div class="level low2">Dry</div>
          <div class="level medium2">Normal</div>
          <div class="level high2">Humid</div>
        </div>
      </div>
    </div>
    <div class="ui--screen-section">
      <div class="ui--section-title">
        <span>Chart</span>
      </div>
      <div class="ui--horizontal-scroll">
        <div style="width: 100%; margin: auto;">
          <canvas id="tempHumidityChart"></canvas>
        </div>
      </div>
    </div>
    <div class="ui--screen-section">
      <div class="ui--section-title">
        <span>Data Log (20 Data Terakhir)</span>
      </div>
      <div class="ui--horizontal-scroll">
        <div style="width: 100%; margin: auto; text-align: center;">
          <table class="table table-bordered">
            <thead>
              <tr class="table-primary">
                <td>Recorded At</td>
                <td>Temperature (°C)</td>
                <td>Humidity (%)</td>
              </tr>
            </thead>
            <tbody id="table-body">
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="ui--screen-section">
      <div class="ui--section-title">
        <span>Devices</span>
      </div>
      <div class="ui--stack">
        <?php $leds = array("D6", "D7", "D8") ?>
        <?php foreach ($leds as $i => $led): ?>
          <div class="ui--card">
            <div class="ui-card--icon icon-lightbulb" id="gambar-lampu<?= $i + 1 ?>"></div>
            <div>
              <span class="ui-card--title">Lampu <?= $led ?></span>
              <span class="ui-card--subtitle" id="status-lampu<?= $i + 1 ?>">Off</span>
            </div>
            <div class="ui--toggle">
              <input type="checkbox" id="lampu<?= $i + 1 ?>" onchange="controlLED();" />
              <label for="lampu<?= $i + 1 ?>">Toggle</label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <audio id="beep1" src="./assets/beep1.mp3"></audio>
  <audio id="beep2" src="./assets/beep2.mp3"></audio>
  <audio id="beep3" src="./assets/beep3.mp3"></audio>
  <!-- Modal Bootstrap -->
  <div class="modal fade" id="audioModal" tabindex="-1" aria-labelledby="audioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="audioModalLabel">Konfirmasi Pemutaran Audio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Klik "Play" untuk mengijinkan pemutaran audio.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="playAudioButton" class="btn btn-primary">Play</button>
        </div>
      </div>
    </div>
  </div>
  <script src="./assets/script.js" type="text/javascript"></script>
</body>

</html>