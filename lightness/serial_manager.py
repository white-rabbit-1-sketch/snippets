import serial
import time

from monitor import MonitorManager
from brightness import *

COM_PORT = 'COM5'

COM_MODE = 9600
SERIAL_ITERATION_SLEEP_TIME = 2

class SerialManager:
    def __init__(self, com_port):
        self.com_port = serial.Serial(com_port, COM_MODE)
        self.monitor = MonitorManager().get_monitor(0)
        
    def loop(self):
        while True:
            data = self.format_data(self.com_port.readline())
            print(data)
            self.handle_data(data)
            time.sleep(SERIAL_ITERATION_SLEEP_TIME)
            
    def format_data(self, serial_data):
        return serial_data.decode("utf-8").replace('\r\n', '')
            
    def handle_data(self, data):
        curent_lux_size = int(float(data))
        
        actual_brightness = calc_brightness_by_lux(curent_lux_size)
        
        print("Lux: " + str(curent_lux_size))
        print("Brightness: " + str(actual_brightness))
        
        self.monitor.set_brightness(actual_brightness)
            
        
serial_manager = SerialManager(COM_PORT)
serial_manager.loop()









