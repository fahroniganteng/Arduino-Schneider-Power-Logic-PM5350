/*
 * SEND DATA PM5350 TO SERVER (with ETEHERNET SHIELD)
 * ***********************************************************************************
 * Code by : fahroni|ganteng
 * contact me : fahroniganteng@gmail.com
 * Date : feb 2021
 * License :  MIT
 * 
 * 
 * NOTE:
 * ```````````````````````````````````````````````````````````````
 * Make sure to change DEVICE_ID, IP and MAC address of each device
 * This code uses static IP, for DHCP check on ethernet/webClient example (in arduino IDE)
 */


IPAddress ip(10, 7, 35, 101);
IPAddress myDns(192,168,15,15);
IPAddress SERVER(10,7,35,100); // SERVER IP OR DOMAIN ==> check example Ethernet/webClient (Arduino IDE)
String DEVICE_ID   = "METER01";
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };




EthernetClient client;

void ethernetInit(){
  Ethernet.begin(mac, ip, myDns);
  if (Ethernet.linkStatus() == LinkOFF) {
    Serial.println("Ethernet cable is not connected.");
  }
  Serial.print("IP : ");
  Serial.println(Ethernet.localIP());
}
void sendToServer(){
  String POST_DATA = "id="+DEVICE_ID;
  POST_DATA += "&dt=[";
  for(int i=0; i<35; i++){
    POST_DATA +=  String(meterData[i]) + ",";
  }
  POST_DATA[POST_DATA.length() - 1] = ']';
  PRINTLN(POST_DATA);
  PRINT("connecting to ");
  PRINT(SERVER);
  PRINTLN("...");
  if(client.connect(SERVER, 80)) {
    PRINTLN("Connected to server");
    client.println("POST /meter/insert.php HTTP/1.1");
    client.println("Host: 10.7.35.100"); // <== MAKE SURE SET THIS SERVER IP OR DOMAIN WEB
    client.println("Authorization: Basic ZmFocm9uaTpnYW50ZW5n"); //this is basic http auth. (for security) ==> check on PHP code to generate, or you can find online generator on internet.
    client.println("User-Agent: Arduino/1.0");
    client.println("Connection: close");
    client.println("Content-Type: application/x-www-form-urlencoded;");
    client.print("Content-Length: ");
    client.println(POST_DATA.length());
    client.println();
    client.println(POST_DATA);
    
    while(client.connected()) {
      if(client.available()){
        char c = client.read();
        Serial.print(c);
      }
    }
    Serial.println();
    client.stop();
    PRINTLN();
    PRINTLN("disconnected");
    PRINTLN();
  } else {
    Serial.println("\nFail Connect to web server...");
  }
}
