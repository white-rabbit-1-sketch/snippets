#pragma once

#include <Arduino.h>
#include "HID-Settings.h"

class LightSensorAPI{
public:
	inline LightSensorAPI(void);
	inline void begin(void);
	inline void end(void);
    
    inline void write(float data);
    
	inline void release(void);
	inline void releaseAll(void);

	virtual void SendReport(void* data, int length) = 0;
};

// Implementation is inline
#include "LightSensorAPI.hpp"