#pragma once

#include <Arduino.h>
#include "HID.h"
#include "HID-Settings.h"
#include "LightSensorAPI.h"

class LightSensor_ : public LightSensorAPI
{
public:
    LightSensor_(void);

protected: 
    virtual inline void SendReport(void* data, int length) override;
};
extern LightSensor_ LightSensor;