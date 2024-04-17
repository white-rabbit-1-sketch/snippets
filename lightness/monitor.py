import ctypes
from ctypes import *
from ctypes.wintypes import *

MAX_MONITOR_COUNT = 5
MONITOR_DEFAULTTOPRIMARY = 0x00000001

dxva = ctypes.windll.Dxva2
user32 = ctypes.windll.User32

class POINT(Structure):
    _fields_ = [("x", c_int),("y", c_int)]
    
class LPPHYSICAL_MONITOR(Structure):
    _fields_ = [("hPhysicalMonitor", POINTER(HANDLE)), ("szPhysicalMonitorDescription", c_char)]    

class MonitorManager:
    monitor_list = (LPPHYSICAL_MONITOR * MAX_MONITOR_COUNT)()

    def __init__(self):
        h_monitor = user32.MonitorFromPoint(POINT(0, 0), MONITOR_DEFAULTTOPRIMARY)
        print ("Monitor: " + str(h_monitor))

        monitor_count = c_int(0)
        dxva.GetNumberOfPhysicalMonitorsFromHMONITOR(h_monitor, byref(monitor_count));
        print ("Monitor count: " + str(monitor_count))
        
        dxva.GetPhysicalMonitorsFromHMONITOR(h_monitor, monitor_count, self.monitor_list);
        
    def get_monitor(self, value):
        return Monitor(self.monitor_list[value].hPhysicalMonitor)
        
class Monitor:
    def __init__(self, handle):
        self.handle = handle
        
    def get_current_brightness(self):
        min_brightness, max_brightness, current_brightness = self.get_brightness()
        
        print ("Current brightness: " + str(current_brightness))
        
        return current_brightness
        
    def get_brightness(self):
        min_brightness = c_int(-1)
        max_brightness = c_int(-1)
        current_brightness = c_int(-1)

        dxva.GetMonitorBrightness(
            self.handle,
            byref(min_brightness),
            byref(current_brightness),
            byref(max_brightness)
        );
        
        return min_brightness.value, max_brightness.value, current_brightness.value
        
    def set_brightness(self, value):
        dxva.SetMonitorBrightness(self.handle, c_int(value));
        dxva.SaveCurrentMonitorSettings(self.handle);

