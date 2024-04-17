#pragma once

LightSensorAPI::LightSensorAPI(void)
{
	// Empty
}

void LightSensorAPI::begin(void){
	// release all buttons
	end();
}

void LightSensorAPI::end(void){
	
}

void LightSensorAPI::release(void){
	begin();
}

void LightSensorAPI::releaseAll(void){
	begin();
}

void LightSensorAPI::write(float data){
	SendReport(&data, sizeof(data));
}