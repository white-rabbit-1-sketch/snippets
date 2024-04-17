#include <Wire.h>
#include <BH1750.h>
#include <i2cdetect.h>

#include "HID-Project.h"
#include "LightSensor.h"

const int pinLed = LED_BUILTIN;
const int pinButton = 2;

BH1750 lightMeter;

void setup() {
  pinMode(pinLed, OUTPUT);
  pinMode(pinButton, INPUT_PULLUP);

  LightSensor.begin();
  Wire.begin();
  lightMeter.begin();
}
 
void loop() {
  
  if (!digitalRead(pinButton)) {
    digitalWrite(pinLed, HIGH);

    float lux = lightMeter.readLightLevel();

    LightSensor.write(lux);


    digitalWrite(pinLed, LOW);
  }
}
