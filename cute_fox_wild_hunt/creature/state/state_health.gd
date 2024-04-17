extends AliveObjectState

class_name CreatureStateHealth

const HP_PER_STAMINA_POINT = 10

var previous_max_health: int
var previous_health: int

func enter_triggered(subject) -> bool:
	return true
	
func enter(subject, context: Dictionary = {}) -> void:
	subject.max_health = subject.stamina_point * HP_PER_STAMINA_POINT
	subject.health = subject.max_health
	subject.creature_lifebar.update_values(subject.health, subject.max_health)
	
	#SignalBus.emit_signal("health_changed", subject.health, subject.max_health)

func process(subject)-> void:
	var max_health = subject.stamina_point * HP_PER_STAMINA_POINT
	
	if not subject.max_health == max_health:
		subject.max_health = max_health
		
	if subject.health > subject.max_health:
		subject.health = subject.max_health
		
	if not previous_max_health == subject.max_health or not previous_health == subject.health:
		previous_max_health = subject.max_health
		previous_health = subject.health
		#SignalBus.emit_signal("health_changed", subject.health, subject.max_health)
		subject.creature_lifebar.update_values(subject.health, subject.max_health)	
	
static func get_state_name() -> String:
	return "context_health"
	
func get_type() -> int:
	return TypeEnum.CONTEXT
