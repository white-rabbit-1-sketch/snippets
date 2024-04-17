extends Control

onready var bar = $Bar
onready var bar_hp_label = $Bar/HpLabel

func update_values(bar_value, bar_max_value) -> void:
	bar.max_value = bar_max_value
	bar.value = bar_value
	bar_hp_label.text = str(bar.value) + "/" + str(bar.max_value)
	
