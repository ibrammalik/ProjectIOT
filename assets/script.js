const chart = new Chart(
  document.getElementById("tempHumidityChart").getContext("2d"),
  {
    type: "line",
    data: {
      labels: [],
      datasets: [
        {
          label: "Temperature (°C)",
          data: [],
          borderColor: "rgba(255, 99, 132, 1)",
          backgroundColor: "rgba(255, 99, 132, 0.2)",
          borderWidth: 2,
          fill: true,
        },
        {
          label: "Humidity (%)",
          data: [],
          borderColor: "rgba(54, 162, 235, 1)",
          backgroundColor: "rgba(54, 162, 235, 0.2)",
          borderWidth: 2,
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: true,
          position: "top",
        },
        tooltip: {
          enabled: true,
        },
      },
      scales: {
        x: {
          title: {
            display: true,
            text: "Time",
          },
        },
        y: {
          title: {
            display: true,
            text: "Value",
          },
          beginAtZero: true,
        },
      },
    },
  }
);

function handleTemperature(temperature) {
  document.getElementById("temp-value").innerHTML = temperature;

  const low = document.querySelector(".low1");
  const medium = document.querySelector(".medium1");
  const high = document.querySelector(".high1");
  const detail = document.querySelector("#ket-temp");

  if (temperature > 29 && temperature < 30) {
    detail.innerHTML = "Rendah (Beep 1x)";
    document.getElementById("beep1").play();

    low.style.backgroundColor = "#4caf50";
    medium.style.backgroundColor = "white";
    high.style.backgroundColor = "white";
  } else if (temperature >= 30 && temperature <= 31) {
    detail.innerHTML = "Normal (Beep 2x)";
    document.getElementById("beep2").play();

    low.style.backgroundColor = "#4caf50";
    medium.style.backgroundColor = "#ffc107";
    high.style.backgroundColor = "white";
  } else if (temperature > 31) {
    detail.innerHTML = "Tinggi (Beep 3x)";
    document.getElementById("beep3").play();

    low.style.backgroundColor = "#4caf50";
    medium.style.backgroundColor = "#ffc107";
    high.style.backgroundColor = "#f44336";
  } else {
    detail.innerHTML = "Rendah (Tidak Beep)";

    low.style.backgroundColor = "white";
    medium.style.backgroundColor = "white";
    high.style.backgroundColor = "white";
  }
}

function handleHumidity(humidity) {
  document.getElementById("humd-value").innerHTML = humidity;

  const low = document.querySelector(".low2");
  const medium = document.querySelector(".medium2");
  const high = document.querySelector(".high2");
  const detail = document.querySelector("#ket-humid");

  if (humidity >= 30 && humidity < 60) {
    detail.innerHTML = "Kering/Aman";

    low.style.backgroundColor = "#4caf50";
    medium.style.backgroundColor = "white";
    high.style.backgroundColor = "white";
  } else if (humidity >= 60 && humidity < 70) {
    detail.innerHTML = "Mulai Banyak Uap Air/Normal (Beep 1x)";
    document.getElementById("beep1").play();

    low.style.backgroundColor = "#4caf50";
    medium.style.backgroundColor = "#ffc107";
    high.style.backgroundColor = "white";
  } else if (humidity >= 70) {
    detail.innerHTML = "Banyak Uap Air (Beep 3x)";
    document.getElementById("beep3").play();

    low.style.backgroundColor = "#4caf50";
    medium.style.backgroundColor = "#ffc107";
    high.style.backgroundColor = "#f44336";
  } else {
    low.style.backgroundColor = "white";
    medium.style.backgroundColor = "white";
    high.style.backgroundColor = "white";
  }
}

function turnLedUiOn(lampId, color = "") {
  document.getElementById(`lampu${lampId}`).checked = true;
  document.getElementById(`status-lampu${lampId}`).innerHTML = "On";
  document.getElementById(
    `gambar-lampu${lampId}`
  ).className = `ui-card--icon icon-lightbulb-on`;
}

function turnLedUiOff(lampId) {
  document.getElementById(`lampu${lampId}`).checked = false;
  document.getElementById(`status-lampu${lampId}`).innerHTML = "Off";
  document.getElementById(`gambar-lampu${lampId}`).className =
    "ui-card--icon icon-lightbulb";
}

const updateChart = (sensorsData) => {
  sensorsDataReverse = sensorsData.reverse();
  const sensorsDataChart = {
    temperatures: sensorsDataReverse.map((sensor) => sensor.temperature),
    humidities: sensorsDataReverse.map((sensor) => sensor.humidity),
    timestamps: sensorsDataReverse.map(
      (sensor) => sensor.timestamp.split(" ")[1]
    ),
  };
  chart.data.labels = sensorsDataChart.timestamps;
  chart.data.datasets[0].data = sensorsDataChart.temperatures;
  chart.data.datasets[1].data = sensorsDataChart.humidities;
  chart.update();
};

const updateTable = (sensorsData) => {
  const tableBody = document.getElementById("table-body");
  const tableRows = sensorsData
    .reverse()
    .map((data, index) => {
      return `
        <tr>
          <td>${data["timestamp"]}</td>
          <td>${data["temperature"]}</td>
          <td>${data["humidity"]}</td>
        </tr>`;
    })
    .join("\n");
  tableBody.innerHTML = tableRows;
};

const controlLED = async () => {
  const payload = {
    LED1: document.getElementById("lampu1").checked ? "on" : "off",
    LED2: document.getElementById("lampu2").checked ? "on" : "off",
    LED3: document.getElementById("lampu3").checked ? "on" : "off",
  };

  for (let [key, value] of Object.entries(payload)) {
    const lampId = key.replace("LED", "");
    if (value == "on") turnLedUiOn(lampId);
    if (value == "off") turnLedUiOff(lampId);
  }

  try {
    const response = await fetch("publish.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });

    const result = await response.json();
    console.log(result);
  } catch (error) {
    console.error("Error:", error);
  }
};

const getData = async () => {
  const response = await fetch("subscribe.php");
  const data = await response.json();
  return data;
};

const updateUI = async () => {
  const { temperature, humidity, sensors_data } = await getData();
  handleTemperature(temperature);
  handleHumidity(humidity);
  updateChart(sensors_data);
  updateTable(sensors_data);
};

document.addEventListener("DOMContentLoaded", function () {
  const playButton = document.getElementById("playAudioButton");

  const modal = new bootstrap.Modal(document.getElementById("audioModal"));
  modal.show();

  playButton.addEventListener("click", () => {
    setInterval(() => {
      updateUI();
    }, 5000);
    updateUI();
    modal.hide();
  });
});
