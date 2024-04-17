BASELINE_BRIGTHNESS = 50
OFFSET_MODIFICATOR = 0

def calc_brightness_by_lux(lux_size):
        brightness = (BASELINE_BRIGTHNESS * ((100 - get_lux_offset(lux_size)) / 100))
        if brightness < 0: brightness = 0
        
        return int(brightness)
        
def get_lux_offset(lux_size):
    offset = 40
        
    if lux_size > 5: offset = 40
    if lux_size > 50: offset = 60
    if lux_size > 100: offset = 80
    if lux_size > 300: offset = 100
    if lux_size > 700: offset = 120
    if lux_size > 1500: offset = 150
        
    return offset + OFFSET_MODIFICATOR