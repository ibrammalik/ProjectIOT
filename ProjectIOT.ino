#include <LiquidCrystal_I2C.h>
#include <Wire.h>
#include <DHT.h>
#include <EEPROM.h>  
#include <ESP8266WiFi.h>  
#include <PubSubClient.h>  
#include <ArduinoJson.h>

#define lampu1 D6   
#define lampu2 D7  
#define lampu3 D8   
#define lampu4 D0
#define lampu5 D3  
#define lampu6 D4
#define DHTPIN D5
#define DHTTYPE DHT11    

LiquidCrystal_I2C lcd(0x27, 16, 2);
DHT dht(DHTPIN, DHTTYPE);
WiFiClient espClient;  
PubSubClient client(espClient);  

// Konfigurasi Wifi
const char* wifi_ssid = "Mi 10T Pro";  
const char* wifi_password = "11111111";

// Konfigurasi MQTT
const char* mqtt_server = "x2.revolusi-it.com"; 
const char* mqtt_username = "usm";
const char* mqtt_password = "usmjaya001";
const char* client_id = "client-G.231.22.0023_NodeMCU1";

// Membuat Variabel
float humidity = 0;
float temperature = 0;
float prevHumidity = 0;
float prevTemperature = 0;
String messages = "";
unsigned long previousMillis = 0;  
const long interval = 5000;
bool showNotification = false;
unsigned long notificationStartTime = 0;
static unsigned long lastAnimationTime = 0;
static int animationPosition = 0;

// Icon Humidity
byte Water[] = {
  B00100,
  B01110,
  B01110,
  B11111,
  B11101,
  B11101,
  B11111,
  B01110
};

// Icon Temperature
byte Temperature[] = {
  B00100,
  B01010,
  B01010,
  B01010,
  B10001,
  B10001,
  B11011,
  B01110
};

// Icon Lampu
byte Lamp[] = {
  B01110,
  B10001,
  B10001,
  B10001,
  B01010,
  B01110,
  B01110,
  B00100
};

// MQTT CONTROLLER / CALLBACK
void callbackMQTT(char* topic, byte* payload, unsigned int length) {  
  char message[length + 1];
  strncpy(message, (char*)payload, length);
  message[length] = '\0';

  Serial.print("Pesan diterima: ");
  Serial.println(message);

  StaticJsonDocument<256> doc;
  DeserializationError error = deserializeJson(doc, message);

  if (error) {
    Serial.println("Gagal mem-parsing JSON");
    return;
  }

 // Static variables to track previous states
  static bool prevStateLampu1 = LOW;
  static bool prevStateLampu2 = LOW;
  static bool prevStateLampu3 = LOW;

  // Lampu D6
  if (doc.containsKey("LED1")) {
    bool currentStateLampu1 = (doc["LED1"] == "on");
    if (currentStateLampu1 != prevStateLampu1) {
      digitalWrite(lampu1, currentStateLampu1 ? HIGH : LOW);
      lcdMessage(String("Lampu D6 : ") + (currentStateLampu1 ? "ON" : "OFF"), true);
      prevStateLampu1 = currentStateLampu1;
    }
  }

  // Lampu D7
  if (doc.containsKey("LED2")) {
    bool currentStateLampu2 = (doc["LED2"] == "on");
    if (currentStateLampu2 != prevStateLampu2) {
      digitalWrite(lampu2, currentStateLampu2 ? HIGH : LOW);
      lcdMessage(String("Lampu D7 : ") + (currentStateLampu2 ? "ON" : "OFF"), true);
      prevStateLampu2 = currentStateLampu2;
    }
  }

  // Lampu D8
  if (doc.containsKey("LED3")) {
    bool currentStateLampu3 = (doc["LED3"] == "on");
    if (currentStateLampu3 != prevStateLampu3) {
      digitalWrite(lampu3, currentStateLampu3 ? HIGH : LOW);
      lcdMessage(String("Lampu D8 : ") + (currentStateLampu3 ? "ON" : "OFF"), true);
      prevStateLampu3 = currentStateLampu3;
    }
  }
}

// Connect MQTT
void reconnect() {  
  while (!client.connected()) {  
    lcdMessage("Connecting MQTT", false);
    Serial.print("Menghubungkan ke MQTT Server -> ");  
    Serial.println(mqtt_server);  
    // Connect MQTT
    if (client.connect(client_id, mqtt_username, mqtt_password)) {  
      Serial.println("Terhubung!");  
      lcdMessage("MQTT Connected ", false);
      // Subscribe
      client.subscribe("G.231.22.0023/control");
    } else {  
      Serial.print("Gagal, rc=");  
      Serial.println(client.state());
    }  
  }  
}

