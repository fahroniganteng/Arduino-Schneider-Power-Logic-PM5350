#include "SimpleModbusMaster.h"
#include <WiFi.h>
#include <WiFiClient.h>
#include <WebServer.h>
#include <ESPmDNS.h>


/**
 * CODE RS485
 * --------------------------------------
 */
#define baud 19200
#define timeout 1000
#define polling 200 // the scan rate
#define retry_count 10 

// used to toggle the receive/transmit pin on the driver
#define TxEnablePin 0 

enum
{
  PACKET1,
  //PACKET2,
  // leave this last entry
  TOTAL_NO_OF_PACKETS
};

// Create an array of Packets for modbus_update()
Packet packets[TOTAL_NO_OF_PACKETS];
packetPointer packet1 = &packets[PACKET1];
unsigned int volt[2];
unsigned long timer;
float tegangan = 0;
float f_2uint_float(unsigned int uint1, unsigned int uint2) {    // reconstruct the float from 2 unsigned integers
  union f_2uint {
    float f;
    uint16_t i[2];
  };
  union f_2uint f_number;
  f_number.i[0] = uint1;
  f_number.i[1] = uint2;
  return f_number.f;
}

/**
 * CODE WEB
 * ---------------------------------
 */

const char *ssid = "fahroni";
const char *password = "ganteng_";
WebServer server(80);

void handleRoot() {
  char temp[400];
  snprintf(temp, 400,
    "<html>\
      <head>\
        <meta http-equiv='refresh' content='1'/>\
        <title>ESP32 Demo</title>\
        <style>\
          body { background-color: #cccccc; font-family: Arial, Helvetica, Sans-Serif; Color: #000088; }\
        </style>\
      </head>\
      <body>\
        <h1>Tegangan : %.2f</h1>\
      </body>\
    </html>",
   tegangan);
  server.send(200, "text/html", temp);
}

void setup(){
  Serial.begin(115200);
  
  // CODE RS485
  //--------------------------------
  // read 3 registers starting at address 0
  packet1->id = 1;
  packet1->function = READ_HOLDING_REGISTERS;
  packet1->address = 3027;
  packet1->no_of_registers = 2;
  packet1->register_array = volt;
  
  modbus_configure(baud, timeout, polling, retry_count, TxEnablePin, packets, TOTAL_NO_OF_PACKETS);
  
  pinMode(LED_BUILTIN, OUTPUT);
  timer = millis();

  // CODE WEB
  // ------------------------------------------
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.println("");

  // Wait for connection
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.print("Connected to ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());

  if (MDNS.begin("esp32")) {
    Serial.println("MDNS responder started");
  }

  server.on("/", handleRoot);
  server.begin();
  Serial.println("HTTP server started");
}

void loop(){
  unsigned int connection_status = modbus_update(packets);
  if (connection_status != TOTAL_NO_OF_PACKETS)
    digitalWrite(LED_BUILTIN, HIGH);
  else
    digitalWrite(LED_BUILTIN, LOW);
    
  long newTimer = millis();
  if(newTimer -  timer >= 1000){
    Serial.println();
    Serial.print("VOLTAGE : ");
    tegangan = f_2uint_float(volt[1],volt[0]);
    Serial.println(tegangan);
    timer = newTimer;
  }

  // web handle
  server.handleClient();
}
