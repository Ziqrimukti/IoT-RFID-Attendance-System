/*
 * ============================================================
 * Project      : IoT RFID Attendance System
 * Microcontroller : ESP8266 NodeMCU
 * 
 * Description:
 * This firmware is developed for an IoT-based attendance system
 * using ESP8266, RFID RC522, and LCD I2C.
 * 
 * Features:
 * - RFID card registration
 * - RFID card verification
 * - Attendance check-in and check-out system
 * - WiFi communication with PHP server
 * - MySQL database integration
 * - LCD status display
 * - Buzzer notification feedback
 * 
 * Hardware:
 * - ESP8266 NodeMCU
 * - RC522 RFID Module
 * - LCD I2C 16x2
 * - Buzzer
 * 
 * Software:
 * - Arduino IDE
 * - ESP8266 Arduino Core
 * - PHP & MySQL Backend
 * 
 * Author      : Ziqri
 * Program     : Teknologi Rekayasa Otomasi
 * Year        : 2026
 *
 * ============================================================
 */

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN 15   // D8
#define RST_PIN 4   // D2
#define buzzer 16

MFRC522 rfid(SS_PIN, RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2);

const char* ssid = "Redmi13c";
const char* password = "12345678";

String serverName =
"http://10.63.108.99/ziqrimukti/proses_rfid.php";

void standbyLCD()
{
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("SILAHKAN TAP");
  lcd.setCursor(5, 1);
  lcd.print("KARTU");
}

void setup()
{
  Serial.begin(115200);
  pinMode (buzzer, OUTPUT);

  SPI.begin();
  rfid.PCD_Init();
  Wire.begin(4, 5);   // SDA = D2, SCL = D1

  lcd.init();
  lcd.backlight();
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Menyambungkan");
  lcd.setCursor(0, 1);
  lcd.print("WIFI");

  Serial.println();
  Serial.println("Connecting WiFi...");

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("WiFi Connected");
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("TERSAMBUNG");
  lcd.setCursor(0, 1);
  lcd.print("WIFI");
  Serial.println(WiFi.localIP());
  standbyLCD();
}

void loop()
{
  if (!rfid.PICC_IsNewCardPresent())
  {
    return;
  }

  if (!rfid.PICC_ReadCardSerial())
  {
    return;
  }

  String uid = "";

  for (byte i = 0; i < rfid.uid.size; i++)
  {
    if (rfid.uid.uidByte[i] < 0x10)
    {
      uid += "0";
    }

    uid += String(rfid.uid.uidByte[i], HEX);
  }

  uid.toUpperCase();

  Serial.print("UID: ");
  Serial.println(uid);

  if (WiFi.status() == WL_CONNECTED)
  {
    WiFiClient client;
    HTTPClient http;

    http.begin(client, serverName);

    http.addHeader(
      "Content-Type",
      "application/x-www-form-urlencoded"
    );

    String postData = "uid=" + uid;

    int httpCode = http.POST(postData);

    Serial.print("HTTP Code: ");
    Serial.println(httpCode);

    if(httpCode > 0)
    {
      String response = http.getString();
      response.trim();

      if(response == "MASUK")
      {
        Serial.println("ABSEN MASUK BERHASIL");
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("ABSEN MASUK");
        lcd.setCursor(0, 1);
        lcd.print("BERHASIL");
        digitalWrite(buzzer, HIGH);
        delay(100);
        digitalWrite(buzzer, LOW);
        delay(50);
        digitalWrite(buzzer, HIGH);
        delay(100);
        digitalWrite(buzzer, LOW);
        delay(1500);
        standbyLCD();
      }
      else if(response == "PULANG")
      {
        Serial.println("ABSEN PULANG BERHASIL");
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("ABSEN PULANG");
        lcd.setCursor(0, 1);
        lcd.print("BERHASIL");
        digitalWrite(buzzer, HIGH);
        delay(100);
        digitalWrite(buzzer, LOW);
        delay(50);
        digitalWrite(buzzer, HIGH);
        delay(100);
        digitalWrite(buzzer, LOW);
        delay(1500);
        standbyLCD();
      }
      else if(response == "SUDAH_ABSEN")
      {
        Serial.println("ANDA SUDAH ABSEN HARI INI");
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("SUDAH ABSEN");
        delay(1500);
        lcd.setCursor(0, 1);
        lcd.print("HARI INI");
        delay(1500);
        standbyLCD();
      }
      else if(response == "BELUM_TERDAFTAR")
      {
        Serial.println("KARTU TIDAK TERDAFTAR");
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("KARTU TIDAK");
        lcd.setCursor(0, 1);
        lcd.print("TERDAFTAR");
        delay(1500);
        standbyLCD();
      }
      else
      {
        Serial.print("SERVER: ");
        Serial.println(response);
      }
    }
    else
    {
      Serial.println("GAGAL KONEK KE SERVER");
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("GAGAL KONEK");
      lcd.setCursor(0, 1);
      lcd.print("KE SERVER");
      delay(1500);
      standbyLCD();
    }

    http.end();
  }

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();

  delay(2000);
}