// Connect Wifi
void connectWifi() {  
  WiFi.begin(wifi_ssid, wifi_password);  
  while (WiFi.status() != WL_CONNECTED) {  
    lcdMessage("Connecting WiFi", false); 
    Serial.print(".");  
  }  
  Serial.println("\nWiFi terhubung"); 
  lcdMessage("WiFi Connected", false); 
}

// Menampilkan Temperature & Humidity pada LCD I2C
void displayTempAndHumid(bool forceUpdate = false) {
  if (forceUpdate || temperature != prevTemperature || humidity != prevHumidity) {
    lcd.setCursor(0,0);
    lcd.write(byte(1));
    lcd.setCursor(1,0);
    lcd.print(" : " + String(temperature) + " C     ");
    lcd.setCursor(0,1);
    lcd.write(byte(0));
    lcd.setCursor(1,1);
    lcd.print(" : "+ String(humidity) + " %     ");
    prevTemperature = temperature;
    prevHumidity = humidity;
  }
}

// Publish Temperature dan Humidity
void publishTempAndHumid(){
  client.publish("G.231.22.0023/temperature", String(temperature).c_str(), true);
  client.publish("G.231.22.0023/humidity", String(humidity).c_str(), true);
}

// Menampilkan Pesan Control LED
void lcdMessage(String message, bool led) {
  showNotification = true;
  notificationStartTime = millis();
  lcd.setCursor(0, 0);
  if (led) {
    lcd.clear();
    lcd.write(byte(2));
    lcd.setCursor(2, 0);
  }
  lcd.print(message);
  animationPosition = 0;
  lcd.setCursor(0, 1);
  lcd.print("                ");
}

// Animasi Loading
void animateRow() {
  if (showNotification && millis() - lastAnimationTime >= 92) {
    lcd.setCursor(animationPosition, 1);
    lcd.print(".");
    animationPosition++;
    if (animationPosition >= 16) {
      animationPosition = 0;
      lcd.setCursor(0, 1);
      lcd.print("                ");
    }
    lastAnimationTime = millis();
  }
}

// Menyalakan LED berdasarkan suhu
void ledIndicator() {
  if (temperature > 29 && temperature < 30) {
    digitalWrite(lampu4, HIGH);
    digitalWrite(lampu5, LOW);
    digitalWrite(lampu6, LOW);
  } 
  else if (temperature >= 30 && temperature <= 31) {
    digitalWrite(lampu4, HIGH);
    digitalWrite(lampu5, HIGH);
    digitalWrite(lampu6, LOW);
  } 
  else if (temperature > 31) {
    digitalWrite(lampu4, HIGH);
    digitalWrite(lampu5, HIGH);
    digitalWrite(lampu6, HIGH);
  } else {
    digitalWrite(lampu4, LOW);
    digitalWrite(lampu5, LOW);
    digitalWrite(lampu6, LOW);
  }
} 

// Fungsi Setup
void setup() {  
  Serial.begin(9600);  
  client.setServer(mqtt_server, 1883);  
  client.setCallback(callbackMQTT);  
  pinMode(lampu1, OUTPUT);  
  pinMode(lampu2, OUTPUT);  
  pinMode(lampu3, OUTPUT);  
  pinMode(lampu4, OUTPUT);  
  pinMode(lampu5, OUTPUT);  
  pinMode(lampu6, OUTPUT);
  Wire.begin();
  lcd.begin(16,2);
  lcd.backlight();
  dht.begin();
  lcd.createChar(0, Water);
  lcd.createChar(1, Temperature);
  lcd.createChar(2, Lamp);
}

// Fungsi Loop
void loop() { 
  if (WiFi.status() != WL_CONNECTED) connectWifi();
  if (!client.connected()) reconnect();
  client.loop();
  
  // Interval 5 Detik
  unsigned long currentMillis = millis();
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;

    humidity = dht.readHumidity();
    temperature = dht.readTemperature();
    ledIndicator();
    publishTempAndHumid();
    if(!showNotification) displayTempAndHumid();
  }

  // Animasi 3 Detik
  if (showNotification) {
    animateRow();
    if (millis() - notificationStartTime >= 3000) {
      showNotification = false;
      lcd.clear();
      displayTempAndHumid(true);
    }
  }
}

