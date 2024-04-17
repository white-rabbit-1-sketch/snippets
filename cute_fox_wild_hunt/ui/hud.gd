extends Control

onready var lifebar = $LifeBar

func _ready() -> void:
	SignalBus.connect("health_changed", self, "_on_health_changed")
	
func _on_health_changed(health, max_health) -> void:
	lifebar.update_values(health, max_health)
