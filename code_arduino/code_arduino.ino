/*
 * ARDUINO - PM5350 (MODBUS RTU)
 * ***********************************************************************************
 * Code by : fahroni|ganteng
 * contact me : fahroniganteng@gmail.com
 * Date : feb 2021
 * License :  MIT
 * 
 */



// FOR DEBUGING ONLY
// ```````````````````````
// NOTE : Pins 0 and 1 also used for modbus communication ==> enable (DEBUG) only when needed
#define DEBUG 0 // set 1 to enable debuging
#if DEBUG
  #define PRINT(s)    { Serial.print(s); }
  #define PRINTLN(s)  { Serial.println(s); }
#else
  #define PRINT(s)
  #define PRINTLN(s)
#endif

// fn get size of int array
// ````````````````````````````
#define ARRSIZE(x)   (sizeof(x) / sizeof(x[0]))

#include <SPI.h>
#include <Ethernet.h>
#include "ronnModbus.h"
#include "ronnEthernet.h"

unsigned long timer;

void setup(){
  modbusInit();
  ethernetInit();
  delay(1000);
}

void loop(){
  connection_status = modbus_update(packets);
  // Send every 5000ms
  if(millis() - timer >= 5000 || timer > millis()){
    timer = millis();
    modbusData();   // update data modbus convert to float
    sendToServer(); // send to server over HTTP request (POST)
  }
}
