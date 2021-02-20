# Arduino-Schneider-Power-Logic-PM5350

Share a project to record the Schneider-Power-Logic-PM5350 data to database (MySql).

## Video
All video in indonesian language.
#### Video 01 : Communication and wiring
[![VIDEO01](http://img.youtube.com/vi/SQy2AJfB5v8/0.jpg)](https://youtu.be/SQy2AJfB5v8)
> code in video01 can be found in folder code_arduino_video01 
> 
#### Video 02 : Application Demo
[![VIDEO01](http://img.youtube.com/vi/AGjSCudvQ30/0.jpg)](https://youtu.be/AGjSCudvQ30)

#### Video 03 : Installation guide
[![VIDEO01](http://img.youtube.com/vi/jyJJ7BVtf-0/0.jpg)](https://youtu.be/jyJJ7BVtf-0)

## Hardware required
- Schneider-Power-Logic-PM5350
- Server (PHP & MySQL)
- Arduino UNO
- Ethernet shield for arduino UNO
- Converter RS484 to UART

## Communication
- Schneider-Power-Logic-PM5350 to arduino using modbus RTU (via RS485)
- Arduino to server using http request (via ethernet cable)
> Check video 01 for detail   

## Wiring
![wiring](wiring.png)
>  Check video 01 & 02 for detail   


## License and credits
My code under MIT license, other libraries follow their own license.
- SimpleModbusMaster.h https://github.com/angeloc/simplemodbusng
- jQuery.js https://jquery.org/license/
- Bootstrap https://getbootstrap.com/
- etc...
