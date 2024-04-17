extends AliveObjectState

class_name CreatureStateBump

const BUMP_SCALE = 0.3

var previous_creature_scale: float

func can_enter(subject) -> bool:
	return not state_machine.is_active("die")
		
func enter(subject, context: Dictionary = {}) -> void:
	previous_creature_scale = subject.scale.y
	subject.scale.y *= BUMP_SCALE

func leave(subject) -> void:
	subject.scale.y = previous_creature_scale
	
static func get_state_name() -> String:
	return "bump"
